<?php
/*
Plugin Name:  ACF Tooltips by SimpliWeb
Plugin URI:   https://github.com/westcoastdigital/Simpli-ACF-Tooltips
Description:  Adds a custom tooltip tab to fields in ACF
Version:      1.1.0
Author:       Jon Mather
Author URI:   https://simpliweb.com.au
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  simpli
Domain Path:  /languages
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SB_ACF_TOOLTIPS_VERSION', '1.0.0');
define('SB_ACF_TOOLTIPS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SB_ACF_TOOLTIPS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class for ACF Tooltips
 * 
 * This plugin adds customisable tooltip functionality to ACF fields.
 * Tooltips appear as icons next to field labels with hover popups containing
 * custom content, styling, and positioning options.
 * 
 * @since 1.0.0
 */
class SB_ACF_Tooltips
{
    /**
     * Single instance of the class
     * 
     * @var SB_ACF_Tooltips|null
     */
    private static $instance = null;

    /**
     * Get singleton instance
     * 
     * Ensures only one instance of the plugin runs at a time
     * 
     * @return SB_ACF_Tooltips
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - Initialise plugin hooks
     * 
     * Private constructor to enforce singleton pattern
     */
    private function __construct()
    {
        // Check if ACF is active before doing anything
        add_action('plugins_loaded', array($this, 'check_acf'));

        // Register tooltip settings for all ACF field types
        add_action('acf/init', array($this, 'register_tooltip_settings'));

        // Load admin styles and scripts
        add_action('acf/input/admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        // Render the tooltip on the frontend of ACF fields (priority 11 to run after ACF's render)
        add_action('acf/render_field', array($this, 'render_tooltip_after_label'), 11);
    }

    /**
     * Check if ACF is active
     * 
     * Verifies that Advanced Custom Fields is installed and activated.
     * Shows admin notice if ACF is missing.
     * 
     * @return void
     */
    public function check_acf()
    {
        if (!function_exists('acf_get_setting')) {
            add_action('admin_notices', array($this, 'acf_missing_notice'));
        }
    }

    /**
     * Display admin notice if ACF is not active
     * 
     * Shows an error notice in the WordPress admin if ACF is not found
     * 
     * @return void
     */
    public function acf_missing_notice()
    {
?>
        <div class="notice notice-error">
            <p><?php _e('ACF Tooltips requires Advanced Custom Fields to be installed and active.', 'simpli'); ?></p>
        </div>
<?php
    }

    /**
     * Register tooltip settings for all ACF field types
     * 
     * Dynamically loops through all registered ACF field types and hooks
     * our tooltip settings into each one's presentation tab.
     * 
     * @return void
     */
    public function register_tooltip_settings()
    {
        // Get all registered ACF field types
        $field_types = acf_get_field_types();

        // Loop through each field type and add our tooltip settings hook
        foreach ($field_types as $type => $class) {
            add_action("acf/render_field_presentation_settings/type={$type}", array($this, 'add_tooltip_settings'));
        }
    }

