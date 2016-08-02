<?php

/**
 * WP_Area Class.
 *
 * @author   Amal Ranganath
 * @category Model
 * @package  PMM/WP_Area
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once 'base-model.php';

if (!class_exists('WP_Area')) {

    class WP_Area extends baseModel {

        /**
         * Table identity (requred)
         * @static
         */
        const TABLE_NAME = 'area';
        const PRIMARY_KEY = 'id';

        /**
         * Table fields
         * @var string 
         */
        public $id;
        public $area;
        //public $lat;
        //public $lng;

        public static function findInArea($find) {
            global $wpdb;
            $table_name = $wpdb->prefix . self::TABLE_NAME;
            //$sql = "SELECT id, area, ( %d * acos( cos(radians(%s)) * cos(radians(lat)) * cos( radians(lng) - radians(%s) ) + sin( radians(%s) ) * sin( radians(lat) ) ) ) AS distance FROM $table_name HAVING distance < %d ORDER BY distance";
            $sql = "SELECT * FROM $table_name WHERE MBRContains(LineFromText(CONCAT(
        '('
        , @lng + d% / ( 111.1 / cos(RADIANS(@lng)))
        , ' '
        , @lat + d% / 111.1
        , ','
        , @lng - d% / ( 111.1 / cos(RADIANS(@lat)))
        , ' '
        , @lat - d% / 111.1
        , ')' )
        ,mypoint)";
            return $wpdb->get_results($wpdb->prepare($sql, $find));
        }

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
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    area varchar(64) NOT NULL,
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
         * Handles data query and filter, sorting, and pagination.
         */
        public function prepare_items() {

            $this->_column_headers = array($this->get_columns(), array(), array());

            /** Process bulk action */
            $this->process_bulk_action();

            $per_page = $this->get_items_per_page('area_per_page', 10);
            $current_page = $this->get_pagenum();
            $condition = " LIMIT $per_page";
            $condition .= " OFFSET " . ( $current_page - 1 ) * $per_page;
            $total_items = count($this->get_results());

            $this->set_pagination_args([
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page //WE have to determine how many items to show on a page
            ]);

            $this->items = $this->get_results($condition, ARRAY_A);
        }

        /**
         *  Associative array of columns
         * @return array
         */
        public function get_columns() {
            $columns = [
                //'cb' => '<input type="checkbox" />',
                'area' => __('Area', 'wp-pmm'),
                //'lat' => __('Latitude', 'wp-pmm'),
                //'lng' => __('Longitude', 'wp-pmm'),
            ];

            return $columns;
        }

        /**
         * 
         * @param array $item
         * @param string $column_name
         * @return string
         */
        function column_default($item, $column_name) {
            switch ($column_name) {
                case 'area':
                //case 'lat':
                //case 'lng':
                    return $item[$column_name];
                default:
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }


    }

}