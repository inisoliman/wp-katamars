<?php
/**
 * كلاس الإدارة
 */

class Katamars_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * تحميل ملفات CSS للإدارة
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            KATAMARS_PLUGIN_URL . 'admin/css/katamars-admin.css',
            [],
            $this->version,
            'all'
        );
    }

    /**
     * تحميل ملفات JavaScript للإدارة
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            KATAMARS_PLUGIN_URL . 'admin/js/katamars-admin.js',
            ['jquery'],
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'katamars_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('katamars_admin_nonce')
        ]);
    }

    /**
     * إضافة قائمة الإدارة
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('القطمارس القبطي', 'katamars'),
            __('القطمارس', 'katamars'),
            'manage_options',
            $this->plugin_name,
            [$this, 'display_plugin_admin_page'],
            'dashicons-book-alt',
            25
        );

        add_submenu_page(
            $this->plugin_name,
            __('لوحة التحكم', 'katamars'),
            __('لوحة التحكم', 'katamars'),
            'manage_options',
            $this->plugin_name,
            [$this, 'display_plugin_admin_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('القراءات اليومية', 'katamars'),
            __('القراءات', 'katamars'),
            'manage_options',
            $this->plugin_name . '-readings',
            [$this, 'display_readings_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('السنكسار', 'katamars'),
            __('السنكسار', 'katamars'),
            'manage_options',
            $this->plugin_name . '-synaxarium',
            [$this, 'display_synaxarium_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('الأعياد والمناسبات', 'katamars'),
            __('الأعياد', 'katamars'),
            'manage_options',
            $this->plugin_name . '-feasts',
            [$this, 'display_feasts_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('الإعدادات', 'katamars'),
            __('الإعدادات', 'katamars'),
            'manage_options',
            $this->plugin_name . '-settings',
            [$this, 'display_settings_page']
        );
    }

    /**
     * عرض الصفحة الرئيسية للإدارة
     */
    public function display_plugin_admin_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-dashboard.php';
    }

    /**
     * عرض صفحة القراءات
     */
    public function display_readings_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-readings.php';
    }

    /**
     * عرض صفحة السنكسار
     */
    public function display_synaxarium_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-synaxarium.php';
    }

    /**
     * عرض صفحة الأعياد
     */
    public function display_feasts_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-feasts.php';
    }

    /**
     * عرض صفحة الإعدادات
     */
    public function display_settings_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-settings.php';
    }

    /**
     * تهيئة الإعدادات
     */
    public function init_settings() {
        register_setting(
            $this->plugin_name,
            $this->plugin_name . '_settings'
        );
    }
}