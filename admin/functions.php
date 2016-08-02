<?php
/**
 * Admin functions.
 *
 * @author   Amal Ranganath
 * @category Admin
 * @package  PMM/functions
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


require_once (PMM_DIR . 'models/wp-area.php');
require_once (PMM_DIR . 'models/wp-client.php');


if (!function_exists('pmm_admin_scripts')) {

    function pmm_admin_scripts() {
        $allowed = array('client', 'clients', 'reset-password', 'area-manager', 'pmm-control-panel');

        if (isset($_GET['page']) && in_array($_GET['page'], $allowed)) {
            wp_enqueue_media();
            wp_enqueue_style('pmm-admin-styles', plugins_url('../assets/css/admin.css', __FILE__));
            wp_enqueue_script('pmm-admin-scripts', plugins_url('../assets/js/admin.js', __FILE__), array('jquery'));
        }
    }

}
add_action('admin_enqueue_scripts', 'pmm_admin_scripts');

if (!function_exists('project_and_meadia_manager_menu')) {

    function project_and_meadia_manager_menu() {
        add_menu_page(__('Project Manager', 'wp-pmm'), __('Project Manager', 'wp-pmm'), 'project_manager', 'pmm-control-panel', 'pmm_control_panel', '', 21);
    }

}
add_action('admin_menu', 'project_and_meadia_manager_menu');

if (!function_exists('pmm_control_panel')) {

    function pmm_control_panel() {
        //Call redirect_to_page_by_user
    }

}

function redirect_to_page_by_user() {
    if (isset($_GET['page']) && $_GET['page'] == 'pmm-control-panel' && !is_super_admin()) {
        wp_redirect(admin_url('/admin.php?page=clients', 'http'), 301);
        exit;
    }
}

add_action('admin_init', 'redirect_to_page_by_user', 1);

if (!function_exists('pmm_submenu_pages')) {

    function pmm_submenu_pages() {
        //add sub menu pages
        add_submenu_page('pmm-control-panel', current_user_can( 'manage_options' ) ? __('General', 'wp-pmm') : '', current_user_can( 'manage_options' ) ? __('General', 'wp-pmm') : '', 'project_manager', 'pmm-control-panel', IncludePhpFile); //dissabled main
        add_submenu_page('pmm-control-panel', __('Area Manager', 'wp-pmm'), __('Area Manager', 'wp-pmm'), 'administrator', 'area-manager', IncludePhpFile);
        add_submenu_page('pmm-control-panel', __('Franchises', 'wp-pmm'), __('Franchises', 'wp-pmm'), 'administrator', 'franchises', IncludePhpFile);
        add_submenu_page('pmm-control-panel', __('Clients', 'wp-pmm'), __('Clients', 'wp-pmm'), 'project_manager', 'clients', IncludePhpFile);
        add_submenu_page('pmm-control-panel', __('Register Client', 'wp-pmm'), __('New Client', 'wp-pmm'), 'project_manager', 'client', IncludePhpFile);
        add_submenu_page('pmm-control-panel', __('Reset Password', 'wp-pmm'), __('Reset Password', 'wp-pmm'), 'project_manager', 'reset-password', IncludePhpFile);
        //remove wpsl_stores menu items
        remove_submenu_page('edit.php?post_type=wpsl_stores', 'edit.php?post_type=wpsl_stores');
        remove_submenu_page('edit.php?post_type=wpsl_stores', 'post-new.php?post_type=wpsl_stores');
    }

}
add_action('admin_menu', 'pmm_submenu_pages');

if (!function_exists('IncludePhpFile')) {

    function IncludePhpFile() {
        if (isset($_GET['page'])) {
            $filename = $_GET['page'];
            //if (file_exists($filename))
            require_once("views/{$filename}.php");
        }
    }

}

/**
 * Handle all the ajax requests
 */
