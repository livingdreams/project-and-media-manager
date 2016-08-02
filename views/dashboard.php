<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$client = new WP_Client();
$client->get_row('id', $_SESSION['user_id']);
//var_dump($client->is_admin ); die('asd');
?>

<div class="dashboard-para">
    <?php echo 'Hi ' . '<span class="client-name">' . $client->get_fullname() . '</span>' ?>
    | <a href="#" title="Log out" id="log-out">Log Out</a>
</div>
<?php
//get projects
$args = array(
    'posts_per_page' => -1,
    'offset' => 0,
    //'category' => ,
    //'orderby' => 'menu_order, post_title', // post_date, rand
    //'order' => 'DESC',
    //'include' => ,'exclude' => ,
    'meta_key' => $client->is_admin ? '' : '_client_id',
    'meta_value' => $client->is_admin ? '' : $client->id,
    'post_type' => 'client_project',
    //'post_mime_type'  => ,
    //'post_parent'     => ,
    'post_status' => 'publish',
        //'suppress_filters' => true
);
?>
<h2 class="sub-para"> Your Projects </h2>
<?php
$loop = new WP_Query($args);
?>
<ul>
    <?php while ($loop->have_posts()) : $loop->the_post(); ?>
        <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
    <?php endwhile; ?>
</ul>