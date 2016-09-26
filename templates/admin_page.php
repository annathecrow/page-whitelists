<div class="wrap">
    <h1>Page Whitelists - <?php _e('settings','page-whitelists') ?></h1>
    <form method="POST" action="options.php">
        <?php settings_fields('wl_lists'); ?>
        <?php do_settings_sections('wl_lists'); ?>
        <?php submit_button(__("Save settings",'page-whitelists')); ?>        
    </form>
</div>