if (!function_exists('pmm_ajax_callback')) {

    function pmm_ajax_callback() {

        if (isset($_REQUEST['method'])) {

            switch ($_REQUEST['method']) {
                case 'general':                    
                    parse_str($_REQUEST['data'], $data);
                    $pmm_settings = get_option('pmm_settings');
                    if ($pmm_settings) {
                        update_option('pmm_settings', $data);
                        $data['status'] = true;
                        $data['message'] = 'Options Saved!';
                    } else {
                        $data['status'] = true;
                        $data['message'] = 'Options Added!';
                        add_option('pmm_settings', $data);
                    }
                    //var_dump($data);
                    break;
                //Add new area
                case 'new_area':
                    $area = new WP_Area();
                    $area->set_attributes($_REQUEST['data']);

                    //validate for unique username
                    if ($area->is_duplicate('area', $area->area)) {
                        $data['status'] = false;
                        $data['message'] = 'Area already exists';
                    } else {
                        if ($area->insert()) {
                            $data['status'] = true;
                            $data['message'] = 'reload';
                        } else {
                            $data['status'] = false;
                            $data['message'] = $area->error;
                        }
                    }
                    break;
                //Reset client password
                case 'reset_password':
                    //var_dump($_REQUEST);
                    $client = new WP_Client();
                    $client->set_attributes($_REQUEST['data']);
                    //to encrypt the password
                    $client->before_insert();
                    //if update
                    $password = $client->password;
                    if ($client->update()) {
                        $client->get_row('id', $client->id);
						ob_start();
                        //$message = include 'emailTemplate.php';
                        include('views/passwordResetTemplate.php');   // execute the file
                        $message = ob_get_contents();
                        /*$message = '

            Hello ' . ucfirst($client->get_fullname()) . ',

            Your new password is ' . $password . '

            Thank you.
            
            Global Enterprise Disaster Restoration
            1800.725.7045                       
            ';*/
                        //$client->get_row('id', $client->id);
                        //$message = 'Hello ' . $client->get_fullname() . ',' . $message;
                        send_email($client->email, 'Your New Password', $message);
						ob_end_clean();
                        $data['status'] = true;
                        $data['message'] = 'reload';
                    } else {
                        $data['status'] = false;
                        $data['message'] = $client->error;
                    }
                    break;
                //Client login
                case 'login':
                    $client = new WP_Client();
                    if ($client->login($_REQUEST['data'])) {
                        $data['status'] = true;
                        $data['message'] = 'reload';
                    } else {
                        $data['status'] = false;
                        $data['message'] = $client->error;
                    }
                    break;
                //Client logout
                case 'logout':
                    $client = new WP_Client();
                    if ($client->logout()) {
                        $data['status'] = true;
                        $data['message'] = 'reload';
                    } else {
                        $data['status'] = false;
                        $data['message'] = $client->error;
                    }
                    break;
                //Add new client
                case 'register':
                    $client = new WP_Client();
                    $client->set_attributes($_REQUEST['data']);

                    //validate for unique username
                    if ($client->is_duplicate('username', $client->username)) {
                        $data['status'] = false;
                        $data['message'] = __('Username already exists', 'wp-pmm');
                    } else {
                        if ($client->insert()) {
                            //send email
                            if ($client->send_details == 1) {
                                $commercial_link = get_home_url() . "/commercial/";
                                $video_link = get_home_url() . '#video_id';
                                ob_start();
                                //$message = include 'emailTemplate.php';
                                include('views/emailTemplate.php');   // execute the file
                                $message = ob_get_contents();    // get the contents from the buffer
                              
                                /*$message= '
            Hello ' . ucfirst($client->get_fullname()) . ',
            <br/><br/>
            Thank you for giving us the opportunity to serve you.<br/>
            To track the progress of the work please visit the Client Login area at: ' . get_home_url() . '/client-dashboard/' . '
             
            <p>Use the below credentials to login:<p>
             
                Username:' . $client->username . ' <br/>
                Password:' . $client->password . ' <br/>
                
            <br/>You can also <a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8" target="_blank">download our new Mobile App</a> to your phone and get access to tracking the progress at all times, using the same credentials provided. 
            App available on IOS devices only for now. Android version will be available soon.
            <br/>Go to the App Store and <a href="https://itunes.apple.com/us/app/disaster-restoration/id649725393?mt=8"> download the Free Disaster Restoration App. </a>

            <p>To learn how to use the App go view the video tutorial <a href="' . $video_link . '"> here  <a> </p>

           Few important features in the Client Area:
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &#9679; My Project Status: Section that keeps you informed and clearly indicates how far we’ve progressed with your restoration.
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &#9679; Project Documents: Quickly access and download documents and related to your project. Including your Contract, Work Authorization, Customer Selection Form and other miscellaneous documents.
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &#9679; More Details :You’ll find more details, including dated notes and additional information about your project.
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  - Gallery: Easily browse high quality photos from the job site an attractive pop-up photo gallery
                <br/>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Videos: Easily browse videos related to each milestone.

            <p>If you have any questions please e-mail us at info@globalenterprisesouthflorida.com or call us on 1800.725.7045.</p>

             Also, please fill out our <a href="' . $commercial_link . '"> “RED ALERT PROGRAM”  </a> form ,
            <br/> <strong>Red Alert Program – Residential or Commercial </strong>
            <br/>By joining to our red alert program for your home, you minimize further damages by having an immediate plan of action. Knowing what to do and what to expect in advance is the key to timely mitigation and can help minimize how water and fire or even storm damage can affect your home.
            <br/><strong>Advantage of our red alert program:</strong>
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1.  It would take a little time to complete the form but it will save a lot of time if it’s ever needed.
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2.  You will know who to rely on when disaster happens and not to think about “What to do now?”
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3.  When disaster happens, our team will be there for you, we are well prepared to protect your property and mitigate your damages.
                <br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 4.  Providing detailed information about your home or business will avoid questions which require immediate answers. This saves time and money. 

            <br/><br/>
            <br/> Global Enterprise Disaster Restoration
            <br/> 1800.725.7045
                        ';*/
                                send_email($client->email, 'User Access', $message);
								ob_end_clean();
                            }
                            
                            else {

                                $subject = 'User Access of ' . $client->get_fullname();
                                ob_start();
                                include('views/Notificationemail.php');   // execute the file
                                $message = ob_get_contents();    // get the contents from the buffer
                                /*$message = '
            Hello ' . $client->get_fullname() . ',
            
            We are pleased to inform you that your preferred disaster restoration company: Global Enterprise has
            successfully set-up your mobile app access. You can now view the photos and read actual progress
            report of your property while the restoration process is being done.
            
            After you have downloaded the Disaster Restoration mobile app, log in to My Project with the user and 
            password below:     
            
            Username:' . $client->username . '
            Password:' . $client->password . '
            
            Feel free to contact us if you have any questions about the app or the project.
            Thank you.
            
            Global Enterprise Disaster Restoration
            1800.725.7045
            ';*/
                                send_email(get_option('admin_email'), $subject, $message);
				ob_end_clean();
                            }
                            $data['status'] = true;
                            $data['message'] = __('Client has been registered successfully.', 'wp-pmm');
                        } else {
                            $data['status'] = false;
                            $data['message'] = $client->error;
                        }
                    }
                    break;

                //Add new client
                case 'update':
                    $client = new WP_Client();
                    $client->set_attributes($_REQUEST['data']);

                    if ($client->update()) {
                        $data['status'] = true;
                        $data['message'] = __('Client has been updated successfully.', 'wp-pmm');
                    } else {
                        $data['status'] = false;
                        $data['message'] = $client->error;
                    }
                    break;
                //Could not find the requested method
                default :
                    $data['status'] = false;
                    $data['message'] = 'Requested method (' . $_REQUEST['method'] . ') is not found!';
                    break;
            }

            echo json_encode($data);
            wp_die();
        }
    }

}
add_action('wp_ajax_pmm_ajax_action', 'pmm_ajax_callback');
add_action('wp_ajax_nopriv_pmm_ajax_action', 'pmm_ajax_callback');

