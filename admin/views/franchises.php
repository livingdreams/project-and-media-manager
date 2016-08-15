<?php
require_once(PMM_DIR . 'models/base-model.php');

class PMM_Franchises extends baseModel {

    const TABLE_NAME = 'franchises';
    const PRIMARY_KEY = 'id';

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = array($this->get_columns(), array(), array());

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('user_per_page', 10);
        $current_page = $this->get_pagenum();

        $user = new WP_User_Query(array('role' => 'franchisee', 'number' => $per_page, 'offset' => ( $current_page - 1 ) * $per_page));

        $items = new WP_User_Query(array('role' => 'franchisee'));

        $this->set_pagination_args([
            'total_items' => $items->get_total(), //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = $user->get_results();
    }

    /**
     *  Associative array of columns
     * @return array
     */
    public function get_columns() {
        $columns = [
            //'cb' => '<input type="checkbox" />',
            'id' => __('Name', 'wp-pmm'),
            'user_email' => __('Email', 'wp-pmm'),
            //'display_name' => __('Display Name', 'wp-pmm'),
            'area' => __('Area', 'wp-pmm'),
            'address' => __('Address', 'wp-pmm'),
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
            //case 'id':
            case 'display_name':
            case 'user_email':
            case 'area':
            case 'address':
                return $item->$column_name;
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Edit action for image column
     * @param array $item
     * @return string
     */
    function column_id($item) {
        $actions = array(
            'edit' => sprintf('<a href="user-edit.php?user_id=%s">%s</a>', $item->id, __('Edit')),
                //'delete' => sprintf('<a href="?page=%s&tab=%s&action=delete&id=%s&paged=%s">Delete</a>', $_REQUEST['page'], $_REQUEST['tab'], $item['id'], $_REQUEST['paged']),
        );

        return sprintf('%1$s %2$s', sprintf('<a class="row-title" href="user-edit.php?user_id=%s">%s</a>', $item->id, $item->display_name), $this->row_actions($actions));
    }

}

//display Franchises
$franchises = new PMM_Franchises();
//var_dump($fra);
?>
<div class="wrap">

    <h1>Registered Franchises <a href="<?= bloginfo('url') ?>/wp-admin/user-new.php?role=franchisee" class="page-title-action">Add New</a></h1>
    <hr>
    <div class="pm_block">
        <div id="errorMessage"></div>
    </div>

    <?php
    $franchises->prepare_items();
    $franchises->display();
    ?>

</div>