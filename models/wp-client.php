<?php

/**
 * WP_Client Class.
 *
 * @author   Amal Ranganath
 * @category Model
 * @package  ProjectManager/WP_Client
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

session_start();

require_once 'base-model.php';

if (!class_exists('WP_Client')) {

    class WP_Client extends baseModel {

        /**
         * Table identity (requred)
         * @static
         */
        const TABLE_NAME = 'clients';
        const PRIMARY_KEY = 'id';

        /**
         * Table fields
         * @var string 
         */
        public $id;
        public $owner;
        public $firstname;
        public $lastname;
        public $username;
        public $password;
        public $email;
        public $address;
        public $telno;
        public $mobileno;
        public $status;
        public $is_admin;

        /**
         * Other fields
         * @var string 
         */
        public $send_details;

        /**
         * Encrypt pass before insert
         */
        public function before_insert() {
            //use password hash        
            $this->attributes['password'] = wp_hash_password($this->password);
            unset($this->attributes['send_details']);
        }

        public function get_fullname() {
            return ucfirst($this->firstname) . ' ' . ucfirst($this->lastname);
        }

        /**
         * Client login
         * @param array $data
         * @return boolean
         */
        public function login($data) {
            parent::set_attributes($data);
            $password = $this->password;
            if ($this->get_row('username', $this->username)) {
                //is already logged in
                if (isset($_SESSION['user_login']) && $_SESSION['user_name'] == $this->username)
                    return true;
                //validate password
                require_once ABSPATH . 'wp-includes/class-phpass.php';
                $hasher = new PasswordHash(8, true);
                if ($hasher->CheckPassword($password, $this->password)) {
                    $_SESSION['user_login'] = true;
                    $_SESSION['user_id'] = $this->id;
                    $_SESSION['user_name'] = $this->username;
                    $_SESSION['is_admin'] = $this->is_admin ? true : false;
                    return true;
                } else {
                    $this->error = __("Wrong Password", "wp-pmm");
                }
            }
            //authentication failed
            return false;
        }

        public function logout() {
            if ($_SESSION['user_login']) {
                session_unset($_SESSION['user_login']);
                session_unset($_SESSION['user_id']);
                session_unset($_SESSION['user_name']);
                session_unset($_SESSION['is_admin']);
                return true;
            }
            $this->error = __("You are not logged in.", "wp-pmm");
            return false;
        }

        /**
         * Create Table
         * @global type $wpdb
         */
        public static function createTable() {
            //Create tabel
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table_name = $wpdb->prefix . self::TABLE_NAME;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    owner mediumint(9) NOT NULL,
                    username varchar(32) NOT NULL,
                    password varchar(64) NOT NULL,
                    email varchar(128) NOT NULL,
                    firstname varchar(64),
                    lastname varchar(64),
                    status varchar(255),
                    created_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    is_admin BOOLEAN NOT NULL,
                    UNIQUE KEY id (id)
                  ) $charset_collate;";
            dbDelta($sql);
        }

        /**
         * Drop Table
         * @global type $wpdb
         */
        public static function dropTable() {
            global $wpdb;
            $tablename = $wpdb->prefix . self::TABLE_NAME;
            $wpdb->query("DROP TABLE IF EXISTS $tablename");
        }

        public function get_clients() {
            $user_id = get_current_user_id();
            $condition = is_super_admin() ? '' : "WHERE owner = $user_id";
            return $this->get_results($condition);
        }

        /**
         * Handles data query and filter, sorting, and pagination.
         */
        public function prepare_items() {

            $this->_column_headers = array($this->get_columns(), array(), array());

            //Process bulk action 
            $this->process_bulk_action();

            $per_page = $this->get_items_per_page('clients_per_page', 10);
            $current_page = $this->get_pagenum();
            $user_id = get_current_user_id();
            $condition = is_super_admin() ? '' : "WHERE owner = $user_id";
            $condition .= " LIMIT $per_page";
            $condition .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;
            $total_items = count($this->get_clients());

            $this->set_pagination_args([
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            ]);

            $this->items = $this->get_results($condition, ARRAY_A);
        }

        /**
         * Associative array of columns
         * @return array
         */
        public function get_columns() {
            $columns = [
                //'cb' => '<input type="checkbox" />',
                'username' => __('Username', 'wp-pmm'),
                'firstname' => __('First Name', 'wp-pmm'),
                'lastname' => __('Last Name', 'wp-pmm'),
                'email' => __('Email', 'wp-pmm'),
                'created_on' => __('Created On', 'wp-pmm'),
            ];

            return $columns;
        }

        /**
         * Display the column value
         * @param array $item
         * @param string $column_name
         * @return string
         */
        function column_default($item, $column_name) {
            switch ($column_name) {
                case 'firstname':
                case 'lastname':
                case 'email':
                case 'created_on':
                    return $item[$column_name];
                default:
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }

        /**
         * Edit action for username column
         * @param array $item
         * @return string
         */
        function column_username($item) {
            $actions = array(
                'edit' => sprintf('<a href="?page=%s&action=edit&client=%s">Edit</a>', $_REQUEST['page'], $item['id']),
                    //'delete' => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id']),
            );

            return sprintf('%1$s %2$s', sprintf('<a class="row-title" href="?page=%s&action=edit&client=%s">%s</a>', $_REQUEST['page'], $item['id'], $item['username']), $this->row_actions($actions));
        }

    }

}
?>