/**
 * Show custom user profile fields
 * @param  obj $user The user object.
 * @return void
 */
function franchise_custom_profile_fields($user) {
    /*
      https://codex.wordpress.org/Roles_and_Capabilities#Editor
      https://codex.wordpress.org/Function_Reference/current_user_can
      https://codex.wordpress.org/Function_Reference/add_cap
     */
    if (current_user_can('administrator' && 'editor') && is_user_logged_in()) {
        $area = new WP_Area();
        $areas = $area->get_results();
        ?>
        <div id="pm-filds" style="display: <?= ($user == 'add-new-user' || $user->roles[0] != 'project_manager') ? 'none' : 'block' ?>">
            <h3><?php IS_PROFILE_PAGE ? _e('Your Franchise Details') : _e('Franchise Details'); ?></h3>
            <hr>
            <table class="form-table">
                <tr class="user-contact-wrap">
                    <th><label for="contact_person"><?php _e('Contact Person') ?></label></th>
                    <td><input type="text" name="contact_person" id="contact_person" value="<?php echo esc_attr($user->contact_person) ?>" class="regular-text" /></td>
                </tr>
                <tr class="user-area-wrap">
                    <th><label for="area"><?php _e('Franchise Area') ?></label></th>
                    <td><select name="area" id="area">
                            <option value="">Please Select</option>
                            <?php foreach ($areas as $val): ?>
                                <option value="<?= $val->area; ?>" <?= $user->area == $val->area ? 'selected="selected"' : '' ?>><?= $val->area; ?></option>
                            <?php endforeach; ?>
                        </select></td>
                </tr>
                <tr class="user-description-wrap">
                    <th><label for="address"><?php _e('Address'); ?></label></th>
                    <td><textarea name="address" id="address" rows="4" cols="30"><?php echo $user->address; ?></textarea>
                        <p class="description"><?php _e('Your perment Address. This may be shown publicly.'); ?></p></td>
                </tr>
                <tr class="user-working-hour-wrap">
                    <th><label for="working_hours"><?php _e('Working hours') ?></label></th>
                    <td><input type="text" name="working_hours" id="working_hours" value="<?php echo esc_attr($user->working_hours) ?>" class="regular-text" /></td>
                </tr>
                <tr class="user-telephone-wrap">
                    <th><label for="telephone"><?php _e('Telephone') ?></label></th>
                    <td><input type="text" name="telephone" id="telephone" value="<?php echo esc_attr($user->telephone) ?>" class="regular-text" /></td>
                </tr>
                <tr class="user-address-wrap">
                    <th><label for="lat"><?php _e('Latitude') ?></label></th>
                    <td><input type="text" name="lat" id="lon" value="<?php echo esc_attr($user->lat) ?>" class="regular-text" required="required" /></td>
                </tr>
                <tr class="user-address-wrap">
                    <th><label for="lon"><?php _e('Longitude') ?></label></th>
                    <td><input type="text" name="lon" id="lon" value="<?php echo esc_attr($user->lon) ?>" class="regular-text" required="required" /></td>
                </tr>
                <tr class="user-address-wrap">
                    <th><label for="lat"><?php _e('Remarks') ?></label></th>
                    <td><input type="text" name="remarks" id="lon" value="<?php echo esc_attr($user->remarks) ?>" class="regular-text" /></td>
                </tr>
            </table>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                role = "<?= isset($_GET['role']) ? $_GET['role'] : ""; ?>";
                if (role == "")
                    role = jQuery('#role').val();
                else
                    jQuery('#role').val(role);
                toggle_fields(role)
            });
            jQuery('#role').on('change', function () {
                toggle_fields(this.value);
            });
            function toggle_fields(val) {
                if (val == 'franchisee')
                    jQuery('#pm-filds').show();
                else
                    jQuery('#pm-filds').hide();
            }
        </script>
        <?php
    }
}

