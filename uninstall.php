<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

//delte options
$options = array(
    //settings
    "isuc_settings",
    "isuc_configration_dissmiss",
    //widget
    "widget_rnguc-post-viewed"
);
foreach ($options as $option) {
    if (get_option($option)) {
        delete_option($option);
    }
}