<?php
namespace MZ_Mobilize_America\Common;

class API {

    /*
     * Basic Restful Request
     */
    private function callApi($method, $endpoint, $data = false) {

        $ma_options = get_option('mz_mobilize_america_settings');
        
        $curl = curl_init();
        
        $subdomain = $ma_options['use_staging'] == 'on' ? 'staging-api' : 'api';
        
        $url = 'https://' . $subdomain . '.mobilize.us/v1/' . $endpoint;
        
        $url = htmlentities($url);

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
     * Make Request
     *
     * @since 1.0.0
     * This is the static function through which this class is interfaced.
     * @param $method string GET, POST
     * @param $endpoint string 
     * @param $method string 
     */
    public static function make_request($method, $endpoint, $data = false) {
    
        $response = self::callApi($method, $endpoint, $data);
        
        $result = json_decode($response);
        
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