add_action('show_user_profile', 'franchise_custom_profile_fields');
add_action('edit_user_profile', 'franchise_custom_profile_fields');
add_action('user_new_form', 'franchise_custom_profile_fields');

/**
 * Update custom user profile fields
 * @param  obj $user The user object.
 * @return void
 */
function franchise_custom_profile_save($user_id) {
    if (!current_user_can('edit_user', $user_id))
        return false;
    update_user_meta($user_id, 'contact_person', $_POST['contact_person']);
    update_user_meta($user_id, 'area', $_POST['area']);
    update_user_meta($user_id, 'address', $_POST['address']);
    update_user_meta($user_id, 'working_hours', $_POST['working_hours']);
    update_user_meta($user_id, 'telephone', $_POST['telephone']);
    update_user_meta($user_id, 'lon', $_POST['lon']);
    update_user_meta($user_id, 'lat', $_POST['lat']);
    update_user_meta($user_id, 'remarks', $_POST['remarks']);
    $posted_id = get_user_meta($user_id, 'post_id', true);
    //Create a store post
    $post = array(
        'ID' => $posted_id == '' ? 0 : $posted_id,
        'post_type' => 'wpsl_stores',
        'post_status' => 'publish',
        'post_title' => wp_strip_all_tags($_POST['contact_person']),
        'post_content' => $_POST['first_name'] . ' ' . $_POST['last_name']
    );

    $post_id = wp_insert_post($post);
    update_user_meta($user_id, 'post_id', $post_id); //
    update_post_meta($post_id, 'wpsl_address', $_POST['address']);
    update_post_meta($post_id, 'wpsl_city', $_POST['area']);
    update_post_meta($post_id, 'wpsl_country', 'United States');
    update_post_meta($post_id, 'wpsl_phone', $_POST['telephone']);
    update_post_meta($post_id, 'wpsl_hours', $_POST['working_hours']);
    update_post_meta($post_id, 'wpsl_lat', $_POST['lat']);
    update_post_meta($post_id, 'wpsl_lng', $_POST['lon']);
    update_post_meta($post_id, 'wpsl_country_iso', 'US');
}

