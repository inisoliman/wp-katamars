<?php
/**
 * الكلاس الرئيسي للإضافة
 *
 * @since 1.0.0
 * @package Katamars
 */

class Katamars {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = KATAMARS_VERSION;
        $this->plugin_name = 'katamars';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * تحميل الملفات المطلوبة
     */
    private function load_dependencies() {
        // المكتبات الأساسية
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-loader.php';
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-i18n.php';
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-database.php';
        
        // وحدات النظام
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-coptic-calendar.php';
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-readings.php';
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-synaxarium.php';
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-feasts.php';
        
        // الإدارة
        require_once KATAMARS_PLUGIN_DIR . 'admin/class-katamars-admin.php';
        require_once KATAMARS_PLUGIN_DIR . 'admin/class-katamars-settings.php';
        
        // الواجهة العامة
        require_once KATAMARS_PLUGIN_DIR . 'public/class-katamars-public.php';
        require_once KATAMARS_PLUGIN_DIR . 'public/class-katamars-shortcodes.php';
        require_once KATAMARS_PLUGIN_DIR . 'public/class-katamars-widgets.php';
        
        // REST API
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-api.php';
        
        $this->loader = new Katamars_Loader();
    }

    /**
     * إعداد الترجمة
     */
    private function set_locale() {
        $plugin_i18n = new Katamars_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * تعريف hooks الإدارة
     */
    private function define_admin_hooks() {
        $plugin_admin = new Katamars_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'init_settings');
    }

    /**
     * تعريف hooks العامة
     */
    private function define_public_hooks() {
        $plugin_public = new Katamars_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Shortcodes
        $shortcodes = new Katamars_Shortcodes();
        $this->loader->add_action('init', $shortcodes, 'register_shortcodes');
        
        // Widgets
        $widgets = new Katamars_Widgets();
        $this->loader->add_action('widgets_init', $widgets, 'register_widgets');
        
        // REST API
        $api = new Katamars_API();
        $this->loader->add_action('rest_api_init', $api, 'register_routes');
    }

    /**
     * تشغيل الإضافة
     */
    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}