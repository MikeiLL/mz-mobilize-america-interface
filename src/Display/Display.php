<?php
namespace MZ_Mobilize_America\Display;

use MZ_Mobilize_America as NS;
use MZ_Mobilize_America\ShortCode as ShortCode;
use MZ_Mobilize_America\Common as Common;
use MZ_Mobilize_America\Libraries as Libraries;

class Display extends ShortCode\ShortCode_Script_Loader {

    static $addedAlready = false;
    
    /*
     * @since 1.0.0
     * 
     * visibility private
     * Shortcode attributes
     */
    private $atts;

    public function handleShortcode($atts, $content = null) {

        $this->atts = shortcode_atts( array(
			'endpoint' => 'organizations',
			'organization_id' => 0,
			'per_page' => '',
			'full_listing_text' => __('Click Here for Full Listings &amp; Submission', NS\PLUGIN_TEXT_DOMAIN),
			'sign_up_text' => __('Sign Up', NS\PLUGIN_TEXT_DOMAIN),
			'failure_to_retrieve' => __("Unable to retrieve listings at this time.", NS\PLUGIN_TEXT_DOMAIN),
			'no_events_message' => __("We don't have any listings at this time. Click below to get involved or informed.", NS\PLUGIN_TEXT_DOMAIN),
			'container_class' => 'loader',
			'loading_text' => -__('Loading...', NS\PLUGIN_TEXT_DOMAIN),
			'container_id' => 'MobilizeEvents',
			'thumbnail' => 0,
			'events_feed' => '',
			'query_string' => 0,
			'template_suffix' => '',
			'other_orgs' => 0
				), $atts );

        // Add Style with script adder
        self::addScript();
        self::localizeScript($this->atts);
                
        $ma_options = get_option('mz_mobilize_america_settings');
        		                
        $ajax_template = $ma_options['use_ajax'] == 'on' ? '_ajax' : '';
        
        $this->atts['endpoint'] = strtolower($this->atts['endpoint']);

        $api_result = $this->request_data();
        ob_start();
        $template_loader = new Libraries\Template_Loader();
        $template_loader->set_template_data( ['atts' => $this->atts, 'api_result' => $api_result] );
        $template_file = $template_loader->get_template_part( $this->atts['endpoint'] . $ajax_template .$this->atts['template_suffix'] );
        
        if (empty($template_file)) return __("Template file does not exist.", NS\PLUGIN_TEXT_DOMAIN);
        
        return ob_get_clean();
        
    }

    public function addScript() {
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            wp_register_script('mobilize_events_script', NS\PLUGIN_NAME_URL . 'src/Frontend/js/display.js', array('jquery'), 1.0, true );
 	        wp_enqueue_script('mobilize_events_script');
            wp_register_style( 'mobilize_events_style', NS\PLUGIN_NAME_URL . 'src/Frontend/css/display.css');
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

    public function return_data() {

        check_ajax_referer( $_REQUEST['nonce'], "mobilize_america_events_nonce", false);

        $atts = $_REQUEST['atts'];

        $full_listing_text = $atts['full_listing_text'];
		$sign_up_text = $atts['sign_up_text'];
		$thumbnail = $atts['thumbnail'];
		$no_events_message = stripslashes($atts['no_events_message']);
		$events_feed = $atts['events_feed'];
		$failure_to_retrieve = stripslashes($atts['failure_to_retrieve']);
		$event_count = $atts['event_count'];

 	    $result['type'] = "success";

 	    $events = $this->retrieve_events($atts);
        ob_start();

        if ($events['API Error']) {
        ?>
            <span class="bg-warning text-white warning-message failure-message"><?php echo $failure_to_retrieve; ?></span>
        <?php
        } else if ($events['zero'] == 1) {
        ?>
            <span class="bg-warning text-white warning-message no-events-message"><?php echo $no_events_message; ?></span>
        <?php
        } else {

            $events = $this->sequence_events($events);

            $template_loader = new Libraries\Template_Loader();

            $count = 0;
            foreach ($events as $key => $event):

                $count++;

                $event_address = $event->location->address_lines[0] . ' ' . $event->location->address_lines[1];
                $data = array(
                        'title' => $event->title,
                        'time_slots' => $event->timeslots,
                        'featured_image_url' => isset($event->featured_image_url) ? $event->featured_image_url : '',
                        'venue_name' => isset($event->location->venue) ? $event->location->venue : '',
                        'location_address' => $event_address,
                        'locality' => isset($event->location->locality) ? $event->location->locality : '',
                        'region' => isset($event->location->region) ? $event->location->region : '',
                        'postal_code' => isset($event->location->postal_code) ? $event->location->postal_code : '',
                        'url' => $event->browser_url,
                        'thumbnail' => $thumbnail
                    );
                $template_loader->set_template_data( $data );
                $template_loader->get_template_part( 'event' );

                // Limit the number of events
                if ($count >= $event_count) break;
            endforeach;

        } // If !empty Response

            if (!empty($events_feed)):?>
                <a class="btn-event btn-events-block" href="<?php echo $events_feed ?>"><?php echo $full_listing_text ?></a>
            <?php
            endif;
        
        $organization_id = $atts['organization_id'];
        $now = new \DateTime(null, new \DateTimeZone('America/New_York'));

        // Allow One Day window to allow
        // for in-progress events
        $di = new \DateInterval('PT12H');
        $di->invert = 1;
        $now->add($di);

        $events_start_time_filter = $now->getTimestamp();
        
        if (empty($atts['other_orgs'])):
            $url_string = 'https://api.mobilize.us/api/v1/events?';
            $url_string .= 'organization_id=' . $organization_id . '&';
        else:
            $url_string = 'https://api.mobilize.us/v1/organizations/' . $organization_id . '/events?';
        endif;
        
        $url_string .= 'timeslot_start=gte_' . $events_start_time_filter;
        $url_string .= '&per_page=10';
        
        print_r($url_string);

        $result['message'] = ob_get_clean();

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
           $result = json_encode($result);
           echo $result;
        }
        else {
           header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();

    }

    /*
     *
     *
     */
    private function request_data() {
                
        $api = new Common\API($this->atts);
                
        $result = $api->make_request(false);
        
        return $result;
    }

    
}

?>
