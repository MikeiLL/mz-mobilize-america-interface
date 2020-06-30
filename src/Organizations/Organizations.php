<?php
namespace MZ_Mobilize_America\Organizations;

use MZ_Mobilize_America\ShortCode as ShortCode;
use MZ_Mobilize_America as NS;

class Organizations extends ShortCode\ShortCode_Script_Loader {

    static $addedAlready = false;

    public function handleShortcode($atts, $content = null) {

        $atts = shortcode_atts( array(
			'organization_id' => 1
				), $atts );

        // Add Style with script adder
        //self::addScript();
        //self::localizeScript($atts);        

        return "<pre>" . print_r($this->retrieve_organizations($atts), true) . "</pre>";
    }

    public function addScript() {
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            wp_register_script('mobilize_events_script', NS\PLUGIN_NAME_URL . 'inc/frontend/js/events.js', array('jquery'), 1.0, true );
 	        wp_enqueue_script('mobilize_events_script');
            wp_register_style( 'mobilize_events_style', NS\PLUGIN_NAME_URL . 'inc/frontend/css/events.css');
            wp_enqueue_style('mobilize_events_style');
        }
    }

    public static function localizeScript($atts = []) {

        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $nonce = wp_create_nonce( 'mobilize_america_events_nonce');
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
            'nonce' => $nonce,
            'atts' => $atts
            );
        wp_localize_script( 'mobilize_events_script', 'mobilize_america_events', $params);
    }

    /*
     * Basic Restful Request
     */
    private function CallAPI($method, $url, $data = false) {

        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    /*
     *
     *
     */
    private function retrieve_organizations($atts) {

		$organization_id = $atts['organization_id'];
        $now = new \DateTime(null, new \DateTimeZone('America/New_York'));


        // Allow One Day window to allow
        // for in-progress events
        $di = new \DateInterval('PT12H');
        $di->invert = 1;
        $now->add($di);

        $events_start_time_filter = $now->getTimestamp();

        $url_string = 'https://sandbox-api.mobilize.us/v1/organizations';
            
        $mobilize_url = htmlentities($url_string);

        $response = $this->CallAPI('GET', $mobilize_url);

        $result = json_decode($response);
        
        return $response;

        if (!empty($result->error)) {
            return array('API Error' => $result->error);
              $to = get_option('admin_email');
              $subject = __('Mobilize America API Error', 'mobilize-america');
              $message = __("There was an error returning events:", 'mobilize-america') . "\n" . print_r($result->error);
              wp_mail( $to, $subject,  $message, '', $attachments );
        } else if (!$result->count >= 1) {
            return array('zero' => 1);
        }

        return json_decode($response)->data;
    }

}

?>
