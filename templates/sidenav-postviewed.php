<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div id="uc-recent-postviewed" class="uc-sidenav">
    <h3 class="uc-sidenav-title">
        <?php esc_html_e("Recently Viewed", "rng-isuc"); ?>
    </h3>
    <a href="#" class="uc-close-sidenav">
        <?php esc_html_e("Close", "rng-isuc"); ?></a>
    <?php
    if ($has_posts) {
        ?>
        <ul class="uc-post-viewed ja-pp-style-1">
            <?php
            $query = new WP_Query($query_args);
            if ($query->have_posts()):

                while ($query->have_posts()):
                    $query->the_post();
                    $post_id = get_the_ID();

                    $post_thumbnail_id = get_post_thumbnail_id(get_the_ID());
                    $thumbnail_alt = get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', TRUE);
                    $thumbnail_url = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail', FALSE);
                    $block_el = (has_post_thumbnail($post_id)) ? "" : "block-el";
                    ?>
                    <li>

                        <?php
                        if (isset($thumbnail_url) and ! empty($thumbnail_url)) {
                            ?>
                            <a class="uc-post-viewed-thumb-wrapper" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                <img class="papular-posts-widg-thumbnail" src="<?php echo current($thumbnail_url) ?>" title="<?php echo get_the_title() ?>" alt="<?php echo get_the_title(); ?>">
                            </a>
                            <?php
                        }
                        ?>

                        <a class="uc-post-viewed-title-wrapper <?php echo $block_el; ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                            <p class="uc-post-viewed-title">
                                <?php the_title(); ?>
                            </p>
                        </a>
                        <span class="uc-post-viewed-date">
                            <?php echo get_the_date(); ?></span>
                    </li>
                    <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </ul>

        <?php
    } else {
        ?>
        <div class="uc-no-post-viewed">
            <p>
                <?php _e("No Post Was Viewed", "rng-isuc"); ?>
            </p>
            <i class="ucicon-eye-blocked"></i>
        </div>
        <?php
    }
    ?>
</div>

<!-- Use any element to open the sidenav -->
<a href="#" class="uc-open-sidenav"><i class="ucicon-clock"></i><span>
        <?php esc_html_e("Post Viewed", "rng-isuc"); ?></span></a>
<div class="uc-black-window"></div>
