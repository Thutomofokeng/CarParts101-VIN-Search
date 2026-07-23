<?php

class CP101_VIN_Shortcode {

    public function __construct() {
        add_shortcode('cp101_vin_search', [$this, 'render']);
    }

    private function wmi_lookup($vin)
{
    $wmi = strtoupper(substr($vin, 0, 3));

    $json_file = plugin_dir_path(__FILE__) . 'wmi-map.json';

    if (!file_exists($json_file)) {
        return 'Unknown';
    }

    $json = file_get_contents($json_file);
    $map = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'Unknown';
    }

    return $map[$wmi] ?? null;
}


    private function decode_vin($vin) {
        $endpoint='https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValues/';
        $url=$endpoint.rawurlencode($vin).'?format=json';
        $response=wp_remote_get($url,['timeout'=>20]);
        if(is_wp_error($response)){ return $response; }
        $json=json_decode(wp_remote_retrieve_body($response),true);
        if(empty($json['Results'][0])){
            return new WP_Error('vin_error','Unable to decode VIN.');
        }
        $result=$json['Results'][0];
        if(empty($result['Make'])){$w=$this->wmi_lookup($vin); if($w){$result['Make']=$w;}}
        if((empty($result['Model'])||empty($result['EngineModel'])) && ($key=get_option('cp101_decodethis_api_key'))){
$url='https://decodethis.com/api/v1/vins/'.rawurlencode($vin).'/decode';
$r=wp_remote_get($url,['timeout'=>20,'headers'=>['Authorization'=>'Bearer '.$key,'Accept'=>'application/json']]);
if(!is_wp_error($r)){
$b=json_decode(wp_remote_retrieve_body($r),true);
if(!empty($b['success'])&&!empty($b['data'])){
$d=$b['data'];
$map=['make'=>'Make','model'=>'Model','year'=>'ModelYear','trim'=>'Trim','engine'=>'EngineModel','transmission'=>'TransmissionStyle','fuel'=>'FuelTypePrimary','body'=>'BodyClass'];
foreach($map as $src=>$dst){if(empty($result[$dst])&&!empty($d[$src])){$result[$dst]=$d[$src];}}
}
}
}
        return $result;
    }

    public function render() {
        ob_start(); ?>
<style>
.cp101-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;margin-top:20px}.cp101-card{border:1px solid #ddd;border-radius:8px;padding:15px;text-align:center;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.06)}.cp101-card img{width:220px;height:220px;object-fit:contain;margin:auto;display:block}.cp101-title{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:3em;font-weight:600;margin:12px 0}.cp101-btn{display:inline-block;padding:10px 16px;background:#F7931E;color:#fff !important;text-decoration:none;border-radius:6px;border:1px solid #F7931E;font-weight:600}.cp101-btn:hover{background:#d97d0d;border-color:#d97d0d;color:#fff !important}</style>

<h1>Find the Right Part. First Time.</h1>
<p>Enter your 17-character VIN and we'll identify your vehicle before showing compatible parts.</p>
<form method="post">
<input type="text" name="cp101_vin" maxlength="17" minlength="17" required placeholder="Enter VIN" value="<?php echo isset($_POST['cp101_vin'])?esc_attr($_POST['cp101_vin']):''; ?>">
<button type="submit">Find Parts</button>
</form>
<?php
if(!empty($_POST['cp101_vin'])){
 $vin=strtoupper(sanitize_text_field($_POST['cp101_vin']));
 if(strlen($vin)!=17){
   echo '<p style="color:red;">Please enter a valid 17-character VIN.</p>';
 } else {
   $db = new CP101_Vehicle_Database();

$vehicle = $db->find_vehicle($vin);

if (!$vehicle) {

    // Fall back to NHTSA
    $vehicle = $this->decode_vin($vin);

    if (is_wp_error($vehicle)) {
        echo '<p style="color:red;">' . esc_html($vehicle->get_error_message()) . '</p>';
    } else {

        echo '<h2>Vehicle Identified</h2><table class="widefat striped">';

        $fields = [
            'Make'         => 'Make',
            'Model'        => 'Model',
            'Model Year'   => 'ModelYear',
            'Series'       => 'Series',
            'Trim'         => 'Trim',
            'Body Class'   => 'BodyClass',
            'Engine'       => 'EngineModel',
            'Fuel Type'    => 'FuelTypePrimary',
            'Drive Type'   => 'DriveType',
            'Transmission' => 'TransmissionStyle'
        ];

        foreach ($fields as $label => $key) {
            if (!empty($vehicle[$key])) {
                echo '<tr><th>' . esc_html($label) . '</th><td>' . esc_html($vehicle[$key]) . '</td></tr>';
            }
        }

        echo '</table>';
    }

} else {

    // Local JSON database

    echo '<h2>Vehicle Identified</h2><table class="widefat striped">';

    $fields = [
        'Make'       => 'make',
        'Model'      => 'model',
        'Model Year' => 'year',
        'Plant'      => 'plant',
        'Engine'     => 'engine',
        'Body'       => 'body',
        'Drive'      => 'drive'
    ];

    foreach ($fields as $label => $key) {
        if (!empty($vehicle[$key])) {
            echo '<tr><th>' . esc_html($label) . '</th><td>' . esc_html($vehicle[$key]) . '</td></tr>';
        }
    }

    echo '</table>';
}
if(class_exists('WooCommerce')){
 echo '<h2>Compatible Parts</h2><div class="cp101-grid">';
 $make  = $vehicle['Make'] ?? $vehicle['make'] ?? '';
$model = $vehicle['Model'] ?? $vehicle['model'] ?? '';

$search = trim($make . ' ' . $model);
 $args=[
 'post_type'=>'product',
 'posts_per_page'=>24,
 's'=>$search
 ];
 $q=new WP_Query($args);
 if(!$q->have_posts() && !empty($vehicle['Make'])){
   $args['s']=$vehicle['Make'];
   $q=new WP_Query($args);
 }
 while($q->have_posts()){ $q->the_post(); global $product;
 echo '<div class="cp101-card">';
 echo get_the_post_thumbnail(get_the_ID(),'medium');
 echo '<div class="cp101-title"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
 if($product){echo '<div>'.$product->get_price_html().'</div>';}
 echo '<p><a class="cp101-btn" href="'.get_permalink().'">View Product</a></p></div>';
 }
 wp_reset_postdata();
 echo '</div>';
}

   }
return ob_get_clean();
    }
}
}
