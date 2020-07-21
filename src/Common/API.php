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
    public function __construct($shortcode_atts = []){
        $this->shortcode_atts = $shortcode_atts;
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
    private function callApi($data = false) {

        $ma_options = get_option('mz_mobilize_america_settings');
        		                
        $subdomain = $ma_options['use_staging'] == 'on' ? 'staging-api' : 'api';
        
        $endpoint = $this->shortcode_atts['endpoint'];

        $url = 'https://' . $subdomain . '.mobilize.us/v1/' . $endpoint;
        
        $query_array = $this->parse_query_string();

        switch($endpoint):
            case "ogranizations":
                $method = 'GET';
                break;
            case "events":
                $method = 'GET';
                $now = new \DateTime(null, new \DateTimeZone('America/New_York'));
                // Allow One Day window to allow
                // for in-progress events
                $di = new \DateInterval('PT12H');
                $di->invert = 1;
                $now->add($di);
                $events_start_time_filter = $now->getTimestamp();
                $query_array['timeslot_start'] = 'gte_' . $events_start_time_filter;

                break;
        endswitch;
        
        $url = (!empty($query_array)) ? $url . '?' . http_build_query($query_array) : $url;
        
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
     * Parse Query String 
     * @since 1.0.0
     *
     * If query_string present, parse into a usable array, merge with other shortcodes, build query;
     * @return query string
     */
     private function parse_query_string(){
        $query_string = $this->shortcode_atts['query_string'];
                
        //remove question mark if present
        if (substr($query_string, 0, 1) == '?') {
            $query_string = substr($query_string, 1);
        } 
        
        $ma_options = get_option('mz_mobilize_america_settings');
        
        $defaults = [
            'organization_id' => !empty($this->shortcode_atts['organization_id']) ? $this->shortcode_atts['organization_id'] : $ma_options['organization_id'],
            'per_page' => !empty($this->shortcode_atts['per_page']) ? $this->shortcode_atts['per_page'] : $ma_options['per_page']
        ];
        
        // Unset empty default values
        foreach($defaults as $k => $v) {
            if (empty($v)) unset($defaults[$k]);
        }
        
        if (!empty($query_string)){
            $query_array = wp_parse_args($query_string, $defaults);
        } else {
            $query_array = $defaults;
        }
        
        return $query_array;
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
    public function make_request($data = false) {
    
        $response = self::callApi($data);

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
