<div class="wrap">

    <h1>Registered Franchises <a href="<?= bloginfo('url') ?>/wp-admin/user-new.php?role=franchisee" class="page-title-action">Add New</a></h1>

    <hr>

    <div class="pm_block">
        <div id="errorMessage"></div>
    </div>
    <?php $user = new WP_User_Query(array('role' => 'franchisee')); ?>
    <ul class="subsubsub">
        <li class="all"><a href="" class="current">All <span class="count">(<?= $user->total_users ?>)</span></a></li>        
    </ul>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr><th><span>Name</span></th><th>Email</th><th>Area</th><th>Address</th></tr>
        </thead>
        <tbody>
            <?php
            //User Loop
            if (!empty($user->results)) {
                foreach ($user->results as $user)
                    echo '<tr><td><a class="row-title" href="' . get_bloginfo('url') . '/wp-admin/user-edit.php?user_id=' . $user->id . '" >' . $user->display_name . '</a><br/><div class="row-actions"><span class="edit"><a href="' . get_bloginfo('url') . '/wp-admin/user-edit.php?user_id=' . $user->id . '" aria-label="Edit “' . $user->display_name . '”">Edit</a> </span></div></td><td>' . $user->user_email . '</td><td>' . $user->area . '</td><td>' . $user->address . '</td></tr>';
            } else {
                echo 'No users found.';
            }
            ?>
        </tbody>
    </table>
    <?php
    ?>
</div>