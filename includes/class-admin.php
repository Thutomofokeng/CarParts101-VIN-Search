<?php
class CP101_VIN_Admin{
    public function __construct(){
        add_action('admin_init',[$this,'settings']);
        add_action('admin_menu',[$this,'menu']);
    }
    function menu(){
        add_options_page('CarParts101 VIN','CarParts101 VIN','manage_options','cp101-vin',[$this,'page']);
    }
    function settings(){
        register_setting('cp101_vin','cp101_vin_api_key');
        register_setting('cp101_vin','cp101_decodethis_api_key');
        register_setting('cp101_vin','cp101_vin_endpoint');
    }
    function page(){ ?>
    <div class="wrap"><h1>CarParts101 VIN</h1>
    <form method="post" action="options.php">
    <?php settings_fields('cp101_vin'); ?>
    <table class="form-table">
    <tr><th>API Endpoint</th><td><input style="width:100%" name="cp101_vin_endpoint" value="<?php echo esc_attr(get_option('cp101_vin_endpoint'));?>"></td></tr>
    <tr><th>API Key</th><td><input style="width:100%" name="cp101_vin_api_key" value="<?php echo esc_attr(get_option('cp101_vin_api_key'));?>"></td></tr>
    <tr><th>DecodeThis API Key</th><td><input style="width:100%" name="cp101_decodethis_api_key" value="<?php echo esc_attr(get_option('cp101_decodethis_api_key'));?>"></td></tr>
    </table><?php submit_button();?></form></div><?php }
}
