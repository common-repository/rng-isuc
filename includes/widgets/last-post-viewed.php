<?php

defined('ABSPATH') || exit;

class rnguc_posts_viewed_widget extends WP_Widget {

    public function __construct() {
        $widget_options = array(
            'classname' => 'uc-post-viewed',
            'description' => esc_html__("Show last post viewed by user", "rng-isuc")
        );
        parent::__construct("uc-post-viewed", esc_html__("Last PostViewed", "rng-isuc"), $widget_options);
    }

    /**
     * output widget
     */
    public function widget($args, $instance) {
        //$instance = get value from admin panel
        //$args = get structure of widget
        //apply_filters widget_title
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__("Last Posts viewed", "rng-isuc");
        $title_filtered = apply_filters("widget_title", $title);
        $post_types = (!empty($instance['post_types']) and isset($instance['post_types'])) ? $instance['post_types'] : array('post');
        $posts_count = (!empty($instance['posts_count'])) ? (int) $instance['posts_count'] : 4;
        $style = (!empty($instance['style'])) ? $instance['style'] : 0;

        $output = $args["before_widget"];
        $output .= $args["before_title"];
        $output .= $title_filtered;
        $output .= $args["after_title"];

        ob_start();
        global $rnguc_isuc;
        $query_args = $rnguc_isuc->get_query_args();
        $query_args['post_type'] = $post_types;
        $query_args['posts_per_page'] = $posts_count;
        $query = new WP_Query($query_args);
        ?>
        <ul class="uc-post-viewed ja-pp-style-<?php echo $style; ?>">
            <?php
            if ($query->have_posts()) {
                switch ($style):
                    case '0':
                        while ($query->have_posts()):
                            $query->the_post();
                            ?>
                            <li>
                                <a class="uc-post-viewed-title" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                            </li>
                            <?php
                        endwhile;
                        break;
                    case '1':
                        while ($query->have_posts()):
                            $query->the_post();
                            $post_id = get_the_ID();
                            $img_thumb = get_the_post_thumbnail($post_id, 'thumbnail', array("class" => "papular-posts-widg-thumbnail"));
                            $block_el = (has_post_thumbnail($post_id)) ? "" : "block-el";
                            ?>
                            <li>
                                <a class="uc-post-viewed-thumb-wrapper" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo $img_thumb; ?></a>
                                <a class="uc-post-viewed-title-wrapper <?php echo $block_el; ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                                    <p class="uc-post-viewed-title"><?php the_title(); ?></p>
                                </a>
                                <span class="uc-post-viewed-date"><?php the_date(); ?></span>
                            </li>
                            <?php
                        endwhile;
                        break;
                endswitch;
            }else {
                _e("No Post Was Viewed", "rng-isuc");
            }
            ?>
        </ul>
        <?php
        $output .= ob_get_clean();
        $output .= $args["after_widget"];
        echo $output;
    }

    /**
     * form admin panel widgt
     */
    public function form($instance) {
        //$instance = get value from admin panel fields
        //$this->get_field_id('FIELDNAME') = avoid id conflict
        //$this->get_field_name('FIELDNAME') = avoid name conflict
        $title = (!empty($instance['title'])) ? $instance['title'] : esc_html__("Last post viewed", "rng-isuc");
        $post_types = (!empty($instance['post_types']) and isset($instance['post_types'])) ? $instance['post_types'] : array('post');
        $posts_count = (!empty($instance['posts_count'])) ? $instance['posts_count'] : 4;
        $style = (!empty($instance['style'])) ? $instance['style'] : 0;

        global $rnguc_settings;
        $settings = $rnguc_settings->settings;
        $active_post_type = $settings['legal_pt'];
        $post_count = $settings['post_count'];
        ?>
        <p>
            <label><?php _e("Title", "rng-isuc"); ?></label>
            <input type="text" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" style="width: 100%;" name="<?php echo $this->get_field_name("title"); ?>" value="<?php echo $title; ?>">
        </p>
        <p>
            <label><?php _e("Select post types", "rng-isuc"); ?></label>
            <select id="<?php echo $this->get_field_id("post-types") ?>" multiple="" name="<?php echo $this->get_field_name("post_types"); ?>[]" style="width: 100%;">
                <?php
                foreach ($active_post_type as $post_type) {
                    $selected = (in_array($post_type, $post_types)) ? 'selected=""' : '';
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
                    <?php
                }
                ?>
            </select>
        </p>
        <p>
            <label><?php _e("Posts per page", "rng-isuc"); ?></label>
            <input type="number" id="<?php echo $this->get_field_id('posts-count'); ?>" style="width: 100%;" name="<?php echo $this->get_field_name('posts_count'); ?>" value="<?php echo $posts_count; ?>" max="<?php echo $post_count; ?>" />
        </p>
        <p>
            <label><?php _e("Select style", "rng-isuc"); ?></label>
            <select id="<?php echo $this->get_field_id("style"); ?>" style="width: 100%;" name="<?php echo $this->get_field_name("style") ?>">
                <option <?php echo ($style == 0) ? 'selected=""' : ''; ?> value="0"><?php _e("style1 (simple list)", "rng-isuc"); ?></option>
                <option <?php echo ($style == 1) ? 'selected=""' : ''; ?> value="1"><?php _e("style2 (with thumbnail)", "rng-isuc"); ?></option>
            </select>
        </p>
        <?php
    }

    /**
     * save admin panel fields in $instance
     */
    public function update($new_instance, $old_instance) {
//$old_instance = old instance
//$new_instance = new instance
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['post_types'] = $new_instance['post_types'];
        $instance['posts_count'] = $new_instance['posts_count'];
        $instance['style'] = $new_instance['style'];
        return $instance;
    }

}

/**
 * register widget main function
 */
function register_rnguc_posts_viewed_widget() {
    register_widget("rnguc_posts_viewed_widget");
}

add_action("widgets_init", "register_rnguc_posts_viewed_widget");
/*
*Constants*
1.*uc-post-viewed
2.*WIDGET_DESCRIPTION
3.rnguc_posts_viewed_widget
4.WIDGET_TITLE
5.OUTPUT_CONTENT
6.WIDGET_ID
*/

