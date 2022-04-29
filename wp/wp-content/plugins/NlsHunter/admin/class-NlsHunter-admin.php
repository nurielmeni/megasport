<?php
// Set the Modul customizer options
include_once NLS__PLUGIN_PATH . '/includes/customizer/customizerAdjustments.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.1.0
 *
 * @package    NlsHunter
 * @subpackage NlsHunter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    NlsHunter
 * @subpackage NlsHunter/admin
 * @author     Meni Nuriel <nurielmeni@gmail.com>
 */
class NlsHunter_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.1.0
     * @access   private
     * @var      string    $NlsHunter    The ID of this plugin.
     */
    private $nlsHunterApi;

    /**
     * The version of this plugin.
     *
     * @since    1.1.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.1.0
     * @param      string    $NlsHunter       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($nlsHunterApi, $version)
    {

        $this->nlsHunterApi = $nlsHunterApi;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.1.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in NlsHunter_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The NlsHunter_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->nlsHunterApi, plugin_dir_url(__FILE__) . 'css/NlsHunter-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.1.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in NlsHunter_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The NlsHunter_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->nlsHunterApi, plugin_dir_url(__FILE__) . 'js/NlsHunter-admin.js', array('jquery'), $this->version, false);
    }

    public function NlsHunter_plugin_menu()
    {
    }

    // Load the plugin admin page partial.
    public function NlsHunter_plugin_options()
    {
    }
}
