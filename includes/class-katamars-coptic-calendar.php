<?php
/**
 * كلاس التقويم القبطي
 */

class Katamars_Coptic_Calendar {

    /**
     * تحويل التاريخ الميلادي إلى قبطي
     */
    public static function gregorian_to_coptic($date) {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        
        // حساب السنة القبطية
        $coptic_year = $year - 284;
        
        // حساب يوم السنة
        $day_of_year = date('z', $timestamp) + 1;
        
        // تعديل للسنة الكبيسة
        if (self::is_leap_year($year) && $day_of_year > 244) {
            $day_of_year--;
        }
        
        // حساب الشهر القبطي
        $coptic_month = 1;
        $coptic_day = $day_of_year;
        
        if ($day_of_year <= 244) {
            $coptic_month = floor(($day_of_year - 1) / 30) + 1;
            $coptic_day = (($day_of_year - 1) % 30) + 1;
        } else {
            $coptic_month = 13;
            $coptic_day = $day_of_year - 244;
            
            if ($coptic_day > 5 && !self::is_coptic_leap_year($coptic_year)) {
                $coptic_year++;
                $coptic_month = 1;
                $coptic_day = 1;
            } elseif ($coptic_day > 6) {
                $coptic_year++;
                $coptic_month = 1;
                $coptic_day = 1;
            }
        }
        
        return [
            'year' => $coptic_year,
            'month' => $coptic_month,
            'day' => $coptic_day,
            'formatted' => self::format_coptic_date($coptic_day, $coptic_month, $coptic_year)
        ];
    }

