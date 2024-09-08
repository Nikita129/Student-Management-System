<form method="post" action="options.php">
    <?php 
        settings_fields("sms_plugin_options"); 

        do_settings_sections("sms-plugin-settings");

        submit_button("Save SMS Settings"); 
    ?>            
</form>    