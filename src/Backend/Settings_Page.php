<?php
namespace MZ_Mobilize_America\Backend;

use MZ_Mobilize_America as NS;

use MZ_Mobilize_America\Libraries as Libraries; 


/**
 * This file contains the class which holds all the actions and methods to create the admin dashboard sections
 *
 * This file contains all the actions and functions to create the admin dashboard sections.
 * It should probably be refactored to use oop approach at least for the sake of consistency.
 *
 * @since 2.1.0
 *
 * @package MZ_MBO_Access
 *
 */
/**
 * Actions/Filters
 *
 * Related to all settings API.
 *
 * @since  1.0.0
 */

class Settings_Page {

    static protected $wposa_obj;

    public function __construct() {
        self::$wposa_obj = new Libraries\WP_OSA;
    }

    public function addSections() {
		
		// Section: Basic Settings.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mobilize_america_settings',
                'title' => __( 'Default Organization', NS\PLUGIN_TEXT_DOMAIN ),
            )
        );
        
		// Section: Basic Settings.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mobilize_america_shortcodes',
                'title' => __( 'Shortcodes', NS\PLUGIN_TEXT_DOMAIN ),
            )
        );
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_settings',
            array(
                'id'      => 'organization_id',
                'type'    => 'text',
                'name'    => __( 'Main Organization ID', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => __( 'Default organization ID with which to interface', NS\PLUGIN_TEXT_DOMAIN )
            )
        );
		
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_shortcodes',
            array(
                'id'      => 'shortcode_listing',
                'type'    => 'html',
                'name'    => __( 'Shortcodes and Atts', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => $this->access_codes()
            )
        );
        
        
    }
    
    private function access_codes(){
        $return = '';
        $return .= '<p>'.sprintf('[%1$s] %2$s', 'mobilize_display_events', __("List events for specific organization.", NS\PLUGIN_TEXT_DOMAIN)).'</p>';
        $return .= '<p>'.sprintf('[%1$s] %2$s', 'mobilize_display_organizations', __("List organizations", NS\PLUGIN_TEXT_DOMAIN)).'</p>';
        // $return .= "<ul>";
        // $return .= "<li><strong>level_1_redirect</strong>: " . __("(url string) URL to redirect users with level one access.", 'mz-mindbody-api')."</li>";
        // $return .= "</ul>";
        return $return;
    }
    
    

}
