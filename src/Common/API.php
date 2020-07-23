<?php
namespace MZ_Mobilize_America\Common;

use MZ_Mobilize_America as NS;
/*
 * The API Class
 *
 * @since 1.0.0
 * 
 * Makes request, referencing shortcode_atts, and holds retrieved data.
 * Provides methods to get links for next and previous.
 *
 * @usedby NS\Display
 *
 */
class API {

    /*
     * Next Page
     * 
     * @since 1.0.0
     *
     * @visibility public
     *
     * This is the next page retrieved from the API
     */
    private $next_page;
    
    /*
     * Previous Page
     * 
     * @since 1.0.0
     *
     * @visibility public
     *
     * Previous page retrieved from the API
     *
     */
    private $previous_page;
     
    
    /*
     * Shortcode Attributes
     *
     * @since 1.0.0
     * 
     * @visibility private
     */
    private $shortcode_atts;
    
    /*
     * Request Results
     *
     * @since 1.0.0
     * 
     * @visibility public
     */
    public $request_results;
    
    
    /*
     * Basic Restful Request
     *
     * @since 1.0.0
     * 
     *  
     */
    public function __construct($shortcode_atts = [], $api_result = 0){
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
		    $response_body = json_decode($response['body']);
		    $this->next_page = $response_body->next;
		    $this->previous_page = $response_body->previous;
		    return $response_body;
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
        
        $page = get_query_var('mobilize_page', 0);
        
        $defaults = [
            'organization_id' => !empty($this->shortcode_atts['organization_id']) ? $this->shortcode_atts['organization_id'] : $ma_options['organization_id'],
            'per_page' => !empty($this->shortcode_atts['per_page']) ? $this->shortcode_atts['per_page'] : $ma_options['per_page'],
            'page' => !empty($page) ? $page : ''
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
    
        $this->request_results = self::callApi($data);

        if (!empty($this->request_results->error)) {
            self::alert_admin(print_r($response->error, True));
            throw new Exception($response->error);
        } else if (!$this->request_results->count >= 1) {
            throw new Exception("Zero Count");
        }

        return $this->request_results;
        
    }
    
    /*
     * Add query vars
     * 
     * @since 1.0.0
     * 
     * @param $url_string, generally returned from Mobilize API
     * @return int the page referenced in the url's query string, or 0 if not present
     */
    public function get_page_query($url_string){
    
        $url_array = wp_parse_url($url_string);
        
        if (empty($url_array['query'])) return 0;
        
        $query_args = wp_parse_args($url_array['query']);

        if (empty($query_args['page'])) return 0;
        
        return $query_args['page'];
    }
    
    
    /*
     * Get Next Page URL
     * 
     * @since 1.0.0
     *
     * @return False or string url of with mobilize_page query string for subsequent listings
     */
    public function get_next(){
        $next_page_query = $this->get_page_query($this->next_page);
        if (False == $next_page_query) {
            return 0;
        }
        return add_query_arg('mobilize_page', $next_page_query, get_the_permalink());
    }
    
    /*
     * Get Previous Page URL
     * 
     * @since 1.0.0
     *
     * @return False or string url of with mobilize_page query string for previous listings
     */
    public function get_previous(){
        $prev_page_query = $this->get_page_query($this->previous_page);
        if (False == $prev_page_query) {
            return 0;
        }
        return add_query_arg('mobilize_page', $prev_page_query, get_the_permalink());
    }
    
    /*
     * Get Page Navigation
     * 
     * @since 1.0.0
     *
     * @return string HTML built from get_previous and get_next
     */
    public function get_navigation(){
        $return = '<div class="mobilize-nav" role="navigation">';
        $previous = $this->get_previous();
        $next = $this->get_next();
        if ($previous){
            $return .= '<a class="float-left" href="' . $previous . '">' . __("Previous", NS\PLUGIN_TEXT_DOMAIN) . '</a>';
        }
        if ($next){
            $return .= '<a class="float-right" href="' . $next . '">' . __("Next", NS\PLUGIN_TEXT_DOMAIN) . '</a>';
        }
        $return .= '</div>';
        return $return;
    }
}

?>
