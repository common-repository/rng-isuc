<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (is_array($posts)) {
    ?>
    <ul class="rng-isuc ucrow">
        <?php
        $i = 1;
        foreach ($posts as $p) :
            $post_thumbnail = get_the_post_thumbnail($p->ID, 'post-thumbnail', array('class' => 'thumbnail'));

            $date_format = get_option('date_format');
            ?>
            <li class="item-product uccol-sm-3">

                <a href="<?php echo get_the_permalink($p->ID); ?>" title="<?php echo esc_attr($p->post_title); ?>">
                    <?php
                    if (!empty($post_thumbnail)) {
                        echo $post_thumbnail;
                    } else {
                        ?>
                        <img src="<?php echo trailingslashit(RNGUC_PDU); ?>assets/img/post-thumbnail.jpg" alt="<?php echo esc_attr($p->post_title); ?>" >
                        <?php
                    }
                    ?>
                </a>
                <h4>
                    <a href="<?php echo get_the_permalink($p->ID); ?>" title="<?php echo esc_attr($p->post_title); ?>"><?php echo esc_attr($p->post_title); ?></a>

                </h4>
                <span class="post-date"><?php echo get_the_date($p->ID); ?></span>

            </li>
            <?php
        endforeach;
        ?>
    </ul>
    <?php
}