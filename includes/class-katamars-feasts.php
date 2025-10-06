<?php
/**
 * كلاس الأعياد والمناسبات
 */

class Katamars_Feasts {

    /**
     * الحصول على أعياد اليوم
     */
    public static function get_todays_feasts($language = 'ar') {
        $date = current_time('Y-m-d');
        $coptic_date = Katamars_Coptic_Calendar::gregorian_to_coptic($date);
        
        global $wpdb;
        $table = $wpdb->prefix . 'katamars_feasts';
        
        // البحث بالتاريخ الميلادي والقبطي
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE (date_gregorian = %s)
             OR (month_coptic = %d AND day_coptic = %d)
             ORDER BY rank_level DESC, feast_type",
            $date,
            $coptic_date['month'],
            $coptic_date['day']
        ), ARRAY_A);
        
        return self::process_feasts($results, $language);
    }

    /**
     * الحصول على الأعياد القادمة
     */
    public static function get_upcoming_feasts($limit = 5, $language = 'ar') {
        global $wpdb;
        $table = $wpdb->prefix . 'katamars_feasts';
        $today = current_time('Y-m-d');
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE date_gregorian >= %s 
             ORDER BY date_gregorian ASC 
             LIMIT %d",
            $today,
            $limit
        ), ARRAY_A);
        
        return self::process_feasts($results, $language);
    }

    /**
     * معالجة الأعياد
     */
    private static function process_feasts($results, $language = 'ar') {
        $processed = [];
        
        foreach ($results as $feast) {
            $name = $language === 'en' && !empty($feast['feast_name_en']) 
                ? $feast['feast_name_en'] 
                : $feast['feast_name_ar'];
                
            $description = $language === 'en' && !empty($feast['description_en']) 
                ? $feast['description_en'] 
                : $feast['description_ar'];
            
            $processed[] = [
                'id' => $feast['id'],
                'name' => $name,
                'type' => $feast['feast_type'],
                'type_name' => self::get_feast_type_name($feast['feast_type'], $language),
                'date' => $feast['date_gregorian'],
                'rank' => $feast['rank_level'],
                'description' => $description,
                'icon' => $feast['icon_name'],
                'color' => $feast['color_theme'],
                'fast_breaking' => (bool)$feast['fast_breaking']
            ];
        }
        
        return $processed;
    }

    /**
     * أسماء أنواع الأعياد
     */
    private static function get_feast_type_name($type, $language = 'ar') {
        $types = [
            'ar' => [
                'major' => 'عيد سيدي كبير',
                'minor' => 'عيد سيدي صغير',
                'lord' => 'عيد ربّاني',
                'virgin' => 'عيد السيدة العذراء',
                'angel' => 'عيد الملائكة',
                'apostle' => 'عيد رسولي',
                'saint' => 'عيد قديس',
                'commemoration' => 'تذكار'
            ],
            'en' => [
                'major' => 'Major Feast',
                'minor' => 'Minor Feast',
                'lord' => 'Lord's Feast',
                'virgin' => 'Virgin Mary Feast',
                'angel' => 'Angelic Feast',
                'apostle' => 'Apostolic Feast',
                'saint' => 'Saint's Feast',
                'commemoration' => 'Commemoration'
            ]
        ];
        
        return $types[$language][$type] ?? $type;
    }

    /**
     * تنسيق العيد للعرض
     */
    public static function format_feast_html($feast, $options = []) {
        $defaults = [
            'show_date' => true,
            'show_icon' => true,
            'css_class' => 'katamars-feast'
        ];
        
        $options = wp_parse_args($options, $defaults);
        
        $html = '<div class="' . esc_attr($options['css_class']) . '" 
                      data-type="' . esc_attr($feast['type']) . '"
                      style="border-color: ' . esc_attr($feast['color']) . '">';
        
        if ($options['show_icon'] && !empty($feast['icon'])) {
            $html .= '<span class="feast-icon">' . esc_html($feast['icon']) . '</span>';
        }
        
        $html .= '<h3 class="feast-name">' . esc_html($feast['name']) . '</h3>';
        $html .= '<p class="feast-type"><em>' . esc_html($feast['type_name']) . '</em></p>';
        
        if ($options['show_date']) {
            $html .= '<p class="feast-date">' . esc_html(date('j F Y', strtotime($feast['date']))) . '</p>';
        }
        
        if (!empty($feast['description'])) {
            $html .= '<div class="feast-description">' . wp_kses_post(nl2br($feast['description'])) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}