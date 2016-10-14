<?php

/**
 * API requests.
 *
 * @author   Amal Ranganath
 * @category API
 * @package  PMM/API
 * @version  1.0.0
 */
//find franchises near by given location
if ($require == 'wp-api/location') {

    if (isset($_REQUEST['lng']) && isset($_REQUEST['lat']) && isset($_REQUEST['radius'])) {

        $loc = new WPSL_Frontend();

        foreach ($loc->find_nearby_locations() as $location) {
            $franchisee_details[] = array(
                'contact_person' => $location['store'],
                'franchise_area' => $location['address'],
                'address' => $location['city'],
                'working_hours' => strip_tags(trim($location['hours'])),
                'telephone' => $location['phone']);
        }
        $pmm_options = get_option('pmm_settings');
        $result = array(
            'franchise_numbers' => $pmm_options['franchise_number'],
            'franchisee_details' => $franchisee_details
        );
        echo json_encode($result);
        die();
    }
} else {
    //Register devices
    if ($require == 'wp-api/RegisterDevices/apns.php') {
        ini_set('display_errors', 'on');
        //Load APNS class
        require_once('classes/class_APNS.php');
        // FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
        $args = (!empty($_GET)) ? $_GET : array('task' => $argv[1]);

        // CREATE APNS OBJECT, WITH DATABASE OBJECT AND ARGUMENTS
        $apns = new APNS($args, dirname(__FILE__ . 'dr_prod.pem'), dirname(__FILE__ . 'dr_dev.pem'));
        //echo '<pre>';var_dump($apns);
        //echo json_encode($apns);
    }

    if ($require == 'wp-api/appversion.php') {

        echo json_encode(array('version' => 2.4));
    }

    /**
     * User and franchisee login
     */
    if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {

        $user = array('username' => $_REQUEST['username'], 'password' => $_REQUEST['password']);

        $client = new WP_Client();
        $result['AuthenticateResult'] = 0;

        if ($client->login($user)) {
            $result['AuthenticateResult'] = 1;
            //get client user details
            if ($require == 'wp-api/getuserinfo.php') {
                $result['user_first'] = $client->firstname;
                $result['user_last'] = $client->lastname;
                $result['user_email'] = $client->email;
                $result['user_address'] = $client->address;
                $result['user_telno'] = $client->telno;
                $result['user_mobileno'] = $client->mobileno;
            } else if ($require == 'wp-api/updateuser.php') {
                //update client user
                $data = array(
                    'id' => $client->id,
                    'firstname' => isset($_REQUEST['firstname']) ? $_REQUEST['firstname'] : $client->firstname,
                    'lastname' => isset($_REQUEST['lastname']) ? $_REQUEST['lastname'] : $client->lastname,
                    'address' => isset($_REQUEST['address']) ? stripcslashes($_REQUEST['address']) : $client->address,
                    'email' => isset($_REQUEST['email']) ? $_REQUEST['email'] : $client->email,
                    'telno' => isset($_REQUEST['telno']) ? $_REQUEST['telno'] : $client->telno,
                    'mobileno' => isset($_REQUEST['mobileno']) ? $_REQUEST['mobileno'] : $client->mobileno,
                );
                $client->set_attributes($data);
                if ($client->update())
                    $result['AuthenticateResult'] = 1;
            }
            //check for franchisee
        }else if ($franchise = get_userdatabylogin($user['username'])) {
            if (wp_check_password($user['password'], $franchise->user_pass, $franchise->ID)) {
                $result['AuthenticateResult'] = 1;
                if ($require == 'wp-api/getuserinfo.php') {
                    $result['user_first'] = $franchise->first_name;
                    $result['user_last'] = $franchise->last_name;
                    $result['user_email'] = $franchise->user_email;
                    $result['user_address'] = get_user_meta($user_id, 'area', true);
                    $result['user_telno'] = get_user_meta($user_id, 'telephone', true);
                    $result['user_mobileno'] = $result['user_telno'];
                } else if ($require == 'wp-api/updateuser.php') {
                    update_user_meta($franchise->ID, 'first_name', $_REQUEST['firstname']);
                    update_user_meta($franchise->ID, 'address', $_REQUEST['address']);
                    update_user_meta($franchise->ID, 'telephone', $_REQUEST['telno']);
                }
            }
        }
        echo json_encode($result);
    }

    //reset unseen 
    if (isset($_REQUEST['url_id'])) {
        /* if ($project = get_post($_REQUEST['url_id'])) {
          update_post_meta($project->ID, '_unseen', 0);
          echo('{"AuthenticateResult":1}');
          } */
        $unseen = new WP_USER_UNSSEN();
        $row = $unseen->get_row('id', $_REQUEST['url_id']);
        if ($row) {
            $unseen->update_unseen($_REQUEST['url_id']);
            echo('{"AuthenticateResult":1}');
        } else {
            echo('{"AuthenticateResult":0}');
        }
    }

    //find projects assign to given client
    if (isset($_REQUEST['client']) OR strpos($require, 'wp-api/usernames/') !== false) {
        $user = explode('.', str_replace('wp-api/usernames/', '', $require));
        $user = isset($_REQUEST['client']) ? esc_sql($_REQUEST['client']) : $user[0];
        create_json($user);
        /* $json_file = PMM_DIR . "cache/" . $user . ".json";
          if (file_exists($json_file)) {
          echo file_get_contents($json_file);
          exit();
          } */
    }

    die();
}

