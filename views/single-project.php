<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
acf_form_head();
get_header();
$client = new WP_Client();
$client->get_row('id', $_SESSION['user_id']);
?>

<div class="et_pb_row" style="padding-bottom:0px;">
    <div class="dashboard-para">
        <?php echo 'Hi ' . '<span class="client-name">' . $client->get_fullname() . '</span>' ?>
        | <a href="<?= bloginfo('url') ?>/client-dashboard" title="Dashboard" >Back to Projects</a> 
    </div>
</div>

<?php
$view = 1;
if (isset($_SESSION['user_id']) && !$client->is_admin) {
    $client_id = get_post_meta(get_the_ID(), '_client_id',true);
    if ($client_id != $client->id)
        $view = 0;
}
    /* global $post;
      $mypost = array(
      'name' => $post->post_name,
      'post_type' => 'client_project',
      'meta_key' => '_client_id',
      'meta_value' => $client->id,
      );
      $loop = new WP_Query($mypost); */
    //while ($loop->have_posts()) : $loop->the_post();
if($view):	
    ?>
    <div id="main-content" class="project-template">

        <div class="et_pb_row" style="padding-top: 0px;">

            <h1><?php the_title(); ?></h1>
            <div class="entry-content">
                <?php the_content(); ?>
                <div class="sub-para">
                    <h4>Project Status :</h4>
                    <?php
                    $progress = get_field('progress');
                    $progress = ($progress == 7) ? 100 : $progress * 15;
                    ?>
                    <ul class="et_pb_counters et-waypoint et_pb_module et_pb_bg_layout_light  et_pb_counters_0 et-animated">
                        <li class="et_pb_counter_0">
                            <!--<span class="et_pb_counter_title">Project Status</span>-->
                            <span class="et_pb_counter_container et-animated" style="background-color: #dddddd;">
                                <span class="et_pb_counter_amount" style="width: <?= $progress ?>%; min-width: 47px; background-color: rgb(61, 173, 27);" data-width="<?= $progress ?>%"><span class="et_pb_counter_amount_number"><?= $progress ?>%</span></span>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="sub-para">
                    <?php if (have_rows('file_uploads')): ?>
                        <h4>Project Documents :</h4>
                        <ul class="files">
                            <?php
                            while (have_rows('file_uploads')): the_row();
                                // vars
                                $file = get_sub_field('file');
                                $fileTitle = get_sub_field('file_title');
                                ?>

                                <li class="file">
                                    <a href="<?php echo $file['url']; ?>" target="_blank"><?php echo $fileTitle; ?></a>
                                </li>

                            <?php endwhile; ?>
                        </ul>

                    <?php endif; ?>
                </div>

                <?php if (have_rows('milestone')): ?>
                    <div class="sub-para">
                        <h4>More Details :</h4> 
                        <?php while (have_rows('milestone')): the_row(); ?>
                            <?php
                            $date = get_sub_field('date');
                            $description = get_sub_field('description');
                            $images = get_sub_field('gallery');
                            ?>
                            <div class="milestone">
                                <strong><?php echo $date; ?></strong> : <?php echo $description; ?> 
                                <?php if ($images): ?>
                                    <div class="sub-para">
                                        <h6>Gallery :</h6>
                                        <div class="et_pb_row mile_gallery">		
                                            <div class="et_pb_column et_pb_column_4_4">
                                                <div class="et_pb_gallery_grid et_pb_bg_layout_light clearfix" style="display: block;">
                                                    <div class="et_pb_gallery_items et_post_gallery">
                                                        <ul>
                                                            <?php
                                                            foreach ($images as $image):
                                                                $content = '<div class="et_pb_gallery_item et_pb_grid_item et_pb_bg_layout_light"><div class="et_pb_gallery_image landscape">';
                                                                $content .= '<a  href="' . $image['url'] . '">';
                                                                $content .= '<img src="' . $image['sizes']['medium'] . '" alt="' . $image['alt'] . '" />';
                                                                $content .= '<span class="et_overlay"></span></a>';
                                                                $content .= '</div></div>';

                                                                echo $content;
                                                                ?>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                ?>

                                <?php if (have_rows('video_uploads')):
                                    ?>
                                    <div class="sub-para">
                                        <h6>Videos :</h6>
                                        <?php
                                        while (have_rows('video_uploads')): the_row();
                                            $video = get_sub_field('add_video');
                                            $thumb = get_sub_field('video_thumbnail');
                                            ?> 
                                            <div class="et_pb_column et_pb_column_1_2 custom-video">
                                                <video id="my_video_1" class="video-js vjs-default-skin" controls preload="auto" width="480" height="264"  data-setup="{}">
                                                    <source src="<?= $video['url'] ?>" type='video/mp4'>
                                                    <source src="<?= $video['url'] ?>" type='video/webm'>
                                                </video>
                                            </div>
                                            <!--<a href="<?= $video['url'] ?>" target="_blank"title="Download">Download</a>-->
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

            </div> 
        </div>
    </div>

    <?php
endif;
//endwhile;
get_footer();
