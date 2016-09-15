<?php

/**
  Plugin Name: Project & Media Manager
  Plugin URI: livingdreams.lk
  Description: Manage your projects with documents, images and videos.
  Author: Amal Ranganath
  Version: 1.0.0
  Author URI: livingdreams.lk
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


//deafine plugins directory
if (!defined('PMM_DIR')) {
    define('PMM_DIR', plugin_dir_path(__FILE__));
}

require_once('admin/functions.php');
require_once('admin/projects.php');

/**
 * Plugin Activation hook
 */
function pmm_plugin_activation() {
    add_role('franchisee', 'Franchisee', array(
        'project_manager' => true,
        'read' => true,
        'upload_files' => true,
        'show_ui' => true,
        //'manage_options' => true,
        'publish_projects' => true,
        'edit_projects' => true,
        'edit_project' => true,
        'level_2' => true,
        'level_1' => true,
        'level_0' => true));
    $role = get_role('administrator');
    $role->add_cap('project_manager');
    $role->add_cap('publish_projects');
    $role->add_cap('edit_project');
    $role->add_cap('edit_others_projects');
    $role->add_cap('delete_others_projects');
    //$role->add_cap('delete_published_projects');
    $role->add_cap('delete_projects');
    $role->add_cap('delete_project');
    create_pmm_tables();
}

register_activation_hook(__FILE__, 'pmm_plugin_activation');

function create_pmm_tables() {
    WP_Area::createTable();
    WP_Client::createTable();
    //Create tabels
    global $wpdb;
    $table_name = $wpdb->prefix . 'devices';
    $charset_collate = $wpdb->get_charset_collate();
    $sql_devices = "CREATE TABLE IF NOT EXISTS $table_name (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `appname` varchar(255) NOT NULL,
  `appversion` varchar(25) DEFAULT NULL,
  `deviceuid` char(40) NOT NULL,
  `devicetoken` char(64) NOT NULL,
  `devicename` varchar(255) NOT NULL,
  `devicemodel` varchar(100) NOT NULL,
  `deviceversion` varchar(25) NOT NULL,
  `pushbadge` enum('disabled','enabled') DEFAULT 'disabled',
  `pushalert` enum('disabled','enabled') DEFAULT 'disabled',
  `pushsound` enum('disabled','enabled') DEFAULT 'disabled',
  `development` enum('production','sandbox') CHARACTER SET latin1 NOT NULL DEFAULT 'production',
  `status` enum('active','uninstalled') NOT NULL DEFAULT 'active',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(255) DEFAULT NULL,
  UNIQUE KEY pid (pid)
)  $charset_collate;";
    $wpdb->query($sql_devices);

    $table_name = $wpdb->prefix . 'messages';
    $sql_messages = "CREATE TABLE IF NOT EXISTS $table_name (
  `pid` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `clientid` varchar(64) NOT NULL,
  `fk_device` int(9) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `delivery` datetime NOT NULL,
  `status` enum('queued','delivered','failed') CHARACTER SET latin1 NOT NULL DEFAULT 'queued',
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY pid (pid)
)  $charset_collate;";
    $wpdb->query($sql_messages);
}

/**
 * Plugin Deactivation hook
 */
function pmm_plugin_deactivation() {
    remove_role('franchisee');
    $role = get_role('administrator');
    $role->remove_cap('project_manager');
    $role->remove_cap('publish_projects');
    $role->remove_cap('edit_project');
    $role->remove_cap('edit_others_projects');
    $role->remove_cap('delete_others_projects');
    //$role->remove_cap('delete_published_projects');
    $role->remove_cap('delete_projects');
    $role->remove_cap('delete_project');
    //WP_Area::dropTable();
    //WP_Client::dropTable();
}

register_deactivation_hook(__FILE__, 'pmm_plugin_deactivation');

/**
 * Filter uploads for current user
 * @param object $query
 * @return object
 */
function show_current_user_attachments($query) {
    if (!is_super_admin()) {
        $query['author'] = get_current_user_id();
    }
    return $query;
}

add_filter('ajax_query_attachments_args', 'show_current_user_attachments');

/**
 * Notice requred plugins
 */
function pmm_show_errors() {
    if (!class_exists('acf_pro'))
        echo '<div class="error"><p>The <a href="https://www.advancedcustomfields.com/">Advanced Custom Fields PRO</a> Plugin is required for Projects & Media Manager.</p></div>';
    if (!class_exists('WP_Store_locator'))
        echo '<div class="error"><p>The <a href="https://wordpress.org/plugins/wp-store-locator/">WP Store Locator</a> Plugin is required for Projects & Media Manager.</p></div>';
}

add_action('admin_notices', 'pmm_show_errors');

/**
 * API request 
 */
function pmm_API($request) {
    $require = $request->request;
    if (strpos($require, 'wp-api') !== false)
        include_once('pmm-api.php');
}

add_action('parse_request', 'pmm_API');

/**
 * Enqueue styles and scripts
 */
function pmm_front_scripts() {
    wp_enqueue_style('pmm-client-styles', plugins_url('assets/css/styles.css', __FILE__));
    wp_enqueue_script('pmm-client-scripts', plugins_url('assets/js/script.js', __FILE__), array('jquery'));
    wp_localize_script('pmm-client-scripts', 'pmm_ajax', array('url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'pmm_front_scripts');

/**
 * Shortcode to display client side
 */
function pmm_client_dashboard() {
    ob_start();
    if ($_SESSION['user_login'])
        require_once('views/dashboard.php');
    else
        require_once('views/login-form.php');
    $contents = ob_get_clean();
    return $contents;
}

add_shortcode('client', 'pmm_client_dashboard');

/**
 * Remove unwanted dashboard widgets for users
 */
function remove_dashboard_widgets() {
    $user = wp_get_current_user();
    if (!$user->has_cap('manage_options')) {
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'side');
    }
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');


