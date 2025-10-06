<?php
/**
 * كلاس Shortcodes
 */

class Katamars_Shortcodes {

    /**
     * تسجيل جميع Shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('katamars_today', [$this, 'today_readings_shortcode']);
        add_shortcode('katamars_calendar', [$this, 'calendar_shortcode']);
        add_shortcode('katamars_synaxarium', [$this, 'synaxarium_shortcode']);
        add_shortcode('katamars_feasts', [$this, 'feasts_shortcode']);
        add_shortcode('katamars_search', [$this, 'search_shortcode']);
    }

    /**
     * Shortcode: قراءات اليوم
     */
    public function today_readings_shortcode($atts) {
        $atts = shortcode_atts([
            'service' => 'liturgy',
            'language' => 'ar',
            'show_reference' => 'true',
            'show_type' => 'true'
        ], $atts);
        
        $readings = Katamars_Readings::get_todays_readings($atts['service'], $atts['language']);
        
        $html = '<div class="katamars-readings-container">';
        $html .= '<h2 class="katamars-title">قراءات اليوم</h2>';
        
        foreach ($readings as $reading) {
            $html .= Katamars_Readings::format_reading_html($reading, [
                'show_reference' => ($atts['show_reference'] === 'true'),
                'show_type' => ($atts['show_type'] === 'true')
            ]);
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Shortcode: التقويم
     */
    public function calendar_shortcode($atts) {
        $atts = shortcode_atts([
            'language' => 'ar'
        ], $atts);
        
        $info = Katamars_Coptic_Calendar::get_day_info();
        
        $html = '<div class="katamars-calendar">';
        $html .= '<div class="calendar-header">';
        $html .= '<h3>' . esc_html($info['day_name']['ar']) . '</h3>';
        $html .= '<p class="gregorian-date">' . esc_html($info['gregorian_formatted']) . '</p>';
        $html .= '<p class="coptic-date">' . esc_html($info['coptic']['formatted']) . '</p>';
        $html .= '</div>';
        
        if ($info['fast']) {
            $html .= '<div class="calendar-fast">';
            $html .= '<strong>الصوم:</strong> ' . esc_html($info['fast']['name']);
            $html .= ' (' . esc_html($info['fast']['days_remaining']) . ' يوم متبقي)';
            $html .= '</div>';
        }
        
        $html .= '<div class="calendar-season">';
        $html .= '<strong>الموسم:</strong> ' . esc_html($info['season']['name']);
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Shortcode: السنكسار
     */
    public function synaxarium_shortcode($atts) {
        $atts = shortcode_atts([
            'language' => 'ar',
            'show_images' => 'true'
        ], $atts);
        
        $synaxarium = Katamars_Synaxarium::get_todays_synaxarium($atts['language']);
        
        if (empty($synaxarium)) {
            return '<p>لا يوجد سنكسار لهذا اليوم.</p>';
        }
        
        return Katamars_Synaxarium::format_synaxarium_html($synaxarium, [
            'show_images' => ($atts['show_images'] === 'true')
        ]);
    }

    /**
     * Shortcode: الأعياد
     */
    public function feasts_shortcode($atts) {
        $atts = shortcode_atts([
            'upcoming' => 'true',
            'limit' => '5',
            'language' => 'ar'
        ], $atts);
        
        if ($atts['upcoming'] === 'true') {
            $feasts = Katamars_Feasts::get_upcoming_feasts($atts['limit'], $atts['language']);
            $title = 'الأعياد القادمة';
        } else {
            $feasts = Katamars_Feasts::get_todays_feasts($atts['language']);
            $title = 'أعياد اليوم';
        }
        
        $html = '<div class="katamars-feasts">';
        $html .= '<h2>' . esc_html($title) . '</h2>';
        
        if (empty($feasts)) {
            $html .= '<p>لا توجد أعياد.';
        } else {
            foreach ($feasts as $feast) {
                $html .= Katamars_Feasts::format_feast_html($feast);
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Shortcode: البحث
     */
    public function search_shortcode($atts) {
        $atts = shortcode_atts([
            'language' => 'ar'
        ], $atts);
        
        $html = '<div class="katamars-search">';
        $html .= '<form class="katamars-search-form" method="get">';
        $html .= '<input type="text" name="katamars_q" placeholder="ابحث في القراءات والسنكسار..." class="katamars-search-input">';
        $html .= '<button type="submit" class="katamars-search-btn">بحث</button>';
        $html .= '</form>';
        $html .= '<div class="katamars-search-results"></div>';
        $html .= '</div>';
        
        return $html;
    }
}