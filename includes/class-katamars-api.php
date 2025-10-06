<?php
/**
 * كلاس REST API
 */

class Katamars_API {

    /**
     * تسجيل المسارات
     */
    public function register_routes() {
        $namespace = 'katamars/v1';
        
        // قراءات اليوم
        register_rest_route($namespace, '/readings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_readings'],
            'permission_callback' => '__return_true'
        ]);
        
        // السنكسار
        register_rest_route($namespace, '/synaxarium', [
            'methods' => 'GET',
            'callback' => [$this, 'get_synaxarium'],
            'permission_callback' => '__return_true'
        ]);
        
        // التقويم
        register_rest_route($namespace, '/calendar', [
            'methods' => 'GET',
            'callback' => [$this, 'get_calendar'],
            'permission_callback' => '__return_true'
        ]);
        
        // الأعياد
        register_rest_route($namespace, '/feasts', [
            'methods' => 'GET',
            'callback' => [$this, 'get_feasts'],
            'permission_callback' => '__return_true'
        ]);
        
        // البحث
        register_rest_route($namespace, '/search', [
            'methods' => 'GET',
            'callback' => [$this, 'search'],
            'permission_callback' => '__return_true'
        ]);
    }

    /**
     * API: الحصول على القراءات
     */
    public function get_readings($request) {
        $date = $request->get_param('date') ?: current_time('Y-m-d');
        $service = $request->get_param('service') ?: 'liturgy';
        $language = $request->get_param('language') ?: 'ar';
        
        $readings = Katamars_Readings::get_readings_by_date($date, $service, $language);
        
        return rest_ensure_response([
            'success' => true,
            'date' => $date,
            'service' => $service,
            'language' => $language,
            'readings' => $readings
        ]);
    }

    /**
     * API: الحصول على السنكسار
     */
    public function get_synaxarium($request) {
        $month = $request->get_param('month');
        $day = $request->get_param('day');
        $language = $request->get_param('language') ?: 'ar';
        
        if ($month && $day) {
            $synaxarium = Katamars_Synaxarium::get_synaxarium_by_coptic_date($month, $day, $language);
        } else {
            $synaxarium = Katamars_Synaxarium::get_todays_synaxarium($language);
        }
        
        return rest_ensure_response([
            'success' => true,
            'synaxarium' => $synaxarium
        ]);
    }

    /**
     * API: الحصول على التقويم
     */
    public function get_calendar($request) {
        $date = $request->get_param('date') ?: current_time('Y-m-d');
        
        $info = Katamars_Coptic_Calendar::get_day_info($date);
        
        return rest_ensure_response([
            'success' => true,
            'calendar' => $info
        ]);
    }

    /**
     * API: الحصول على الأعياد
     */
    public function get_feasts($request) {
        $upcoming = $request->get_param('upcoming');
        $language = $request->get_param('language') ?: 'ar';
        
        if ($upcoming) {
            $limit = $request->get_param('limit') ?: 5;
            $feasts = Katamars_Feasts::get_upcoming_feasts($limit, $language);
        } else {
            $feasts = Katamars_Feasts::get_todays_feasts($language);
        }
        
        return rest_ensure_response([
            'success' => true,
            'feasts' => $feasts
        ]);
    }

    /**
     * API: البحث
     */
    public function search($request) {
        $keyword = $request->get_param('q');
        $type = $request->get_param('type') ?: 'all';
        $language = $request->get_param('language') ?: 'ar';
        
        if (empty($keyword)) {
            return new WP_Error('no_keyword', 'يرجى إدخال كلمة للبحث', ['status' => 400]);
        }
        
        $results = [];
        
        if ($type === 'all' || $type === 'readings') {
            $results['readings'] = Katamars_Readings::search_readings($keyword, $language);
        }
        
        if ($type === 'all' || $type === 'synaxarium') {
            $results['synaxarium'] = Katamars_Synaxarium::search_synaxarium($keyword, $language);
        }
        
        return rest_ensure_response([
            'success' => true,
            'keyword' => $keyword,
            'results' => $results
        ]);
    }
}