    /**
     * Add tooltip settings to the Presentation tab
     * 
     * Renders all tooltip-related settings in ACF's field settings panel.
     * These settings appear when editing a field group.
     * 
     * @param array $field The ACF field being edited
     * @return void
     */
    public function add_tooltip_settings($field)
    {
        // Get available dashicons for the icon selector
        $dashicons = $this->get_dashicons();

        // Section Header - separates tooltip settings from other presentation settings
        acf_render_field_setting($field, array(
            'label' => __('Tooltip Settings', 'simpli'),
            'type'  => 'message',
            'message' => __('Add a helpful tooltip that appears next to the field label.', 'simpli'),
        ));

        // Tooltip Content (WYSIWYG Editor)
        // This is the main content that appears in the tooltip popup
        // Note: delay=1 and quicktags=false prevent editor initialisation errors
        // when creating new fields in ACF
        acf_render_field_setting($field, array(
            'label'        => __('Tooltip Content', 'simpli'),
            'instructions' => __('Enter the content to display in the tooltip. Leave empty to disable tooltip.', 'simpli'),
            'type'         => 'wysiwyg',
            'name'         => 'tooltip_content',
            'tabs'         => 'visual',
            'toolbar'      => 'basic',
            'media_upload' => 0,
            'delay'        => 1, // Delay initialization to prevent ACF editor conflicts
            'wpautop'      => 1,
            'quicktags'    => false, // Disable quicktags to prevent JavaScript errors on new field creation
        ));

        // Tooltip Position - where the popup appears relative to the icon
        acf_render_field_setting($field, array(
            'label'         => __('Tooltip Position', 'simpli'),
            'instructions'  => __('Choose where the tooltip should appear', 'simpli'),
            'type'          => 'select',
            'name'          => 'tooltip_position',
            'choices'       => array(
                'top'    => __('Top', 'simpli'),
                'right'  => __('Right', 'simpli'),
                'bottom' => __('Bottom', 'simpli'),
                'left'   => __('Left', 'simpli'),
            ),
            'default_value' => 'top',
            'allow_null'    => 0,
            'ui'            => 0,
        ));

        // Tooltip Icon - dashicon to display next to the label
        acf_render_field_setting($field, array(
            'label'         => __('Tooltip Icon', 'simpli'),
            'instructions'  => __('Select an icon to display next to the label', 'simpli'),
            'type'          => 'radio',
            'name'          => 'tooltip_icon',
            'choices'       => $dashicons,
            'default_value' => 'dashicons-info',
            'allow_null'    => 0,
            'ui'            => 0,
            'ajax'          => 0,
        ));

        // Tooltip Background Color
        acf_render_field_setting($field, array(
            'label'         => __('Tooltip Background', 'simpli'),
            'instructions'  => __('Select background colour for the tooltip', 'simpli'),
            'type'          => 'color_picker',
            'name'          => 'tooltip_bg',
            'default_value' => '#111111',
            'allow_null'    => 0,
            'ui'            => 0,
            'ajax'          => 0,
        ));

        // Tooltip Width - pixel width of the tooltip popup
        acf_render_field_setting($field, array(
            'label'         => __('Tooltip Width', 'simpli'),
            'instructions'  => __('Set the px width for the tooltip', 'simpli'),
            'type'          => 'number',
            'name'          => 'tooltip_width',
            'default_value' => 60,
            'allow_null'    => 0,
            'ui'            => 0,
            'ajax'          => 0,
            'prepend'       => '',
            'append'        => 'px',
            'maxlength'     => 5,
        ));
    }