function create_json($username) {
    $pmm_options = get_option('pmm_settings');
    $client = new WP_Client();
    $unseen = new WP_USER_UNSSEN();
    $args = array(
        'posts_per_page' => -1,
        'offset' => 0,
        //'orderby' => 'menu_order, post_title', // post_date, rand
        //'order' => 'DESC',
        'post_type' => 'client_project',
        'post_status' => 'publish',
    );
    if ($client->get_row('username', $username)) {
        if (!$client->is_admin) {
            $args['meta_key'] = '_client_id';
            $uid = $args['meta_value'] = $client->id;
        } else {
            $user_query = new WP_User_Query(array('role' => 'Administrator'));
            if (!empty($user_query->results)) {
                $admin_list = "";

                foreach ($user_query->results as $user) {
                    $admin_list .= ',' . $user->ID;
                }
            }
            $uid = $client->id;
            $args['author'] = trim($admin_list, ",");
        }
    } else if ($franchise = get_userdatabylogin($username)) {
        $uid = $args['author'] = $franchise->ID;
    }


    $loop = new WP_Query($args);

    foreach ($loop->posts as $k => $project):
        $condition = " post_id = $project->ID AND user_id = $uid ";
        $unseen_results = $unseen->get_single_row($condition);
        if ($unseen_results) {
            $url_id = $unseen_results->id;
            $unseen_count = $unseen_results->unseen;
        }

        //if ($k > 25 && $client->is_admin)break;
        $images = $doc_titles = $doc_links = array();
        //has milestones
        if ($milestone = get_post_meta($project->ID, 'milestone', true)) {
            for ($i = 0; $i < $milestone; $i++) :

                $videos = $imgs = $thumb_img = $thumb_vds = array();
                if ($gallery = get_field('milestone_' . $i . '_gallery', $project->ID)):
                    foreach ($gallery as $image):
                        $imgs[] = array('Url' => $image['url']);
                        $thumb_img[] = array('Thumbnail' => $image['sizes']['medium']);
                    endforeach;
                endif;
                if ($video_uploads = get_post_meta($project->ID, 'milestone_' . $i . '_video_uploads', true)):
                    for ($j = 0; $j < $video_uploads; $j++):
                        $video = get_field('milestone_' . $i . '_video_uploads_' . $j . '_add_video', $project->ID);
                        //$thumb = get_field('milestone_' . $i . '_video_uploads_' . $j . '_video_thumbnail', $project->ID);
                        $videos[] = array('Url' => $video['url']);
                        $thumb_vds[] = array('Thumbnail' => $pmm_options['video_thumb_url']);
                    endfor;
                endif;

                $images[] = array(
                    'Date' => get_field('milestone_' . $i . '_date', $project->ID),
                    'Description' => get_post_meta($project->ID, 'milestone_' . $i . '_description', true),
                    'Image-Url' => $imgs,
                    'Thumb-Image-Url' => $thumb_img,
                    'Video-Url' => $videos,
                    'Thumb-Video-Url' => $thumb_vds
                );

            endfor;
        }
        //has file uploads
        if ($file_uploads = get_post_meta($project->ID, 'file_uploads', true)) {
            for ($i = 0; $i < $file_uploads; $i++):
                $file = get_field('file_uploads_' . $i . '_file', $project->ID);
                $doc_links[]['link'] = $file['url'];
                $doc_titles[]['title'] = get_field('file_uploads_' . $i . '_file_title', $project->ID);
            endfor;
        }
        $progress = get_field('progress', $project->ID);
        $progress = ($progress == 7) ? 100 : $progress * 15;
        $projects[] = array(
            'name' => $project->post_title,
            'post_modified' => $project->post_modified,
            //'url_id' => (string) $project->ID,
            //'unseen' => get_post_meta($project->ID, '_unseen', true),
            'url_id' => (string) $url_id,
            'unseen' => (string) $unseen_count,
            'progress' => $progress,
            'images' => array_reverse($images),
            'doc-titles' => $doc_titles,
            'doc-links' => $doc_links,
                //'author' => $project->post_author
        );
        //var_dump($project);
    endforeach;

    $result = array(
        'id' => '1',
        'jsonproc' => '2.0',
        'total' => (string) $loop->post_count,
        'results' => array(array(
                'userid' => (string) $uid,
                'username' => $username,
                'role' => '1',
                'projects' => $projects
            ))
    );
    //$json_file = PMM_DIR . "cache/" . $client->username . ".json";
    //file_put_contents($json_file, json_encode(str_replace('\n', '\\n', $result)));
    echo json_encode($result);
}
