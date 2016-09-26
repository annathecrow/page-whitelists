<div class="wrap">
    <h1>Page Whitelists - settings</h1>
    <form method="POST" action="options.php">
        <?php settings_fields('wl_lists'); ?>
        <?php do_settings_sections('wl_lists'); ?>
        <?php submit_button(_x("Save settings",'page-whitelists')); ?>        
    </form>
</div>