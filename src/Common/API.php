<?php
namespace MZ_Mobilize_America\Common;

use MZ_Mobilize_America as NS;

use \Exception as Exception;

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
     * @visibility private
     *
     * This is the next page retrieved from the API
     */
    private $next_page;
    
    /*
     * Previous Page
     * 
     * @since 1.0.0
     *
     * @visibility private
     *
     * Previous page retrieved from the API
     *
     */
    private $previous_page;
    
    /*
     * Pagination Details
     * 
     * @since 1.0.0
     *
     * @visibility public
     *
     * @array of details about pagination for current request
     *
     */
    public $pagination_details;
     
    
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
     * Current Page via Query
     *
     * @since 1.0.0
     * 
     * @visibility public
     */
    public $current_page_via_query;
    
    
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
        
        $organization_id = !empty($this->shortcode_atts['organization_id']) ? $this->shortcode_atts['organization_id'] : $ma_options['organization_id'];
        
        $helpers = new Helpers;
        if ( ($endpoint == 'events') && ($this->shortcode_atts['other_orgs'] == 1) ){
            $url = 'https://' . $subdomain . '.mobilize.us/v1/organizations/' .$organization_id . '/' . $endpoint;
        } else {
            $url = 'https://' . $subdomain . '.mobilize.us/v1/' . $endpoint;
        }
        
        $query_array = $this->parse_query_string(); // where other attributes are added

        switch($endpoint):
            case "ogranizations":
                $method = 'GET';
                break;
            case "events":
                $method = 'GET';
                //$timezone = \wp_timezone();
                //$now = new \DateTime(null, $timezone);
                $now = new \DateTime(null, new \DateTimeZone( $this->wp_timezone_string() ));
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
        
		$response = wp_safe_remote_post( $url, 
			array(
				'method' => $method,
				'timeout' => 45,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => '',
                'body' => json_encode($data),
				'data_format' => 'body',
				'headers'     => [
                    'Content-Type' => 'application/json',
                ],
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
     * My WP Timezone String 
     * @since 1.0.0
     *
     * If query_string present, parse into a usable array, merge with other shortcodes, build query;
     * @return array of query parameters
     */
    private function wp_timezone_string() {
        $timezone_string = get_option( 'timezone_string' );
 
        if ( $timezone_string ) {
            return $timezone_string;
        }
 
        $offset  = (float) get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - $hours );
 
        $sign      = ( $offset < 0 ) ? '-' : '+';
        $abs_hour  = abs( $hours );
        $abs_mins  = abs( $minutes * 60 );
        $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
 
        return $tz_offset;
    }
    
    /*
     * Parse Query String 
     * @since 1.0.0
     *
     * If query_string present, parse into a usable array, merge with other shortcodes, build query;
     * @return array of query parameters
     */
     private function parse_query_string(){
        $query_string = $this->shortcode_atts['query_string'];
                
        //remove question mark if present
        if (substr($query_string, 0, 1) == '?') {
            $query_string = substr($query_string, 1);
        } 
        
        $ma_options = get_option('mz_mobilize_america_settings');
        
        $this->current_page_via_query = get_query_var('mobilize_page', 0);
        
        $defaults = [
            'per_page' => !empty($this->shortcode_atts['per_page']) ? $this->shortcode_atts['per_page'] : $ma_options['per_page'],
            'page' => !empty($this->current_page_via_query) ? $this->current_page_via_query : ''
        ];
        
        if ($this->shortcode_atts['other_orgs'] != 1){
            $defaults['organization_id'] = !empty($this->shortcode_atts['organization_id']) ? $this->shortcode_atts['organization_id'] : $ma_options['organization_id'];
        }
        
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
        $subject = __('Mobilize America API Error', 'mz-mobilize-america');
        
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
        
        // Does it make sense to Sanitize API Request Results here?
        $this->sanitize_results();
        
        return $this->request_results;
        
    }
    
    private function sanitize_results() {
    
        $this->request_results->data = array_map(function ($item){
            $item->title = sanitize_text_field($item->title);
            $item->description = htmlentities($item->description);
            $item->featured_image_url = esc_url($item->featured_image_url);
            
            if (!empty($item->timeslots)){
                $item->timeslots = array_map(function($slot){
                    $slot->instructions = htmlentities($slot->instructions);
                    return $slot;
                }, $item->timeslots);
            }
            
            $item->summary = htmlentities($item->summary);
            $item->accessibility_notes = htmlentities($item->accessibility_notes);
            $item->virtual_action_url = esc_url($item->virtual_action_url);
            $item->browser_url = esc_url($item->browser_url);
            $item->event_type = sanitize_text_field($item->event_type);
            $item->approval_status = sanitize_text_field($item->approval_status);
            $item->location = sanitize_text_field($item->location);
            
            if (!empty($item->sponsor)) {
                $item->sponsor->state = sanitize_text_field($item->sponsor->state);
                $item->sponsor->org_type = sanitize_text_field($item->sponsor->org_type);
                $item->sponsor->district = sanitize_text_field($item->sponsor->district);
                $item->sponsor->name = sanitize_text_field($item->sponsor->name);
                $item->sponsor->candidate_name = sanitize_text_field($item->sponsor->candidate_name);
                $item->sponsor->race_type = sanitize_text_field($item->sponsor->race_type);
                $item->sponsor->event_feed_url = esc_url($item->sponsor->event_feed_url);
                $item->sponsor->slug = sanitize_title($item->sponsor->slug);
            }
            
            $item->visibility = sanitize_text_field($item->visibility);
            $item->address_visibility = sanitize_text_field($item->address_visibility);
            $item->event_campaign = sanitize_text_field($item->event_campaign);
            $item->timezone = sanitize_text_field($item->timezone);
            $item->instructions = htmlentities($item->timezone);
            
            if (!empty($item->tags)) {
                $item->tags->name = sanitize_text_field($item->tags->name);
            }
            
            return $item;
            
        }, $this->request_results->data);

        return $this->request_results;
    }
    
    /*
     * Add query vars
     * 
     * @since 1.0.0
     * 
     * @param $url_string, generally returned from Mobilize API
     * @param $arg which query argument to return
     * @return int the page referenced in the url's query string, or 0 if not present
     */
    public function get_query($url_string, $arg = 'page'){
    
        $url_array = wp_parse_url($url_string);
        
        if (empty($url_array['query'])) return 0;
        
        $query_args = wp_parse_args($url_array['query']);

        if (empty($query_args[$arg])) return 1;
        
        return $query_args[$arg];
    }
    
    
    /*
     * Get Next Page URL
     * 
     * @since 1.0.0
     *
     * @return False or string url of with mobilize_page query string for subsequent listings
     */
    public function get_next(){
        $next_page_query = $this->get_query($this->next_page, 'page');
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
        $prev_page_query = $this->get_query($this->previous_page, 'page');
        if (False == $prev_page_query) {
            return 0;
        }
        return add_query_arg('mobilize_page', $prev_page_query, get_the_permalink());
    }
    
    /*
     * Get Step Navigation
     * 
     * @since 1.0.0
     *
     * @return string HTML built from get_previous and get_next
     */
    public function get_step_navigation(){
        $return = '<div class="mobilize-step-nav" role="navigation">';
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
    
    /*
     * Get Numeric Navigation
     * 
     * @since 1.0.0
     *
     * @return string HTML with numeric data pagination links
     */
    public function get_numeric_navigation(){
        // TODO display a limited number of pages with navigation.
        $this->pagination_details = $this->get_segment_info();
        $return = '<ul class="mobilize-numeric-nav" role="navigation">';
        if ($this->pagination_details['number_of_pages'] <= 3) return;
        foreach (range(1, $this->pagination_details['number_of_pages']) as $page) {
            if ($this->current_page_via_query == $page) {
                $return .= '<li class="nav-item-' . $page .'"><span class="current_page">' . $page . '</span></li>';
            } else {
                $return .= '<li class="nav-item-' . $page .'"><a class="inactive" href="' . add_query_arg('mobilize_page', $page, get_the_permalink()) .'">' . $page . '</a></li>';
            }
            
        }
        $return .= '</ul>';
        return $return;
    }
    
    
    /*
     * Current Page
     * 
     * @since 1.0.0
     *
     * @return int Current Page
     */
    public function current_page(){
        if (empty($this->previous_page)){
            return 1;
        }
        $prev_page_query = $this->get_query($this->previous_page, 'page');
        if (empty($prev_page_query)){
            return 1;
        } else {
            return $prev_page_query + 1;
        }
        return 0;
    }
    
    /*
     * Results Per Page
     * 
     * @since 1.0.0
     *
     * @return int Requested per page count or the default which is 25
     */
    public function results_per_page(){
        $per_page = $this->parse_query_string()['per_page'];
        return (!empty($per_page)) ? $per_page : 25;
    }
    
    
    /*
     * Get Segment Info
     * 
     * @since 1.0.0
     * 
     *
     * @return array containing information about the segment returned of total request results
     */
    public function get_segment_info(){
        $total_results = $this->request_results->count;
        $current_result_count = count($this->request_results->data);
        $current_page = $this->current_page();
        $current_segment_start = $current_result_count * $current_page - ($current_result_count - 1); 
        $current_segment_end = $current_result_count * $current_page; 
        $number_of_pages = ceil($total_results / $this->results_per_page()); 
        return [
            'current_segment_start' => $current_segment_start,
            'current_segment_end' => $current_segment_end,
            'total_results' => $total_results,
            'current_page' => $current_page, 
            'number_of_pages' => $number_of_pages
        ];;
    }
    
    
    /*
     * Display Segment Info
     * 
     * @since 1.0.0
     * 
     *
     * @return array containing information about the segment returned of total request results
     */
    public function display_segment_info(){
        $this->pagination_details = $this->get_segment_info();
        return sprintf(__("Results %1d - %2d of %3d.", NS\PLUGIN_TEXT_DOMAIN), $this->pagination_details['current_segment_start'], $this->pagination_details['current_segment_end'], $this->pagination_details['total_results']);
    }
    
    /*
     * Display Pagination Info
     * 
     * @since 1.0.0
     * 
     *
     * @return array containing information about the segment returned of total request results
     */
    public function display_pagination_info(){
        $this->pagination_details = $this->get_segment_info();
        if ($this->pagination_details['number_of_pages'] == 1) return;
        return sprintf(__("Page %1d of %2d.", NS\PLUGIN_TEXT_DOMAIN), $this->pagination_details['current_page'], $this->pagination_details['number_of_pages']);
    }
}

?>
