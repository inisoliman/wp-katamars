<?php
/**
 * كلاس الترجمة
 */

class Katamars_i18n {

    /**
     * تحميل ملفات الترجمة
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'katamars',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}