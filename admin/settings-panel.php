<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!current_user_can("manage_options")){
    return;
}
    
if (isset($_GET['settings-updated']) and $_GET['settings-updated'] == TRUE) {
    add_settings_error("isuc-settings", "isuc-settings", esc_html__("Settings Updated", "rng-isuc"), "updated");
} elseif (isset($_GET['settings-updated']) and $_GET['settings-updated'] == FALSE) {
    add_settings_error("isuc-settings", "isuc-settings", esc_html__("Error with Updating", "rng-isuc"));
}
?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields("isuc-settings");
        do_settings_sections("isuc-settings");
        submit_button(esc_html__("save", "rng-isuc"));
        ?>
    </form>
</div>