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
        
		// Section: Shortcode and Atts.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mobilize_america_shortcodes',
                'title' => __( 'Shortcodes', NS\PLUGIN_TEXT_DOMAIN ),
            )
        );
        
		// Section: Shortcode and Atts.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mobilize_america_instructions',
                'title' => __( 'Instructions', NS\PLUGIN_TEXT_DOMAIN ),
            )
        );
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_settings',
            array(
                'id'      => 'organization_id',
                'type'    => 'number',
                'name'    => __( 'Main Organization ID', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => __( 'Default organization ID with which to interface', NS\PLUGIN_TEXT_DOMAIN )
            )
        );
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_settings',
            array(
                'id'      => 'per_page',
                'type'    => 'number',
                'name'    => __( 'Default number of listings to request', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => __( 'Default per_page request setting', NS\PLUGIN_TEXT_DOMAIN )
            )
        );
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_settings',
            array(
                'id'      => 'use_staging',
                'type'    => 'checkbox',
                'name'    => __( 'Use the Mobilize America Staging API', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => __( 'Yes. Just testing for now.', NS\PLUGIN_TEXT_DOMAIN )
            )
        );
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_settings',
            array(
                'id'      => 'use_ajax',
                'type'    => 'checkbox',
                'name'    => __( 'Use Ajax', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => __( 'Retrieve API results asynchronously.', NS\PLUGIN_TEXT_DOMAIN )
            )
        );
		
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_shortcodes',
            array(
                'id'      => 'shortcode_listing',
                'type'    => 'html',
                'name'    => __( 'Shortcode', NS\PLUGIN_TEXT_DOMAIN ),
                'desc'    => '<p>' . sprintf('[%1$s %2$s]', 'mobilize_america', 'endpoint="events"') . ' ' . __("Retrieve event data from mobilize america api.", NS\PLUGIN_TEXT_DOMAIN).'</p>'
            )
        );
		
       
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_shortcodes',
            array(
                'id'      => 'attribute_listing',
                'type'    => 'html',
                'name'    => __("Shortcode Atts", NS\PLUGIN_TEXT_DOMAIN),
                'desc'    => $this->attribute_descriptions()
            )
        );
        
        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mobilize_america_instructions',
            array(
                'id'      => 'intro',
                'type'    => 'html',
                'name'    => __("Template-based customization", NS\PLUGIN_TEXT_DOMAIN),
                'desc'    => $this->intro()
            )
        );
        
        
    }
    
    private function attribute_descriptions(){
        $return = '';
        $return .= "<ul>";
        $return .= "<li><strong>endpoint</strong>: " . __("Which endpoint to query (default: organizations).", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>query_string</strong>: " . __("(query string) to append to end of API call", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>full_listing_text</strong>: " . __("(default) Click Here for Full Listings &amp; Submission", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>sign_up_text</strong>: " . __("(default) Sign Up", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>failure_to_retrieve</strong>: " . __("(default) Unable to retrieve listings at this time.", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>no_events_message</strong>: " . __("(default) We don't have any listings at this time. Click below to get involved or informed.", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>container_class</strong>: " . __("(default) loader", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>loading_text</strong>: " . __("(default) Loading...", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>container_id</strong>: " . __("(default) MobilizeEvents", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>per_page</strong>: " . __("Override number of listings to request (overridden by query string atts if present)", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>organization_id</strong>: " . __("Override id of Org to request data for (overridden by query string atts if present)", NS\PLUGIN_TEXT_DOMAIN)."</li>";
        $return .= "<li><strong>template_suffix</strong>: " . sprintf( "Load template file with endpoing + suffix. (<code>_red</code> seeks: %s.)", "<code>theme/templates/mobilize_america/[endpoint]_red</code>" ) . "</li>";
        $return .= "</ul>";        
        
        $return .= '<p>'.sprintf('Example: [%1$s  %2$s]', 'mobilize_america endpoint="events"', htmlentities('query_string="organization_id=1234&per_page=150"')).'</p>';

        return $return;
    }
    
    
    private function intro(){
        $return = '';
        $return .= <<<EOD
<p>This is a very minimal plugin. Data returned from the Mobilize America is called with a shortcode and displayed via template files, which
can be overridden in a theme.</p>
<p>Template files are located at <code>src/Frontend/views/</code> and can be overridden in your theme by copying them (or just making new ones)
to <code>YourTheme/templates/mobilize_america/[endpoint]_red</code>, where <code>[endpoint]</code> is one of <code>events</code> or <code>organizations</code>.</p>
<p>To view the raw data returned from the API, you may use shortcode <code>[mobilize_america endpoint="events" template_suffix="_raw"]</code> (or substitute the organizations endpoint).</p>
EOD;
        return $return;
    }
    

}
