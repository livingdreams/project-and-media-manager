<?php
/**
 * WP_ClientProject Class.
 *
 * @author   Amal Ranganath
 * @category Admin
 * @package  PMM/WP_ClientProject
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_ClientProject')) {

    class WP_ClientProject {

        private $_isDevelopment = false;
        private $_badge;
        private $_payload;
        private $_apns_url;
        private $_apns_port = 2195;
        private $_apns_cert = 'pmm-live.pem';
        private $_passphrase = '#tivadar123';

        public function __construct() {
            add_action('init', array(__CLASS__, 'init'));
            add_action('pre_get_posts', array(__CLASS__, 'before_list_projects'));
            add_action('add_meta_boxes', array(__CLASS__, 'add_custom_meta_boxes'));
            add_action('save_post', array($this, 'save_projects_meta_data'), 1, 2); // save the custom fields
            //include project template
            add_filter('template_include', array(__CLASS__, 'include_project_template'));
        }

        /**
         * Acction to add a filter before query execute
         * @param object $query
         * @return object
         */
        public function before_list_projects($query) {
            if (!is_super_admin() && is_admin() && $query->query_vars['post_type'] == 'client_project')
                add_filter('posts_where', array(__CLASS__, 'filter_current_user_projects'));
            return $query;
        }

        /**
         * Add filter to Where condition
         * @param string $where
         * @return string
         */
        public function filter_current_user_projects($where = '') {
            $user_id = get_current_user_id();
            $where .= " AND post_author = '$user_id'";
            return $where;
        }

        /**
         * Register custome post type 'Project'
         */
        public function init() {

            register_post_type('client_project', array(
                'labels' => array(
                    'name' => __('Client Projects', 'wp-pmm'),
                    'menu_name' => __('Client Projects', 'wp-pmm'),
                    'singular_name' => __('Project', 'wp-pmm'),
                    'add_new_item' => __('Add New  Project', 'wp-pmm'),
                    'edit_item' => __('Edit  Project', 'wp-pmm'),
                    'new_item' => __('New  Project', 'wp-pmm'),
                    'view_item' => __('View  Project', 'wp-pmm'),
                ),
                'description' => __('Project which we will be discussing franchisee and assinged clients.', 'wp-pmm'),
                'public' => true,
                'menu_position' => 20,
                'show_ui' => true,
                //'show_in_menu' => 'pm-control-panel',
                //'map_meta_cap' => true,
                //'rewrite' => true,
                'capability_type' => 'project',
                //'capabilities' => array('edit_posts' => 'project_manager', 'publish_posts' => 'project_manager', 'delete_post' => 'project_manager',),
                'supports' => array('title', 'excerpt', 'editor', 'author', 'thumbnail')
            ));
            flush_rewrite_rules();
        }

        public function include_project_template($template) {
            if (get_post_type() == 'client_project') {
                global $post;
                if (($_SESSION['user_login']) || $_SESSION['is_admin'] || current_user_can('administrator') || (get_current_user_id() == $post->post_author)) {
                    $template = PMM_DIR . 'views/single-project.php';
                } else {
                    $template = get_404_template();
                }
            }

            return $template;
        }

        public function add_custom_meta_boxes() {
            //select client
            add_meta_box('project_clients_meta_box', 'Assign to Client', array(__CLASS__, 'show_clients_meta_box'), 'client_project', 'side', 'high');
        }

        /**
         * show custom metabox fields
         */
        public function show_clients_meta_box() {
            global $post;
            $clients = new WP_Client();
            // Use nonce for verification to secure data sending
            wp_nonce_field(plugin_basename(__FILE__), 'pmm_projects_nonce');
            $client_id = get_post_meta($post->ID, '_client_id', true);
            ?>
            <select name='client_id' id='projects_meta_box_client'>
                <option value="">Please Select</option>
                <?php foreach ($clients->get_clients() as $client): ?>
                       <option value="<?php echo esc_attr($client->id); ?>" <?php selected($client->id, $client_id); ?>><?php echo esc_html($client->username); ?> - (<?php echo esc_html($client->firstname ); ?> <?php echo esc_html($client->lastname ); ?>)</option>
                <?php endforeach; ?>
            </select>

            <?php
        }

        /**
         * Save the post data
         * @param int $post_id
         * @param object $post
         */
        function save_projects_meta_data($post_id, $post) {
            //To verify this came from project post type & verify the authorization.
            if (!isset($_POST['pmm_projects_nonce']) || !wp_verify_nonce($_POST['pmm_projects_nonce'], plugin_basename(__FILE__))) {
                return $post->ID;
            }
            // is the user allowed to edit the post or page?
            if (!current_user_can('edit_post', $post->ID)) {
                return $post->ID;
            }
            
            
            // put it into an array to find and save the data
            $projects_post_meta['_client_id'] = $_POST['client_id'];

            $unseen = get_post_meta($post->ID, '_unseen', true);
            $projects_post_meta['_unseen'] = $unseen == '' ? 1 : ($unseen + 1);

            // add values as custom fields
            foreach ($projects_post_meta as $key => $value) {
                if (get_post_meta($post->ID, $key, FALSE)) { // if the custom field already has a value
                    update_post_meta($post->ID, $key, $value);
                } else { // if the custom field doesn't have a value
                    add_post_meta($post->ID, $key, $value);
                }
                if ($value === '') { // delete if blank
                    delete_post_meta($post->ID, $key);
                }
            }
            if ($projects_post_meta['_unseen'] != 0)
                $this->pushNotifcation($projects_post_meta);
        }

        /**
         * Send push notifications
         * @global object $wpbd
         * @param array $data
         */
        public function pushNotifcation($data) {

            global $wpdb;
            $this->_apns_url = $this->_isDevelopment ? 'gateway.sandbox.push.apple.com' : 'gateway.push.apple.com';

            $streamContext = stream_context_create();
            stream_context_set_option($streamContext, 'ssl', 'local_cert', PMM_DIR . $this->_apns_cert);
            stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->_passphrase);

            $apns = stream_socket_client('ssl://' . $this->_apns_url . ':' . $this->_apns_port, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);

            $table = $wpdb->prefix . 'devices';
            $devices = $wpdb->get_results("SELECT devicetoken AS UL FROM $table WHERE clientid = " . $data['_client_id'] . "");


            foreach ($devices as $key => $device) {
                $this->_badge = intval($data['_unseen']);
                $payload['aps'] = array(
                    'alert' => 'Updates available.',
                    'badge' => $this->_badge,
                    'sound' => 'default'
                );
                $this->_payload = json_encode($payload);


                //$apns_message = 'Test';
                $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device->UL)) . chr(0) . chr(strlen($this->_payload)) . $this->_payload;
                fwrite($apns, $apns_message);

                $filename = PMM_DIR."pushed.txt";
                $fh = fopen($filename, "a") or die("Could not open log file.");

                fwrite($fh, date("d-m-Y, H:i") . " - t:" . $key . " of " . count($devices) . " seen=". $this->_badge ." devToken=". $device->UL) or die("Could not write file!");
                fclose($fh);
            }

            @socket_close($apns);
            @fclose($apns);
        }

    }

    return new WP_ClientProject();
}
