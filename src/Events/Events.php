<?php
namespace MZ_Mobilize_America\Events;

use MZ_Mobilize_America;
use MZ_Mobilize_America\ShortCode as ShortCode;

class Events extends ShortCode\ShortCode_Script_Loader {

    static $addedAlready = false;

    public function handleShortcode($atts, $content = null) {

        $atts = shortcode_atts( array(
			'full_listing_text' => __('Click Here for Full Listings &amp; Submission', 'mobilize-america'),
			'sign_up_text' => __('Sign Up', 'mobilize-america'),
			'organization_id' => '1',
			'events_feed' => '',
			'event_count' => '5',
			'container_class' => 'loader',
			'loading_text' => -__('Loading...', 'mobilize-america'),
			'container_id' => 'MobilizeEvents',
			'failure_to_retrieve' => __("Unable to retrieve events at this time.", 'mobilize-america'),
			'no_events_message' => __("We don't have any upcoming events listed at this time. Click below to get involved or informed.", 'mobilize-america'),
			'thumbnail' => 0,
			'other_orgs' => 0
				), $atts );

        // Add Style with script adder
        self::addScript();
        self::localizeScript($atts);

        ob_start();
        $template_loader = new Libraries\Template_Loader();
        $template_loader->set_template_data( $atts );
        $template_loader->get_template_part( 'events' );

        return ob_get_clean();
    }

    public function addScript() {
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            wp_register_script('mobilize_events_script', MZ_Mobilize_America\PLUGIN_NAME_URL . 'inc/frontend/js/events.js', array('jquery'), 1.0, true );
 	        wp_enqueue_script('mobilize_events_script');
            wp_register_style( 'mobilize_events_style', MZ_Mobilize_America\PLUGIN_NAME_URL . 'inc/frontend/css/events.css');
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

    public function return_events() {

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
    private function retrieve_events($atts) {

		$organization_id = $atts['organization_id'];
        $now = new \DateTime(null, new \DateTimeZone('America/New_York'));


        // Allow One Day window to allow
        // for in-progress events
        $di = new \DateInterval('PT12H');
        $di->invert = 1;
        $now->add($di);

        $events_start_time_filter = $now->getTimestamp();


        if (empty($atts['other_orgs'])):
            $url_string = 'https://api.mobilize.us/v1/events?';
            $url_string .= 'organization_id=' . $organization_id . '&';
        else:
            $url_string = 'https://staging.mobilize.us/v1/organizations' . $organization_id . '/events?';
        endif;
        
        $url_string .= 'timeslot_start=gte_' . $events_start_time_filter;
        $url_string .= '&per_page=10';

        $mobilize_url = htmlentities($url_string);

        $response =  $this->CallAPI('GET', $mobilize_url);

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

    /*
     *
     *
     */
    private function sequence_events($event_data) {
        // Break each event into multiple events if

        usort($event_data, function( $a, $b ) {
                return $a->timeslots[0]->start_date - $b->timeslots[0]->start_date;
            });
        return $event_data;
    }
}

?>
