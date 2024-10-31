<?php

defined('ABSPATH') || exit;

class rnguc_isuc {

    /**
     * cookie name that keep last post viewed ids
     * @var String
     */
    public static $cookiename = 'uc_posts_viewed';

    /**
     * Query that show last post viewed in several template
     * @var Array
     */
    private $query_args;

    function __construct() {
        $this->query_args = $this->set_query_args();
        add_shortcode('isuc_posts_viewed', array($this, 'shortcode_posts_viewed'));
        add_action("template_redirect", array($this, "set_post_view"));
        if ($this->check_sidenav_postviewed()) {
            add_action("wp_footer", array($this, "show_sidenav_postviewed"));
        }
    }

    /**
     * return plugin settings from rnguc_settings class
     * @global Array $rnguc_settings
     * @return Array
     */
    public function get_uc_settings() {
        global $rnguc_settings;
        return $rnguc_settings->settings;
    }

    /**
     * check if sidenav post viewed is active or not
     * @return Boolean
     */
    public function check_sidenav_postviewed() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['side_nav'];
    }

    /**
     * return legal post type for view
     * @return Array
     */
    public function get_legal_post_type() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['legal_pt'];
    }

    /**
     * return posts_per_page for last post viewed
     * @return Integer
     */
    public function get_post_view_count() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['post_count'];
    }

    /**
     * view or track last post viewed
     * @return Boolean
     */
    public function get_post_view_flag() {
        $uc_settings = $this->get_uc_settings();
        return $uc_settings['flag'];
    }

    /*
     * check is current post is legal for showing in last post viewed or not
     * @return Boolean
     */

    public function is_legal_post_views($post_type) {
        extract($this->get_uc_settings());
        return (in_array($post_type, $legal_pt) and $flag);
    }

    /**
     * prepare query argument for show last post viewed
     * @return Array
     */
    public function get_query_args() {
        $this->remove_sigular_id($this->query_args['post__in']);
        return $this->query_args;
    }

    /**
     * get last post viewed ids from cookie
     * @return Array
     */
    public function get_postviewed_cookie() {
        $posts_viewed = (isset($_COOKIE[self::$cookiename]))? $_COOKIE[self::$cookiename] : false;
        if (!isset($posts_viewed)) {
            return array(0);
        }
        $post_viewed_array = (array) unserialize($posts_viewed);
        $post_viewed_array_integer = array_map("intval", $post_viewed_array);
        $post_viewed_array_unique = array_unique($post_viewed_array_integer);
        return array_filter($post_viewed_array_unique);
    }

    /**
     * set query args from cookie for view
     * @return Array
     */
    private function set_query_args() {
        $posts_viewed = (array) $this->get_postviewed_cookie();
        $this->check_post_view_count($posts_viewed);
        $posts_per_page = (int) min(count($posts_viewed), $this->get_post_view_count());
        $legal_pt = (array) $this->get_legal_post_type();
        $query_args = array(
            'order' => 'DESC',
            'post__in' => $posts_viewed,
            'post_type' => $legal_pt,
            'posts_per_page' => $posts_per_page
        );
        return $query_args;
    }

    /**
     * remove single template id in singular from post viewed ids
     * @param Array $posts_viewed
     */
    public function remove_sigular_id(&$posts_viewed) {
        $queried_object = get_queried_object();
        $current_id = (isset($queried_object))? intval($queried_object->ID) : 0;
        if (!is_singular() or ! in_array($queried_object->ID, $posts_viewed)) {
            return;
        }
        $index = array_search($current_id, $posts_viewed);
        unset($posts_viewed[$index]);
    }

    /**
     * show side nav post viewed
     */
    public function show_sidenav_postviewed() {
        ob_start();
        wp_enqueue_script("uc-last-post-viewed-sidenav");
        $query_args = $this->get_query_args();
        $params = array('query_args' => array(), 'has_posts' => FALSE);
        if (current($query_args['post__in']) !== 0) {
            $params['query_args'] = $query_args;
            $params['has_posts'] = TRUE;
        }
        rnguc_get_template("sidenav-postviewed.php", $params);
        $output = ob_get_clean();
        echo $output;
    }

    /**
     * check if can set post viewed id in cookie
     * @param String $post_type
     * @return Boolean
     */
    public function set_post_view_permissin($post_type) {
        $is_legal_post_views = $this->is_legal_post_views($post_type);
        return (is_singular() and ! is_admin() and ! current_user_can("edit_posts") and $is_legal_post_views);
    }

    /**
     * set post viewed id to cookie
     * @global Object $post
     */
    function set_post_view() {
        global $post;
        $post_id = $post->ID;
        $post_type = $post->post_type;
        $posts_viewed = $this->get_postviewed_cookie();

        if (!$this->set_post_view_permissin($post_type) or in_array($post_id, $posts_viewed)) {
            return;
        }

        $cookie_name = self::$cookiename;
        $this->update_post_views($post_id, $cookie_name, $posts_viewed);
    }

    /**
     * insert current post viewed id in post viewed ids
     * @param Integer $post_id
     * @param String $cookie_name
     * @param Array $posts_viewed
     */
    function update_post_views($post_id, $cookie_name, $posts_viewed) {

        if (empty($posts_viewed)) {
            $this->remove_cookie($cookie_name);
            setcookie($cookie_name, serialize(array($post_id)), time() + YEAR_IN_SECONDS, "/");
            return;
        }

        array_unshift($posts_viewed, $post_id);
        $this->check_post_view_count($posts_viewed);


        setcookie($cookie_name, serialize($posts_viewed), time() + YEAR_IN_SECONDS, "/");
    }

    /**
     * restrict post viewed to post viewed count in setting panel
     * @param Array $posts_viewed
     */
    function check_post_view_count(&$posts_viewed) {
        $post_count = $this->get_post_view_count();
        while (count($posts_viewed) > $post_count) {
            array_pop($posts_viewed);
        }
    }

    /**
     * remove cookie by name
     * @param String $cookie_name
     */
    function remove_cookie($cookie_name) {
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
    }

    /**
     * show post viewed as shortcode
     * @return String
     */
    function shortcode_posts_viewed() {
        $posts = array();
        $query_args = $this->get_query_args();
        if (!empty($query_args)) {
            $posts = get_posts($query_args);
        }
        ob_start();
        rnguc_get_template("product-viewed.php", array('posts' => $posts));
        $outpout = ob_get_clean();
        return $outpout;
    }

}

global $rnguc_isuc;
$rnguc_isuc = new rnguc_isuc();
