<?php
/**
 * كلاس الواجهة العامة
 */

class Katamars_Public {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * تحميل ملفات CSS
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            KATAMARS_PLUGIN_URL . 'public/css/katamars-public.css',
            [],
            $this->version,
            'all'
        );
    }

    /**
     * تحميل ملفات JavaScript
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            KATAMARS_PLUGIN_URL . 'public/js/katamars-public.js',
            ['jquery'],
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'katamars_public', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('katamars/v1/'),
            'nonce' => wp_create_nonce('katamars_public_nonce')
        ]);
    }
}