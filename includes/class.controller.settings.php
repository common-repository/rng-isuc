<?php

defined('ABSPATH') || exit;

class rnguc_settings {

    /**
     * plugin general settings
     * @var Array
     */
    public $settings = array();

    public function __construct() {
        $this->settings = $this->get_uc_settings();
        if (is_admin()) {
            add_action("admin_menu", array($this, "admin_menu"));
            add_action("admin_init", array($this, "general_settings_init"));
            add_action("admin_notices", array($this, "configure_notices"));
            add_action("admin_init", array($this, "dismiss_configuration"));
            add_filter('plugin_action_links_' . RNGUC_PRU, array($this, 'add_setting_link'));
        }
    }

    /**
     * get plugin settings
     * @return array
     */
    public function get_uc_settings() {
        $uc_settings_array = array(
            'legal_pt' => array('post'),
            'side_nav' => true,
            'post_count' => 10,
            'flag' => true
        );

        $uc_settings = get_option("isuc_settings");
        if (empty($uc_settings)) {
            return $uc_settings_array;
        }

        $uc_settings_array['legal_pt'] = (array) $uc_settings['legal_pt'];
        $uc_settings_array['side_nav'] = ((string) $uc_settings['side_nav'] == 'yes') ? true : false;
        $uc_settings_array['post_count'] = (int) $uc_settings['post_count'];
        $uc_settings_array['flag'] = ((string) $uc_settings['flag'] == 'yes') ? true : false;
        return $uc_settings_array;
    }

    /**
     * add last post viewed setting menu under the settings menu
     */
    public function admin_menu() {
        add_submenu_page("options-general.php", esc_html__("Last posts viewed", "rng-isuc"), esc_html__("Last posts viewed", "rng-isuc"), "administrator", "isuc-settings", array($this, "isuc_settings"));
    }

    /**
     * return setting panel .
     * it callback for add_submenu_page
     */
    public function isuc_settings() {
        include RNGUC_ADM . "settings-panel.php";
    }

    /**
     * register general setting
     */
    public function general_settings_init() {
        register_setting("isuc-settings", "isuc_settings");
        add_settings_section("isuc-settings-top", esc_html__("General settings", "rng-isuc"), array($this, "general_settings"), "isuc-settings");
        add_settings_field("uc-pv-cookie", esc_html__("Set user post viewed", "rng-isuc"), array($this, "general_settings_flag"), "isuc-settings", "isuc-settings-top", array('id' => 'uc-flag', 'name' => 'flag'));
        add_settings_field("isuc-settings-side-view", esc_html__("Show Side Nav post viewed", "rng-isuc"), array($this, "general_settings_side_nav"), "isuc-settings", "isuc-settings-top", array("id" => "uc-side-nav", "name" => "side_nav"));
        add_settings_field("isuc-settings-legal-pt", esc_html__("Permission", "rng-isuc"), array($this, "general_settings_legal_pt"), "isuc-settings", "isuc-settings-top", array("id" => "uc-legal-pt", "name" => "legal_pt"));
        add_settings_field("isuc-settings-post-count", esc_html__("Post Count", "rng-isuc"), array($this, "general_settings_post_count"), "isuc-settings", "isuc-settings-top", array("id" => "uc-post-count", "name" => "post_count"));
    }

    /**
     * general setting top section description
     */
    public function general_settings() {
        _e("General Settings of rng-isuc WP Plugin. At The First Please select Post types.", "rng-isuc");
    }

    /**
     * register flag setting
     * callback for add_settings_field
     * @param Array $args
     */
    public function general_settings_flag($args) {
        $flag = $this->settings['flag'];
        ?>
        <select id='<?php echo $args['id']; ?>' name='isuc_settings[<?php echo $args['name']; ?>]'>
            <option <?php echo ($flag) ? "selected" : ""; ?> value='yes'><?php _e("Yes", "rng-isuc"); ?></option>
            <option <?php echo ($flag) ? "" : "selected"; ?> value='no'><?php _e("No", "rng-isuc"); ?></option>
        </select>
        <?php
    }

