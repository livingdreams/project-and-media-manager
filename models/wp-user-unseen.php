<?php

/**
 * WP_USER_UNSSEN Class.
 *
 * @author   Amal Ranganath
 * @category Model
 * @package  ProjectManager/WP_Client
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once 'base-model.php';

if (!class_exists('WP_USER_UNSSEN')) {

    class WP_USER_UNSSEN extends baseModel {

        /**
         * Table identity (requred)
         * @static
         */
        const TABLE_NAME = 'user_unseen';
        const PRIMARY_KEY = 'id';

        /**
         * Table fields
         * @var string 
         */
        public $id;
      
       
        /**
         * Create Table
         * @global type $wpdb
         */
        public static function createTable() {
            //Create tabel
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    post_id bigint(20) NOT NULL,
    unseen int(11) DEFAULT 0 NOT NULL,
    is_client tinyint(1) DEFAULT 0 NOT NULL,
    status tinyint(1) DEFAULT 0 NOT NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;";
            $wpdb->query($sql);
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
        
        /**
         * Save data
         * @global object $wpdb
         */
        public function save($data) {
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            if ($wpdb->insert($table_name, $data))
                return $wpdb->insert_id;
            else
                return false;
        }
        
         public function update_row($data) {
            
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            $user_id = $data['user_id'];
            $post_id = $data['post_id'];
            
            $row_exists = $wpdb->query("SELECT * FROM $table_name WHERE post_id = '".$data['post_id']."' AND user_id = '".$data['user_id']."'");
            if($row_exists > 0){
                $wpdb->query("UPDATE $table_name SET unseen=unseen+1 WHERE user_id = $user_id AND post_id = $post_id");
            }else{
                $data['unseen'] = 1;
                $wpdb->insert($table_name, $data);
            }
             
        }
        
        /**
         * Get single row
         * @param string $attribute
         * @param void $value
         * @return array
         */
        public function get_single_row($condition) {
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            if ($row = $wpdb->get_row("SELECT * FROM $table_name WHERE $condition")) {
                return $row;
            }
            return false;
        }
        
        public function update_unseen($url_id){
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            $pk = self::PRIMARY_KEY;
            if ($wpdb->update($table_name, array('unseen' => 0), array($pk => $url_id)))
                return true;
            else
                return false;
        }

        

    }

}
?>
