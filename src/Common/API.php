<?php
namespace MZ_Mobilize_America\Common;

/* 
 * API Error Object
 * @since 1.0.0
 *
 * Hold and display information about errors calling the api
 */
class ApiError {
    public $message = "";
    
    public function __construct($message){
        $this->message = $message;
    }
}

class API {
     
    
    /*
     * Shortcode Attributes
     * @since 1.0.0
     * 
     * @visibility private
     */
    private $shortcode_atts;
    
    
    /*
     * Basic Restful Request
     * @since 1.0.0
     * 
     *  
     */
    public function __construct__($atts = []){
        $this->shortcode_atts = $atts;
    }
    
    
    
    /*
     * Basic Restful Request
     * @since 1.0.0
     * 
     * Make the API call using wordpress wp_remote_post.
     *
     * @param $method string GET, POST, DELETE
     * @param $endpoint string 
     * @param $data string 
     * @param $query_string string 
     */
    private function callApi($method, $endpoint, $data = false) {

        $ma_options = get_option('mz_mobilize_america_settings');
        
		$organization_id = $ma_options['organization_id'];
		
		$query_string = $this->shortcode_atts;
                
        $subdomain = $ma_options['use_staging'] == 'on' ? 'staging-api' : 'api';
        
        $url = 'https://' . $subdomain . '.mobilize.us/v1/' . $endpoint;
        
        switch($endpoint):
            case "events":
                $query_string .= 'organization_id=' . $organization_id;
                

                $now = new \DateTime(null, new \DateTimeZone('America/New_York'));
                // Allow One Day window to allow
                // for in-progress events
                $di = new \DateInterval('PT12H');
                $di->invert = 1;
                $now->add($di);
                $events_start_time_filter = $now->getTimestamp();
                $query_string .= 'timeslot_start=gte_' . $events_start_time_filter;
                $query_string .= '&per_page=10';

                break;
        endswitch;
        
        $url = (!empty($query_string)) ? $url . '?' . $query_string : $url;
        
        $url = htmlentities($url);
		
		$response = wp_remote_post( $url, 
			array(
				'method' => $method,
				'timeout' => 45,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => '',
				'body' => $data,
				'data_format' => 'body',
				'cookies' => array()
			) );
	    
	    if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			self::alert_admin($error_message);
			return "Error: " . $error_message;
		} else {
		    return json_decode($response['body']);
		}	
    }
    
    /*
     * Alert Admin
     *
     */
    private function alert_admin($message) {
        
        $to = get_option('admin_email');
        $subject = __('Mobilize America API Error', 'mobilize-america');
        
        wp_mail( $to, $subject, $message, '');

    }
    
    /*
     * Make Request
     *
     * @since 1.0.0
     * 
     * This is the static function through which this class is interfaced.
     * @param $method string GET, POST, DELETE
     * @param $endpoint string 
     * @param $data string 
     * @param $query_string string 
     */
    public function make_request($method, $endpoint, $data = false) {
    
        $response = self::callApi($method, $endpoint, $data);

        if (!empty($response->error)) {
            return new ApiError($response->error);
            self::alert_admin(print_r($response->error, True));
        } else if (!$response->count >= 1) {
            return new ApiError("Zero Count");
        }

        return $response;
        
    }
}

?>