    /**
     * التحقق من السنة الكبيسة الميلادية
     */
    private static function is_leap_year($year) {
        return (($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0);
    }

    /**
     * التحقق من السنة الكبيسة القبطية
     */
    private static function is_coptic_leap_year($coptic_year) {
        return ($coptic_year % 4 == 3);
    }

    /**
     * تنسيق التاريخ القبطي
     */
    public static function format_coptic_date($day, $month, $year) {
        $months = [
            1 => 'توت', 2 => 'بابه', 3 => 'هاتور', 4 => 'كيهك',
            5 => 'طوبة', 6 => 'أمشير', 7 => 'برمهات', 8 => 'برمودة',
            9 => 'بشنس', 10 => 'بؤونة', 11 => 'أبيب', 12 => 'مسرى',
            13 => 'النسيء'
        ];
        
        $month_name = $months[$month] ?? '';
        return "$day $month_name $year";
    }

    /**
     * الحصول على الشهر القبطي بالإنجليزية
     */
    public static function get_coptic_month_en($month) {
        $months_en = [
            1 => 'Tout', 2 => 'Baba', 3 => 'Hatur', 4 => 'Kiahk',
            5 => 'Touba', 6 => 'Amshir', 7 => 'Baramhat', 8 => 'Baramouda',
            9 => 'Bashans', 10 => 'Paona', 11 => 'Epip', 12 => 'Mesra',
            13 => 'Nasie'
        ];
        
        return $months_en[$month] ?? '';
    }

    /**
     * الحصول على معلومات اليوم
     */
    public static function get_day_info($date = null) {
        if (!$date) {
            $date = current_time('Y-m-d');
        }
        
        $coptic = self::gregorian_to_coptic($date);
        $fast = self::get_current_fast($date);
        $season = self::get_liturgical_season($date);
        
        return [
            'gregorian' => date('Y-m-d', strtotime($date)),
            'gregorian_formatted' => date('j F Y', strtotime($date)),
            'coptic' => $coptic,
            'fast' => $fast,
            'season' => $season,
            'day_name' => self::get_coptic_day_name(date('w', strtotime($date)))
        ];
    }

    /**
     * الحصول على الصوم الحالي
     */
    public static function get_current_fast($date = null) {
        if (!$date) {
            $date = current_time('Y-m-d');
        }
        
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        
        // حساب عيد القيامة
        $easter = self::calculate_easter($year);
        $easter_ts = strtotime($easter);
        
        // الصوم الكبير (55 يوم قبل العيد)
        $lent_start = strtotime('-55 days', $easter_ts);
        $lent_end = $easter_ts;
        
        if ($timestamp >= $lent_start && $timestamp < $lent_end) {
            $days_remaining = floor(($lent_end - $timestamp) / 86400);
            return [
                'name' => 'الصوم الكبير',
                'name_en' => 'Great Lent',
                'days_remaining' => $days_remaining,
                'type' => 'lent'
            ];
        }
        
        // صوم الميلاد (43 يوم قبل 7 يناير)
        $christmas = strtotime("$year-01-07");
        $advent_start = strtotime('-43 days', $christmas);
        
        if ($timestamp >= $advent_start && $timestamp < $christmas) {
            $days_remaining = floor(($christmas - $timestamp) / 86400);
            return [
                'name' => 'صوم الميلاد',
                'name_en' => 'Advent',
                'days_remaining' => $days_remaining,
                'type' => 'advent'
            ];
        }
        
        // صوم الرسل
        $apostles_start = strtotime('+50 days', $easter_ts);
        $apostles_end = strtotime("$year-07-12");
        
        if ($timestamp >= $apostles_start && $timestamp < $apostles_end) {
            $days_remaining = floor(($apostles_end - $timestamp) / 86400);
            return [
                'name' => 'صوم الرسل',
                'name_en' => 'Apostles Fast',
                'days_remaining' => $days_remaining,
                'type' => 'apostles'
            ];
        }
        
        // صوم السيدة العذراء (15 يوم قبل 22 أغسطس)
        $assumption = strtotime("$year-08-22");
        $mary_start = strtotime('-15 days', $assumption);
        
        if ($timestamp >= $mary_start && $timestamp < $assumption) {
            $days_remaining = floor(($assumption - $timestamp) / 86400);
            return [
                'name' => 'صوم السيدة العذراء',
                'name_en' => 'Assumption Fast',
                'days_remaining' => $days_remaining,
                'type' => 'mary'
            ];
        }
        
        return null;
    }

    /**
     * حساب عيد القيامة
     */
    private static function calculate_easter($year) {
        // معادلة Meeus/Jones/Butcher لحساب عيد القيامة
        $a = $year % 4;
        $b = $year % 7;
        $c = $year % 19;
        $d = (19 * $c + 15) % 30;
        $e = (2 * $a + 4 * $b - $d + 34) % 7;
        $month = floor(($d + $e + 114) / 31);
        $day = (($d + $e + 114) % 31) + 1;
        
        // إضافة 13 يوم للتقويم القبطي (شرقي)
        $easter = strtotime("$year-$month-$day +13 days");
        
        return date('Y-m-d', $easter);
    }

    /**
     * الحصول على الموسم الليتورجي
     */
    private static function get_liturgical_season($date) {
        $timestamp = strtotime($date);
        $year = date('Y', $timestamp);
        $easter = strtotime(self::calculate_easter($year));
        
        // موسم القيامة
        if ($timestamp >= $easter && $timestamp < strtotime('+50 days', $easter)) {
            return [
                'name' => 'موسم القيامة',
                'name_en' => 'Easter Season',
                'color' => 'white'
            ];
        }
        
        // موسم الصوم الكبير
        $lent_start = strtotime('-55 days', $easter);
        if ($timestamp >= $lent_start && $timestamp < $easter) {
            return [
                'name' => 'موسم الصوم الكبير',
                'name_en' => 'Lenten Season',
                'color' => 'purple'
            ];
        }
        
        // موسم الميلاد
        if (date('m-d', $timestamp) >= '12-25' || date('m-d', $timestamp) <= '01-19') {
            return [
                'name' => 'موسم الميلاد',
                'name_en' => 'Christmas Season',
                'color' => 'gold'
            ];
        }
        
        // الزمن العادي
        return [
            'name' => 'الزمن العادي',
            'name_en' => 'Ordinary Time',
            'color' => 'green'
        ];
    }

    /**
     * اسم اليوم بالقبطي
     */
    private static function get_coptic_day_name($day_num) {
        $days = [
            0 => ['ar' => 'الأحد', 'en' => 'Sunday', 'cop' => 'Ⲕⲩⲣⲓⲁⲕⲏ'],
            1 => ['ar' => 'الإثنين', 'en' => 'Monday', 'cop' => 'Ⲡⲉⲥⲛⲁⲩ'],
            2 => ['ar' => 'الثلاثاء', 'en' => 'Tuesday', 'cop' => 'Ⲡϣⲟⲙⲧ'],
            3 => ['ar' => 'الأربعاء', 'en' => 'Wednesday', 'cop' => 'Ⲡⲉϥⲧⲟⲟⲩ'],
            4 => ['ar' => 'الخميس', 'en' => 'Thursday', 'cop' => 'Ⲡϯⲟⲩ'],
            5 => ['ar' => 'الجمعة', 'en' => 'Friday', 'cop' => 'Ⲡⲁⲣⲁⲥⲕⲉⲩⲏ'],
            6 => ['ar' => 'السبت', 'en' => 'Saturday', 'cop' => 'Ⲡⲥⲁⲃⲃⲁⲧⲟⲛ']
        ];
        
        return $days[$day_num] ?? $days[0];
    }
}