add_action('personal_options_update', 'franchise_custom_profile_save');
add_action('edit_user_profile_update', 'franchise_custom_profile_save');
add_action('user_register', 'franchise_custom_profile_save');

/**
 * Send php mail message
 * @param string $to
 * @param string $subject
 * @param string $message
 * @return boolean
 */
function send_email($to, $subject, $message) {
    add_filter('wp_mail_content_type', 'set_html_content_type');

    $headers = 'From: Global Enterprise South Florida <webmaster@globalenterprisesouthflorida.com>' . "\r\n" .
            'Reply-To: noreply@globalenterprisesouthflorida.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

    if (wp_mail($to, $subject, $message, $headers)) {
        remove_filter('wp_mail_content_type', 'set_html_content_type');
        return true;
    } else {
        return false;
    }
}

function set_html_content_type() {
    return 'text/html';
}

// Redefine user notification function
if (!function_exists('wp_new_user_notification')) {

    function wp_new_user_notification($user_id, $deprecated = null, $notify = '') {
        if ($deprecated !== null) {
            _deprecated_argument(__FUNCTION__, '4.3.1');
        }

        global $wpdb, $wp_hasher;
        $user = get_userdata($user_id);

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
        // we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $message = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        $message .= sprintf(__('Email: %s'), $user->user_email) . "\r\n";

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

        // `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notifcation.
        if ('admin' === $notify || ( empty($deprecated) && empty($notify) )) {
            return;
        }

        // Generate something random for a password reset key.
        $key = wp_generate_password(20, false);

        /** This action is documented in wp-login.php */
        do_action('retrieve_password_key', $user->user_login, $key);

        // Now insert the key, hashed, into the DB.
        if (empty($wp_hasher)) {
            require_once ABSPATH . WPINC . '/class-phpass.php';
            $wp_hasher = new PasswordHash(8, true);
        }
        $hashed = time() . ':' . $wp_hasher->HashPassword($key);
        $wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user->user_login));

        $message = __('Hi,') . "\r\n\r\n";
        $message .= __('Below are the details for you to access the Global Enterprise South Florida Franchise Area.') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
        $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

        $message .= wp_login_url() . "\r\n\r\n";
        $message .= __('Thank you,') . "\r\n\r\n";
        $message .= __('Global Enterprise Disaster Restoration') . "\r\n";
        $message .= __('1800.725.7045') . "\r\n";

        wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
    }

}

/* Removing meta box from client project */

function remove_yoast_metabox_client_project() {
    remove_meta_box('wpseo_meta', 'client_project', 'normal');
}

add_action('add_meta_boxes', 'remove_yoast_metabox_client_project', 11);
