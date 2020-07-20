<?php
namespace MZ_Mobilize_America\Organizations;

use MZ_Mobilize_America\ShortCode as ShortCode;
use MZ_Mobilize_America as NS;
use MZ_Mobilize_America\Common as Common;

class Organizations extends ShortCode\ShortCode_Script_Loader {

    static $addedAlready = false;
    
    /*
     * @since 1.0.0
     * visibility private
     * Shortcode attributes
     */
    private $atts;

    public function handleShortcode($atts, $content = null) {

        $this->atts = shortcode_atts( array(
			'organization_id' => 1,
			'query_string' => ''
				), $atts );

        // Add Style with script adder
        //self::addScript();
        //self::localizeScript($atts);        

        return $this->retrieve_organizations($atts);
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

    public static function localizeScript() {

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
     *
     *
     */
    private function retrieve_organizations() {

        $endpoint = 'organizations';
        
        $api = new Common\API($this->atts);
        
        $result = $api->make_request('GET', $endpoint, false);
        
        $listing_table = '<table>';
        
        echo "Displaying " . count($result->data) . " of " . $result->count . " results.";
        
        foreach($result->data as $k => $org){
            
            $listing_table .= '<tr><td>' . $org->id . '</td><td>' . $org->name . '</td></tr>';
            
        }
        
        $listing_table .= '</table>';

        return $listing_table;

    }

}

?>
