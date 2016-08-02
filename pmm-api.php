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
                'franchise_area' => $location['city'],
                'address' => $location['address'],
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
     * 
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
                    'address' => isset($_REQUEST['address']) ? $_REQUEST['address'] : $client->address,
                    'email' => isset($_REQUEST['email']) ? $_REQUEST['address'] : $client->address,
                    'telno' => isset($_REQUEST['telno']) ? $_REQUEST['telno'] : $client->telno,
                    'mobileno' => isset($_REQUEST['mobileno']) ? $_REQUEST['mobileno'] : $client->mobileno,
                );
                $client->set_attributes($data);
                if ($client->update())
                    $result['AuthenticateResult'] = 1;
            }/* else {
              echo json_encode($result);
              create_json($client);
              die();
              } */
        }
        echo json_encode($result);
    }

    //reset unseen 
    if (isset($_REQUEST['url_id'])) {
        if ($project = get_post($_REQUEST['url_id'])) {
            update_post_meta($project->ID, '_unseen', 0);
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

function create_json($user) {
    $pmm_options = get_option('pmm_settings');
    $client = new WP_Client();
    $client->get_row('username', $user);
    $args = array(
        'posts_per_page' => -1,
        'offset' => 0,
        //'orderby' => 'menu_order, post_title', // post_date, rand
        //'order' => 'DESC',
        'meta_key' => $client->is_admin ? '' : '_client_id',
        'meta_value' => $client->is_admin ? '' : $client->id,
        'post_type' => 'client_project',
        'post_status' => 'publish',
    );
    $loop = new WP_Query($args);

    foreach ($loop->posts as $k => $project):
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
            'url_id' => (string) $project->ID,
            'unseen' => get_post_meta($project->ID, '_unseen', true),
            'progress' => $progress,
            'images' => array_reverse($images),
            'doc-titles' => $doc_titles,
            'doc-links' => $doc_links
        );
        //var_dump($project);
    endforeach;

    $result = array(
        'id' => '1',
        'jsonproc' => '2.0',
        'total' => (string) $loop->post_count,
        'results' => array(array(
                'userid' => (string) $client->id,
                'username' => $client->username,
                'role' => '1',
                'projects' => $projects
            ))
    );
    //$json_file = PMM_DIR . "cache/" . $client->username . ".json";
    //file_put_contents($json_file, json_encode(str_replace('\n', '\\n', $result)));
    echo json_encode($result);
}
