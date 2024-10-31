<?php

/*
  Plugin Name: rng-isuc
  Description: WordPress Plugin that shows last post viewed by user in several viewes like widget, shortcode and sidebar navigation
  Version: 1.0
  Author: Abolfazl Sabagh
  Author URI: http://asabagh.ir
  License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define("RNGUC_FILE", __FILE__);
define("RNGUC_PRU", plugin_basename(__FILE__));
define("RNGUC_PDU", plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-isuc
define("RNGUC_PRT", basename(__DIR__));          //rng-isuc.php
define("RNGUC_PDP", plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-isuc
define("RNGUC_TMP", RNGUC_PDP . "/public/");     //view OR templates directory for public
define("RNGUC_ADM", RNGUC_PDP . "/admin/");      //view OR templates directory for admin panel
define("RNGUC_PLUGIN_PATH", plugin_dir_path(__FILE__));

/**
 * locate template file
 * @param String $template_name
 * @param String $template_path
 * @param String $default_path
 * @return String
 */
function rnguc_locate_template($template_name, $template_path, $default_path) {
    if (!$template_path) {
        $template_path = "rng-isuc/";
    }
    if (!$default_path) {
        $default_path = RNGUC_PLUGIN_PATH . "templates/";
    }
    $template = locate_template(array($template_path . $template_name, $template_name));
    if (empty($template)) {
        $template = $default_path . $template_name;
    }
    return apply_filters("custom_locate_template", $template, $template_name, $template_path, $default_path);
}

/**
 * require template file with this periority :
 * 1.in active theme templates
 * 2.in current plugin
 * @param String $template_name
 * @param Array $args
 * @param String $template_path
 * @param String $default_path
 */
function rnguc_get_template($template_name, $args = "", $template_path = "", $default_path = "") {
    if (is_array($args) and isset($args)) {
        extract($args);
    }
    $template_file = rnguc_locate_template($template_name, $template_path, $default_path);
    if (!file_exists($template_file)):
        error_log("File with name of {$template_file} is not exist");
        return;
    endif;
    include $template_file;
}

require_once 'includes/class.init.php';
new init(1.0, "rng-isuc");