    /**
     * Get available Dashicons for the icon selector
     * 
     * Returns an array of dashicon class names and their HTML previews.
     * These are displayed as radio options in the field settings.
     * 
     * Developers can add custom icons using the 'sb_acf_tooltip_icons' filter.
     * 
     * @return array Associative array of icon class => HTML preview
     */
    private function get_dashicons()
    {
        // Default Dashicons provided by the plugin
        $default_icons = array(
            'dashicons-lock' => '<span class="dashicons dashicons-lock"></span>',
            'dashicons-calendar' => '<span class="dashicons dashicons-calendar"></span>',
            'dashicons-visibility' => '<span class="dashicons dashicons-visibility"></span>',
            'dashicons-post-status' => '<span class="dashicons dashicons-post-status"></span>',
            'dashicons-edit' => '<span class="dashicons dashicons-edit"></span>',
            'dashicons-trash' => '<span class="dashicons dashicons-trash"></span>',
            'dashicons-external' => '<span class="dashicons dashicons-external"></span>',
            'dashicons-randomize' => '<span class="dashicons dashicons-randomize"></span>',
            'dashicons-hammer' => '<span class="dashicons dashicons-hammer"></span>',
            'dashicons-art' => '<span class="dashicons dashicons-art"></span>',
            'dashicons-performance' => '<span class="dashicons dashicons-performance"></span>',
            'dashicons-universal-access' => '<span class="dashicons dashicons-universal-access"></span>',
            'dashicons-clipboard' => '<span class="dashicons dashicons-clipboard"></span>',
            'dashicons-heart' => '<span class="dashicons dashicons-heart"></span>',
            'dashicons-megaphone' => '<span class="dashicons dashicons-megaphone"></span>',
            'dashicons-schedule' => '<span class="dashicons dashicons-schedule"></span>',
            'dashicons-info' => '<span class="dashicons dashicons-info"></span>',
            'dashicons-info-outline' => '<span class="dashicons dashicons-info-outline"></span>',
            'dashicons-cart' => '<span class="dashicons dashicons-cart"></span>',
            'dashicons-feedback' => '<span class="dashicons dashicons-feedback"></span>',
            'dashicons-cloud' => '<span class="dashicons dashicons-cloud"></span>',
            'dashicons-translation' => '<span class="dashicons dashicons-translation"></span>',
            'dashicons-tag' => '<span class="dashicons dashicons-tag"></span>',
            'dashicons-category' => '<span class="dashicons dashicons-category"></span>',
            'dashicons-archive' => '<span class="dashicons dashicons-archive"></span>',
            'dashicons-tagcloud' => '<span class="dashicons dashicons-tagcloud"></span>',
            'dashicons-media-audio' => '<span class="dashicons dashicons-media-audio"></span>',
            'dashicons-admin-links' => '<span class="dashicons dashicons-admin-links"></span>',
            'dashicons-media-video' => '<span class="dashicons dashicons-media-video"></span>',
            'dashicons-playlist-audio' => '<span class="dashicons dashicons-playlist-audio"></span>',
            'dashicons-playlist-video' => '<span class="dashicons dashicons-playlist-video"></span>',
            'dashicons-yes' => '<span class="dashicons dashicons-yes"></span>',
            'dashicons-no' => '<span class="dashicons dashicons-no"></span>',
            'dashicons-plus' => '<span class="dashicons dashicons-plus"></span>',
            'dashicons-minus' => '<span class="dashicons dashicons-minus"></span>',
            'dashicons-dismiss' => '<span class="dashicons dashicons-dismiss"></span>',
            'dashicons-marker' => '<span class="dashicons dashicons-marker"></span>',
            'dashicons-star-filled' => '<span class="dashicons dashicons-star-filled"></span>',
            'dashicons-star-half' => '<span class="dashicons dashicons-star-half"></span>',
            'dashicons-star-empty' => '<span class="dashicons dashicons-star-empty"></span>',
            'dashicons-flag' => '<span class="dashicons dashicons-flag"></span>',
            'dashicons-share' => '<span class="dashicons dashicons-share"></span>',
            'dashicons-share-alt' => '<span class="dashicons dashicons-share-alt"></span>',
            'dashicons-share-alt2' => '<span class="dashicons dashicons-share-alt2"></span>',
            'dashicons-twitter' => '<span class="dashicons dashicons-twitter"></span>',
            'dashicons-rss' => '<span class="dashicons dashicons-rss"></span>',
            'dashicons-email' => '<span class="dashicons dashicons-email"></span>',
            'dashicons-email-alt' => '<span class="dashicons dashicons-email-alt"></span>',
            'dashicons-facebook' => '<span class="dashicons dashicons-facebook"></span>',
            'dashicons-networking' => '<span class="dashicons dashicons-networking"></span>',
            'dashicons-location' => '<span class="dashicons dashicons-location"></span>',
            'dashicons-location-alt' => '<span class="dashicons dashicons-location-alt"></span>',
            'dashicons-camera' => '<span class="dashicons dashicons-camera"></span>',
            'dashicons-images-alt' => '<span class="dashicons dashicons-images-alt"></span>',
            'dashicons-images-alt2' => '<span class="dashicons dashicons-images-alt2"></span>',
            'dashicons-video-alt' => '<span class="dashicons dashicons-video-alt"></span>',
            'dashicons-video-alt2' => '<span class="dashicons dashicons-video-alt2"></span>',
            'dashicons-video-alt3' => '<span class="dashicons dashicons-video-alt3"></span>',
            'dashicons-vault' => '<span class="dashicons dashicons-vault"></span>',
            'dashicons-shield' => '<span class="dashicons dashicons-shield"></span>',
            'dashicons-shield-alt' => '<span class="dashicons dashicons-shield-alt"></span>',
            'dashicons-sos' => '<span class="dashicons dashicons-sos"></span>',
            'dashicons-search' => '<span class="dashicons dashicons-search"></span>',
            'dashicons-analytics' => '<span class="dashicons dashicons-analytics"></span>',
            'dashicons-chart-pie' => '<span class="dashicons dashicons-chart-pie"></span>',
            'dashicons-chart-bar' => '<span class="dashicons dashicons-chart-bar"></span>',
            'dashicons-chart-line' => '<span class="dashicons dashicons-chart-line"></span>',
            'dashicons-chart-area' => '<span class="dashicons dashicons-chart-area"></span>',
            'dashicons-groups' => '<span class="dashicons dashicons-groups"></span>',
            'dashicons-id' => '<span class="dashicons dashicons-id"></span>',
            'dashicons-products' => '<span class="dashicons dashicons-products"></span>',
            'dashicons-awards' => '<span class="dashicons dashicons-awards"></span>',
            'dashicons-forms' => '<span class="dashicons dashicons-forms"></span>',
            'dashicons-portfolio' => '<span class="dashicons dashicons-portfolio"></span>',
            'dashicons-book' => '<span class="dashicons dashicons-book"></span>',
            'dashicons-download' => '<span class="dashicons dashicons-download"></span>',
            'dashicons-upload' => '<span class="dashicons dashicons-upload"></span>',
            'dashicons-backup' => '<span class="dashicons dashicons-backup"></span>',
            'dashicons-clock' => '<span class="dashicons dashicons-clock"></span>',
            'dashicons-lightbulb' => '<span class="dashicons dashicons-lightbulb"></span>',
            'dashicons-microphone' => '<span class="dashicons dashicons-microphone"></span>',
            'dashicons-desktop' => '<span class="dashicons dashicons-desktop"></span>',
            'dashicons-tablet' => '<span class="dashicons dashicons-tablet"></span>',
            'dashicons-smartphone' => '<span class="dashicons dashicons-smartphone"></span>',
            'dashicons-smiley' => '<span class="dashicons dashicons-smiley"></span>'
        );

        /**
         * Filter: sb_acf_tooltip_icons
         * 
         * Allows developers to add custom icons to the tooltip icon selector.
         * 
         * @param array $default_icons Array of icon class => HTML preview
         * 
         * @return array Modified array of icons
         * 
         * @example
         * add_filter('sb_acf_tooltip_icons', function($icons) {
         *     // Add Font Awesome icon
         *     $icons['fa-custom'] = '<i class="fa fa-custom"></i>';
         *     
         *     // Add custom SVG icon
         *     $icons['my-custom-icon'] = '<span class="my-custom-icon"><svg>...</svg></span>';
         *     
         *     // Add image icon
         *     $icons['my-image-icon'] = '<img src="' . get_template_directory_uri() . '/images/icon.png" width="20" height="20">';
         *     
         *     return $icons;
         * });
         */
        return apply_filters('sb_acf_tooltip_icons', $default_icons);
    }

