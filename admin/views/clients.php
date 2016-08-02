<?php
/**
 * Clients - form or list view
 * @package Admin
 * @type template
 */
if ($_GET['action'] == 'edit') {
    require_once ('client.php');
} else {
    $client = new WP_Client();
    ?>
    <div class = "wrap">
        <h2>List Clients <a href="<?= bloginfo('url') ?>/wp-admin/admin.php?page=client" class="page-title-action">Add New</a></h2>
        <hr>
        <form method="get">
            <?php
            $client->prepare_items();
            $client->display();
            ?>
        </form>
    </div>
    <?php
}