    /**
     * register legal post types setting
     * callback for add_settings_field
     * @param Array $args
     */
    public function general_settings_legal_pt($args) {
        $active_post_type = get_option("isuc_settings");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['legal_pt'];
        }
        $pt_args = array('public' => TRUE);
        $post_types = get_post_types($pt_args, 'names');
        $key = array_search("attachment", $post_types);
        unset($post_types[$key]);
        foreach ($post_types as $post_type):
            if (is_array($active_post_type)) {
                $checked = (in_array($post_type, $active_post_type)) ? "checked" : "";
            } else {
                $checked = '';
            }
            ?>
            <label>
                <?php echo $post_type ?>&nbsp;<input id="<?php echo $args['id']; ?>" type="checkbox" name="isuc_settings[<?php echo $args['name']; ?>][]" <?php echo $checked; ?> value="<?php echo $post_type; ?>" >
            </label>
            <br>
            <?php
        endforeach;
    }

    /**
     * register side nav setting switch
     * callback for add_settings_field
     * @param Array $args
     */
    public function general_settings_side_nav($args) {
        $flag = $this->settings['side_nav'];
        ?>
        <select id='<?php echo $args['id']; ?>' name='isuc_settings[<?php echo $args['name']; ?>]'>
            <option <?php echo ($flag) ? "selected" : ""; ?> value='yes'><?php _e("Yes", "rng-isuc"); ?></option>
            <option <?php echo ($flag) ? "" : "selected"; ?> value='no'><?php _e("No", "rng-isuc"); ?></option>
        </select>
        <?php
    }

    /**
     * register post count setting
     * callback for add_settings_field
     * @param Array $args
     */
    public function general_settings_post_count($args) {
        $post_count = $this->settings['post_count'];
        ?>
        <input type="number" id="<?php echo $args['id']; ?>" name="isuc_settings[<?php echo $args['name']; ?>]" value="<?php echo $post_count; ?>" min="1" max="20" >
        <?php
    }

    /**
     * add notice for configuration in admin panel after installing plugin
     */
    public function configure_notices() {
        $dismiss = get_option("isuc_configration_dissmiss");
        if (!$dismiss) {
            $notice = '<div class="updated"><p>' . esc_html__('rng-isuc is activated, you may need to configure it to work properly.', 'rng-isuc') . ' <a href="' . admin_url('options-general.php?page=isuc-settings') . '">' . esc_html__('Go to Settings page', 'rng-isuc') . '</a> &ndash; <a href="' . add_query_arg(array('uc_dismiss_notice' => 'true', 'uc_nonce' => wp_create_nonce("uc_dismiss_nonce"))) . '">' . esc_html__('Dismiss', 'rng-isuc') . '</a></p></div>';
            echo $notice;
        }
    }

    /**
     * dismiss configuration notices
     */
    public function dismiss_configuration() {
        if (isset($_GET['uc_dismiss_notice']) and $_GET['uc_dismiss_notice'] = 'true' and ( isset($_GET['uc_nonce']))) {
            $verify_nonce = wp_verify_nonce($_GET['uc_nonce'], 'uc_dismiss_nonce');
            if ($verify_nonce) {
                update_option("isuc_configration_dissmiss", 1);
            }
        } elseif (isset($_GET['page']) and $_GET['page'] == "isuc-settings") {
            update_option("isuc_configration_dissmiss", 1);
        }
    }

    /**
     * add setting link for my plugin in plugin list screen
     * @param Array $links
     * @return Array
     */
    public function add_setting_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('options-general.php?page=isuc-settings') . '">' . esc_html__("Settings", "rng-isuc") . '</a>',
        );
        return array_merge($links, $mylinks);
    }

}

global $rnguc_settings;
$rnguc_settings = new rnguc_settings();
