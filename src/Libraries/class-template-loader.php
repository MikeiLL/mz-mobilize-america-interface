<?php
namespace MZ_Mobilize_America\Libraries;

use MZ_Mobilize_America;

class Template_Loader extends MZ_Mobilize_America_Gamajo_Template_Loader {

    /**
     * Prefix for filter names.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $filter_prefix = 'mobilize_america';

    /**
     * Directory name where custom templates for this plugin should be found in the theme.
     *
     * For example: 'your-plugin-templates'.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $theme_template_directory = 'templates/mobilize_america';

    /**
     * Reference to the root directory path of this plugin.
     *
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $plugin_directory = Mobilize_America\PLUGIN_NAME_DIR;

    /**
     * Directory name where templates are found in this plugin.
     *
     * Can either be a defined constant, or a relative reference from where the subclass lives.
     *
     * e.g. 'templates' or 'includes/templates', etc.
     *
     * @since 1.1.0
     *
     * @var string
     */
    protected $plugin_template_directory = 'inc/frontend/views';

}

?>