    /**
     * Helper method to register an icon library
     * 
     * @param string $prefix CSS prefix for the icon library (e.g., 'fa' for Font Awesome)
     * @param array $icon_names Array of icon names without prefix
     * @param string $format HTML format string with %s placeholder for icon name
     * 
     * @example
     * $this->register_icon_library('fa', ['home', 'user', 'heart'], '<i class="fa fa-%s"></i>');
     */
    public function register_icon_library($prefix, $icon_names, $format)
    {
        add_filter('sb_acf_tooltip_icons', function ($icons) use ($prefix, $icon_names, $format) {
            foreach ($icon_names as $name) {
                $key = $prefix . '-' . $name;
                $icons[$key] = sprintf($format, $name);
            }
            return $icons;
        });
    }

    /**
     * Enqueue admin scripts and styles
     * 
     * Loads dashicons, custom CSS for tooltip styling and icon selector,
     * and JavaScript for tooltip functionality.
     * 
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        // Ensure dashicons are loaded (usually already loaded by WordPress)
        wp_enqueue_style('dashicons');

        // Enqueue custom admin CSS for tooltip styling and icon selector
        wp_enqueue_style(
            'sb-acf-tooltips-admin',
            SB_ACF_TOOLTIPS_PLUGIN_URL . 'css/admin.css',
            array('dashicons', 'acf-input'),
            SB_ACF_TOOLTIPS_VERSION
        );

        // Enqueue custom JavaScript for tooltip functionality
        wp_enqueue_script(
            'sb-acf-tooltips-admin',
            SB_ACF_TOOLTIPS_PLUGIN_URL . 'js/admin.js',
            array('jquery', 'acf-input'),
            SB_ACF_TOOLTIPS_VERSION,
            true
        );
    }

    /**
     * Render the tooltip icon and popup
     * 
     * This runs when ACF renders each field. If the field has tooltip content,
     * this outputs the HTML for the tooltip icon and popup box.
     * 
     * Handles both default Dashicons and custom icons added via the filter.
     * 
     * @param array $field The ACF field being rendered
     * @return void
     */
    public function render_tooltip_after_label($field)
    {
        // Don't render anything if there's no tooltip content
        if (empty($field['tooltip_content'])) {
            return;
        }

        // Get tooltip settings with fallbacks
        $icon_key = !empty($field['tooltip_icon']) ? $field['tooltip_icon'] : 'dashicons-info';
        $position = isset($field['tooltip_position']) ? $field['tooltip_position'] : 'top';
        $tooltip_content = !empty($field['tooltip_content']) ? wp_kses_post($field['tooltip_content']) : '';
        $tooltip_bg = !empty($field['tooltip_bg']) ? esc_attr($field['tooltip_bg']) : '#111111';
        $tooltip_width = !empty($field['tooltip_width']) ? intval($field['tooltip_width']) : 60;

        // Get all available icons (including custom ones from filter)
        $all_icons = $this->get_dashicons();

        // Determine the icon HTML to display
        // If the icon key exists in our icons array, use its HTML
        // Otherwise, treat it as a dashicon class (for backward compatibility)
        if (isset($all_icons[$icon_key])) {
            $icon_html = $all_icons[$icon_key];
        } else {
            // Fallback: assume it's a dashicon class
            $icon_html = '<span class="dashicons ' . esc_attr($icon_key) . '"></span>';
        }

        // Output the tooltip HTML
        // The tooltip is positioned via CSS based on the data-position attribute
        // Inline styles allow for custom background color and width per tooltip
        echo sprintf(
            '<span class="sb-acf-tooltip" data-position="%s" data-icon-key="%s">
            %s
            <span class="sb-acf-tooltip-inner" style="background:%s;width:%dpx;">
                <div class="content-wrapper">%s</div>
            </span>
        </span>',
            esc_attr($position),
            esc_attr($icon_key),
            $icon_html,
            $tooltip_bg,
            $tooltip_width,
            $tooltip_content
        );
    }
}

// Initialize the plugin singleton
SB_ACF_Tooltips::get_instance();
