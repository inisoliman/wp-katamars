<?php
/**
 * كلاس السنكسار
 */

class Katamars_Synaxarium {

    /**
     * الحصول على سنكسار اليوم
     */
    public static function get_todays_synaxarium($language = 'ar') {
        $coptic_date = Katamars_Coptic_Calendar::gregorian_to_coptic(current_time('Y-m-d'));
        return self::get_synaxarium_by_coptic_date(
            $coptic_date['month'], 
            $coptic_date['day'],
            $language
        );
    }

    /**
     * الحصول على السنكسار بتاريخ قبطي
     */
    public static function get_synaxarium_by_coptic_date($month, $day, $language = 'ar') {
        // التحقق من الكاش
        $cache_key = "katamars_synax_{$month}_{$day}_{$language}";
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached;
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'katamars_synaxarium';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE month_coptic = %d 
             AND day_coptic = %d 
             ORDER BY saint_type, saint_name_ar",
            $month,
            $day
        ), ARRAY_A);
        
        $synaxarium = self::process_synaxarium($results, $language);
        
        // حفظ في الكاش
        set_transient($cache_key, $synaxarium, DAY_IN_SECONDS);
        
        return $synaxarium;
    }

    /**
     * معالجة السنكسار
     */
    private static function process_synaxarium($results, $language = 'ar') {
        $processed = [];
        
        foreach ($results as $item) {
            $saint_name = $language === 'en' && !empty($item['saint_name_en']) 
                ? $item['saint_name_en'] 
                : $item['saint_name_ar'];
                
            $story = $language === 'en' && !empty($item['story_en']) 
                ? $item['story_en'] 
                : $item['story_ar'];
            
            $processed[] = [
                'id' => $item['id'],
                'name' => $saint_name,
                'type' => $item['saint_type'],
                'type_name' => self::get_saint_type_name($item['saint_type'], $language),
                'story' => $story,
                'image' => $item['image_url']
            ];
        }
        
        return $processed;
    }

    /**
     * أسماء أنواع القديسين
     */
    private static function get_saint_type_name($type, $language = 'ar') {
        $types = [
            'ar' => [
                'martyr' => 'شهيد',
                'saint' => 'قديس',
                'pope' => 'بابا',
                'bishop' => 'أسقف',
                'monk' => 'راهب',
                'nun' => 'راهبة',
                'event' => 'حدث كنسي'
            ],
            'en' => [
                'martyr' => 'Martyr',
                'saint' => 'Saint',
                'pope' => 'Pope',
                'bishop' => 'Bishop',
                'monk' => 'Monk',
                'nun' => 'Nun',
                'event' => 'Church Event'
            ]
        ];
        
        return $types[$language][$type] ?? $type;
    }

    /**
     * تنسيق السنكسار للعرض
     */
    public static function format_synaxarium_html($items, $options = []) {
        $defaults = [
            'show_images' => true,
            'show_type' => true,
            'css_class' => 'katamars-synaxarium'
        ];
        
        $options = wp_parse_args($options, $defaults);
        
        $html = '<div class="' . esc_attr($options['css_class']) . '">';
        
        foreach ($items as $item) {
            $html .= '<div class="synax-item" data-type="' . esc_attr($item['type']) . '">';
            
            if ($options['show_images'] && !empty($item['image'])) {
                $html .= '<img src="' . esc_url($item['image']) . '" alt="' . esc_attr($item['name']) . '" class="synax-image">';
            }
            
            $html .= '<h3 class="synax-name">' . esc_html($item['name']) . '</h3>';
            
            if ($options['show_type']) {
                $html .= '<p class="synax-type"><em>' . esc_html($item['type_name']) . '</em></p>';
            }
            
            $html .= '<div class="synax-story">' . wp_kses_post(nl2br($item['story'])) . '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * البحث في السنكسار
     */
    public static function search_synaxarium($keyword, $language = 'ar') {
        global $wpdb;
        $table = $wpdb->prefix . 'katamars_synaxarium';
        
        $name_column = $language === 'en' ? 'saint_name_en' : 'saint_name_ar';
        $story_column = $language === 'en' ? 'story_en' : 'story_ar';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE $name_column LIKE %s 
             OR $story_column LIKE %s 
             ORDER BY month_coptic, day_coptic 
             LIMIT 50",
            '%' . $wpdb->esc_like($keyword) . '%',
            '%' . $wpdb->esc_like($keyword) . '%'
        ), ARRAY_A);
        
        return $results;
    }
}