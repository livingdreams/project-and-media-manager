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
        add_submenu_page('pmm-control-panel', current_user_can('manage_options') ? __('General', 'wp-pmm') : '', current_user_can('manage_options') ? __('General', 'wp-pmm') : '', 'project_manager', 'pmm-control-panel', IncludePhpFile); //dissabled main
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
                        include('email-templates/reset-password.php');
                        $message = ob_get_clean();
                        //send email
                        send_email($client->email, 'Your New Password', $message);

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
                                $subjectAdminNotification = 'User Access of ' . $client->get_fullname();
                                ob_start();
                                include('email-templates/welcome.php');
                                $message = ob_get_clean();
                                send_email($client->email, 'User Access', $message);
                                $current_user = wp_get_current_user();
                                send_email($current_user->user_email, $subjectAdminNotification, $message);
                                ob_end_clean();
                            } else {
                                $subject = 'User Access of ' . $client->get_fullname();
                                $subjectAdminNotification = 'User Access of ' . $client->get_fullname();
                                ob_start();
                                include('email-templates/notify-admin.php');
                                $message = ob_get_clean();
                                $current_user = wp_get_current_user();
                                send_email($current_user->user_email, $subjectAdminNotification, $message);
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
                //Update client
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
 * Send php mail message
 * @param string $to
 * @param string $subject
 * @param string $message
 * @return boolean
 */
function send_email($to, $subject, $message) {
    //filter email content type
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

//set email content type
function set_html_content_type() {
    return 'text/html';
}

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
                    <th><label for="contact_person"><?php _e('Contact') ?></label></th>
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
                    <td><textarea name="address" id="address" rows="4" cols="30"><?= $user->address; ?></textarea>
                        <p class="description"><?php _e('Your perment Address. This may be shown publicly.'); ?></p></td>
                </tr>
                <tr class="user-working-hour-wrap">
                    <th><label for="working_hours"><?php _e('Working hours') ?></label></th>
                    <td><textarea name="working_hours" id="working_hours" rows="3" cols="30"><?= $user->working_hours ?></textarea></td>
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
    update_post_meta($post_id, 'wpsl_address', $_POST['area']);
    update_post_meta($post_id, 'wpsl_city', $_POST['address']);
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
        ob_start();
        include('email-templates/welcome-Franchisee.php');   // execute the file
        $message = ob_get_contents();
        send_email($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
        send_email(get_option('admin_email'), sprintf(__('[%s] Username and password info'), $blogname), $message);
        ob_end_clean();
        //wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
    }

}

/* Removing meta box from client project */

function remove_yoast_metabox_client_project() {
    remove_meta_box('wpseo_meta', 'client_project', 'normal');
}

add_action('add_meta_boxes', 'remove_yoast_metabox_client_project', 11);
