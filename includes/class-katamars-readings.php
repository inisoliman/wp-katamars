<?php
/**
 * كلاس القراءات اليومية
 */

class Katamars_Readings {

    /**
     * الحصول على قراءات اليوم
     */
    public static function get_todays_readings($service_type = 'liturgy', $language = 'ar') {
        $date = current_time('Y-m-d');
        return self::get_readings_by_date($date, $service_type, $language);
    }

    /**
     * الحصول على قراءات بتاريخ محدد
     */
    public static function get_readings_by_date($date, $service_type = 'liturgy', $language = 'ar') {
        // التحقق من الكاش
        $cache_key = "katamars_readings_{$date}_{$service_type}_{$language}";
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        // استرجاع من قاعدة البيانات
        global $wpdb;
        $table = $wpdb->prefix . 'katamars_readings';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE date_gregorian = %s 
             AND service_type = %s 
             ORDER BY 
                CASE reading_type
                    WHEN 'pauline' THEN 1
                    WHEN 'catholic' THEN 2
                    WHEN 'acts' THEN 3
                    WHEN 'psalm' THEN 4
                    WHEN 'gospel' THEN 5
                    ELSE 6
                END",
            $date,
            $service_type
        ), ARRAY_A);
        
        // معالجة النتائج
        $readings = self::process_readings($results, $language);
        
        // حفظ في الكاش ليوم واحد
        set_transient($cache_key, $readings, DAY_IN_SECONDS);
        
        return $readings;
    }

    /**
     * معالجة القراءات
     */
    private static function process_readings($results, $language = 'ar') {
        $processed = [];
        
        foreach ($results as $reading) {
            $text = $language === 'en' && !empty($reading['text_english']) 
                ? $reading['text_english'] 
                : $reading['text_arabic'];
            
            $processed[] = [
                'id' => $reading['id'],
                'type' => $reading['reading_type'],
                'type_name' => self::get_reading_type_name($reading['reading_type'], $language),
                'book' => $reading['book'],
                'reference' => $reading['reference'],
                'text' => $text,
                'chapter_start' => $reading['chapter_start'],
                'verse_start' => $reading['verse_start'],
                'chapter_end' => $reading['chapter_end'],
                'verse_end' => $reading['verse_end']
            ];
        }
        
        return $processed;
    }

    /**
     * الحصول على اسم نوع القراءة
     */
    private static function get_reading_type_name($type, $language = 'ar') {
        $names = [
            'ar' => [
                'pauline' => 'البولس',
                'catholic' => 'الكاثوليكون',
                'acts' => 'الإبركسيس',
                'psalm' => 'المزمور',
                'gospel' => 'الإنجيل',
                'prophecies' => 'النبوات'
            ],
            'en' => [
                'pauline' => 'Pauline Epistle',
                'catholic' => 'Catholic Epistle',
                'acts' => 'Acts',
                'psalm' => 'Psalm',
                'gospel' => 'Gospel',
                'prophecies' => 'Prophecies'
            ]
        ];
        
        return $names[$language][$type] ?? $type;
    }

    /**
     * الحصول على جميع أنواع الخدمات
     */
    public static function get_service_types($language = 'ar') {
        $services = [
            'ar' => [
                'vespers' => 'رفع بخور عشية',
                'matins' => 'رفع بخور باكر',
                'liturgy' => 'القداس الإلهي'
            ],
            'en' => [
                'vespers' => 'Vespers',
                'matins' => 'Matins',
                'liturgy' => 'Divine Liturgy'
            ]
        ];
        
        return $services[$language] ?? $services['ar'];
    }

    /**
     * البحث في القراءات
     */
    public static function search_readings($keyword, $language = 'ar') {
        global $wpdb;
        $table = $wpdb->prefix . 'katamars_readings';
        
        $text_column = $language === 'en' ? 'text_english' : 'text_arabic';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE $text_column LIKE %s 
             OR reference LIKE %s 
             OR book LIKE %s 
             ORDER BY date_gregorian DESC 
             LIMIT 50",
            '%' . $wpdb->esc_like($keyword) . '%',
            '%' . $wpdb->esc_like($keyword) . '%',
            '%' . $wpdb->esc_like($keyword) . '%'
        ), ARRAY_A);
        
        return $results;
    }

    /**
     * تنسيق القراءة للعرض
     */
    public static function format_reading_html($reading, $options = []) {
        $defaults = [
            'show_reference' => true,
            'show_type' => true,
            'css_class' => 'katamars-reading'
        ];
        
        $options = wp_parse_args($options, $defaults);
        
        $html = '<div class="' . esc_attr($options['css_class']) . '" data-type="' . esc_attr($reading['type']) . '">';
        
        if ($options['show_type']) {
            $html .= '<h3 class="reading-type">' . esc_html($reading['type_name']) . '</h3>';
        }
        
        if ($options['show_reference']) {
            $html .= '<p class="reading-reference"><strong>' . esc_html($reading['reference']) . '</strong></p>';
        }
        
        $html .= '<div class="reading-text">' . wp_kses_post(nl2br($reading['text'])) . '</div>';
        $html .= '</div>';
        
        return $html;
    }
}