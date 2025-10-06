<?php
/**
 * كلاس إعدادات الإدارة
 */

class Katamars_Settings {

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * تسجيل الإعدادات
     */
    public function register_settings() {
        register_setting(
            'katamars_settings_group',
            'katamars_settings',
            [$this, 'sanitize_settings']
        );

        // قسم الإعدادات العامة
        add_settings_section(
            'katamars_general_section',
            __('الإعدادات العامة', 'katamars'),
            [$this, 'general_section_callback'],
            'katamars-settings'
        );

        // اللغة الافتراضية
        add_settings_field(
            'default_language',
            __('اللغة الافتراضية', 'katamars'),
            [$this, 'language_field_callback'],
            'katamars-settings',
            'katamars_general_section'
        );

        // نوع الخدمة الافتراضي
        add_settings_field(
            'default_service',
            __('نوع الخدمة الافتراضي', 'katamars'),
            [$this, 'service_field_callback'],
            'katamars-settings',
            'katamars_general_section'
        );

        // عرض التاريخ القبطي
        add_settings_field(
            'show_coptic_date',
            __('عرض التاريخ القبطي', 'katamars'),
            [$this, 'coptic_date_field_callback'],
            'katamars-settings',
            'katamars_general_section'
        );

        // عرض السنكسار
        add_settings_field(
            'show_synaxarium',
            __('عرض السنكسار', 'katamars'),
            [$this, 'synaxarium_field_callback'],
            'katamars-settings',
            'katamars_general_section'
        );

        // مدة الكاش
        add_settings_field(
            'cache_duration',
            __('مدة الكاش (بالثواني)', 'katamars'),
            [$this, 'cache_field_callback'],
            'katamars-settings',
            'katamars_general_section'
        );

        // تفعيل API
        add_settings_field(
            'enable_api',
            __('تفعيل REST API', 'katamars'),
            [$this, 'api_field_callback'],
            'katamars-settings',
            'katamars_general_section'
        );
    }

    public function general_section_callback() {
        echo '<p>' . __('إعدادات عامة لإضافة القطمارس القبطي', 'katamars') . '</p>';
    }

    public function language_field_callback() {
        $options = get_option('katamars_settings');
        $value = isset($options['language']) ? $options['language'] : 'ar';
        ?>
        <select name="katamars_settings[language]">
            <option value="ar" <?php selected($value, 'ar'); ?>>العربية</option>
            <option value="en" <?php selected($value, 'en'); ?>>English</option>
        </select>
        <?php
    }

    public function service_field_callback() {
        $options = get_option('katamars_settings');
        $value = isset($options['default_service']) ? $options['default_service'] : 'liturgy';
        ?>
        <select name="katamars_settings[default_service]">
            <option value="liturgy" <?php selected($value, 'liturgy'); ?>>القداس الإلهي</option>
            <option value="matins" <?php selected($value, 'matins'); ?>>رفع بخور باكر</option>
            <option value="vespers" <?php selected($value, 'vespers'); ?>>رفع بخور عشية</option>
        </select>
        <?php
    }

    public function coptic_date_field_callback() {
        $options = get_option('katamars_settings');
        $value = isset($options['show_coptic_date']) ? $options['show_coptic_date'] : true;
        ?>
        <label>
            <input type="checkbox" name="katamars_settings[show_coptic_date]" value="1" <?php checked($value, 1); ?>>
            <?php _e('عرض التاريخ القبطي في الصفحات', 'katamars'); ?>
        </label>
        <?php
    }

    public function synaxarium_field_callback() {
        $options = get_option('katamars_settings');
        $value = isset($options['show_synaxarium']) ? $options['show_synaxarium'] : true;
        ?>
        <label>
            <input type="checkbox" name="katamars_settings[show_synaxarium]" value="1" <?php checked($value, 1); ?>>
            <?php _e('عرض السنكسار تلقائياً', 'katamars'); ?>
        </label>
        <?php
    }

    public function cache_field_callback() {
        $options = get_option('katamars_settings');
        $value = isset($options['cache_duration']) ? $options['cache_duration'] : 86400;
        ?>
        <input type="number" name="katamars_settings[cache_duration]" value="<?php echo esc_attr($value); ?>" min="0" step="3600">
        <p class="description"><?php _e('86400 = يوم واحد، 0 = تعطيل الكاش', 'katamars'); ?></p>
        <?php
    }

    public function api_field_callback() {
        $options = get_option('katamars_settings');
        $value = isset($options['enable_api']) ? $options['enable_api'] : true;
        ?>
        <label>
            <input type="checkbox" name="katamars_settings[enable_api]" value="1" <?php checked($value, 1); ?>>
            <?php _e('تفعيل REST API للتطبيقات الخارجية', 'katamars'); ?>
        </label>
        <?php
    }

    /**
     * تنظيف الإعدادات
     */
    public function sanitize_settings($input) {
        $sanitized = [];
        
        if (isset($input['language'])) {
            $sanitized['language'] = sanitize_text_field($input['language']);
        }
        
        if (isset($input['default_service'])) {
            $sanitized['default_service'] = sanitize_text_field($input['default_service']);
        }
        
        $sanitized['show_coptic_date'] = isset($input['show_coptic_date']) ? 1 : 0;
        $sanitized['show_synaxarium'] = isset($input['show_synaxarium']) ? 1 : 0;
        $sanitized['enable_api'] = isset($input['enable_api']) ? 1 : 0;
        
        if (isset($input['cache_duration'])) {
            $sanitized['cache_duration'] = absint($input['cache_duration']);
        }
        
        return $sanitized;
    }
}

new Katamars_Settings();