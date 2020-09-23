<?php
namespace MZ_Mobilize_America\Display;

use MZ_Mobilize_America as NS;
use MZ_Mobilize_America\Shortcode as Shortcode;
use MZ_Mobilize_America\Common as Common;
use MZ_Mobilize_America\Libraries as Libraries;
use \Exception as Exception;

class Display extends Shortcode\Shortcode_Script_Loader {
    
    /*
     * @since 1.0.0
     * 
     * visibility private
     * Shortcode attribute Errors, if any
     */
    private $attribute_errors = [];
    
    /*
     * @since 1.0.0
     * 
     * visibility public
     * If scripts and styles have been enqueued
     */
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
			'per_page' => 0,
			'full_listing_text' => __('Click Here for Full Listings &amp; Submission', NS\PLUGIN_TEXT_DOMAIN),
			'sign_up_text' => __('Sign Up', NS\PLUGIN_TEXT_DOMAIN),
			'failure_to_retrieve' => __("Unable to retrieve listings at this time.", NS\PLUGIN_TEXT_DOMAIN),
			'no_events_message' => __("We don't have any listings at this time. Click below to get involved or informed.", NS\PLUGIN_TEXT_DOMAIN),
			'container_class' => 'loader',
			'loading_text' => __('Loading&#8230;', NS\PLUGIN_TEXT_DOMAIN),
			'container_id' => 'MobilizeEvents',
			'thumbnail' => 0,
			'events_feed' => '',
			'query_string' => 0,
			'template_suffix' => '',
			'other_orgs' => 0
				), $atts );

        // Add Style with script adder
        self::addScript();
        
        try {
            $this->validate_and_sanitize_atts();
        } catch (Exception $e) {
            return sprintf(__("Errors found in shortcode atts: <code>%1s</code>. Refer to the docs in admin settings and update or remove them.", NS\PLUGIN_TEXT_DOMAIN), implode( ', ' , $this->attribute_errors ));
        }
        
        self::localizeScript($this->atts);
                
        $ma_options = get_option('mz_mobilize_america_settings');
        		                
        $ajax_template = $ma_options['use_ajax'] == 'on' ? '_ajax' : '';
        
        $this->atts['button_class'] = !empty($ma_options['button_class']) ? $ma_options['button_class'] : 'btn mobilize';
        
        $this->atts['endpoint'] = strtolower($this->atts['endpoint']);

        $api_object = new Common\API($this->atts);
                
        try {
            $api_object->make_request(false);
        } catch (\Exception $e) {
            return ($e->getMessage() == 'Zero Count') ? $this->atts['no_events_message'] : $this->atts['failure_to_retrieve'];
        }
        
        ob_start();
        $template_loader = new Libraries\Template_Loader();
        $template_loader->set_template_data( ['atts' => $this->atts, 'api_object' => $api_object] );
        $template_file = $template_loader->get_template_part( $this->atts['endpoint'] . $ajax_template .$this->atts['template_suffix'] );
        
        if (empty($template_file)) return __("Template file does not exist.", NS\PLUGIN_TEXT_DOMAIN);
        
        return ob_get_clean();
        
    }

    public function addScript() {
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            wp_register_script('mobilize_america_display_script', NS\PLUGIN_NAME_URL . 'src/Frontend/js/display.js', array('jquery'), NS\PLUGIN_VERSION, true );
 	        wp_enqueue_script('mobilize_america_display_script');
            wp_register_style( 'mobilize_america_display_style', NS\PLUGIN_NAME_URL . 'src/Frontend/css/display.css', [], NS\PLUGIN_VERSION);
            wp_enqueue_style('mobilize_america_display_style');
        }
    }

    public static function localizeScript($atts = []) {

        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $nonce = wp_create_nonce( 'mobilize_america_display_nonce');
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
            'nonce' => $nonce,
            'atts' => $atts
            );
        wp_localize_script( 'mobilize_america_display_script', 'mobilize_america_events', $params);
    }

    /*
     * Request Data from the API
     *
     * @since 1.0.0
     *
     */
    private function request_data() {
                
        $api = new Common\API($this->atts);
                
        $result = $api->make_request(false);
        
        return $result;
    }
    
    /* Verify and Sanitize
     *
     * @since 1.0.0
     *
     * Sanitize Atts or return error.
     */
    public function validate_and_sanitize_atts(){
        
        // Check user input and report errors
        foreach ($this->atts as $attr => $val) {
            if (empty($val)) continue;
            switch ($attr) {
                case 'query_string':
                    if ($val != htmlspecialchars($val, ENT_QUOTES)){
                        array_push($this->attribute_errors, 'query_string');
                        break;
                    }
                    $this->atts[$attr] = htmlspecialchars($val, ENT_QUOTES);
                    break;
                case 'organization_id':
                    if (!is_numeric($val)){
                        array_push($this->attribute_errors, 'organization_id');
                        break;
                    };
                    break;
                case 'per_page':
                    if (!is_numeric($val)){
                        array_push($this->attribute_errors, 'per_page');
                        break;
                    };
                    break;
                case 'other_orgs':
                    if (!filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)){
                        array_push($this->attribute_errors, 'other_orgs');
                        break;
                    }
                    $this->atts[$attr] = $val;
                    break;
                case 'thumbnail':
                    if (!filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)){
                        array_push($this->attribute_errors, 'thumbnail');
                        break;
                    }
                    $this->atts[$attr] = $val;
                    break;
                case 'container_id':
                    if ($val != sanitize_html_class($val)){
                        array_push($this->attribute_errors, 'container_id');
                        break;
                    }
                    $this->atts[$attr] = sanitize_html_class($val);
                    break;
                default:
                   $this->atts[$attr] = sanitize_text_field($val);
            }
        }
        
        if (!empty($this->attribute_errors)){
            throw new Exception('Attribute Errors');
        }
    }

    
}

?>
