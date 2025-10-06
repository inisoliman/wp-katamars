<?php
/**
 * كلاس Widgets
 */

class Katamars_Widgets {

    /**
     * تسجيل جميع الويدجتات
     */
    public function register_widgets() {
        register_widget('Katamars_Today_Readings_Widget');
        register_widget('Katamars_Calendar_Widget');
        register_widget('Katamars_Synaxarium_Widget');
        register_widget('Katamars_Upcoming_Feasts_Widget');
    }
}

/**
 * ويدجت قراءات اليوم
 */
class Katamars_Today_Readings_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'katamars_today_readings',
            __('قراءات اليوم - Katamars', 'katamars'),
            ['description' => __('عرض قراءات اليوم من القطمارس', 'katamars')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $service = !empty($instance['service']) ? $instance['service'] : 'liturgy';
        $readings = Katamars_Readings::get_todays_readings($service, 'ar');
        
        echo '<div class="katamars-widget-readings">';
        
        if (!empty($readings)) {
            foreach ($readings as $reading) {
                echo '<div class="widget-reading-item">';
                echo '<strong>' . esc_html($reading['type_name']) . '</strong><br>';
                echo '<small>' . esc_html($reading['reference']) . '</small>';
                echo '</div>';
            }
        } else {
            echo '<p>لا توجد قراءات متاحة.</p>';
        }
        
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('قراءات اليوم', 'katamars');
        $service = !empty($instance['service']) ? $instance['service'] : 'liturgy';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('العنوان:', 'katamars'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('service')); ?>">
                <?php _e('نوع الخدمة:', 'katamars'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('service')); ?>"
                    name="<?php echo esc_attr($this->get_field_name('service')); ?>">
                <option value="liturgy" <?php selected($service, 'liturgy'); ?>>القداس الإلهي</option>
                <option value="matins" <?php selected($service, 'matins'); ?>>رفع بخور باكر</option>
                <option value="vespers" <?php selected($service, 'vespers'); ?>>رفع بخور عشية</option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['service'] = (!empty($new_instance['service'])) ? sanitize_text_field($new_instance['service']) : 'liturgy';
        return $instance;
    }
}

/**
 * ويدجت التقويم القبطي
 */
class Katamars_Calendar_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'katamars_calendar',
            __('التقويم القبطي - Katamars', 'katamars'),
            ['description' => __('عرض التاريخ القبطي والصوم', 'katamars')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $info = Katamars_Coptic_Calendar::get_day_info();
        
        echo '<div class="katamars-widget-calendar">';
        echo '<div class="widget-coptic-date">';
        echo '<strong>' . esc_html($info['coptic']['formatted']) . '</strong>';
        echo '</div>';
        
        if ($info['fast']) {
            echo '<div class="widget-fast-info">';
            echo '<span class="fast-icon">🕊️</span> ';
            echo esc_html($info['fast']['name']);
            echo '<br><small>' . esc_html($info['fast']['days_remaining']) . ' يوم متبقي</small>';
            echo '</div>';
        }
        
        echo '<div class="widget-season">';
        echo '<small>' . esc_html($info['season']['name']) . '</small>';
        echo '</div>';
        echo '</div>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('التقويم القبطي', 'katamars');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('العنوان:', 'katamars'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * ويدجت السنكسار
 */
class Katamars_Synaxarium_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'katamars_synaxarium',
            __('سنكسار اليوم - Katamars', 'katamars'),
            ['description' => __('عرض سنكسار اليوم', 'katamars')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $synaxarium = Katamars_Synaxarium::get_todays_synaxarium('ar');
        
        echo '<div class="katamars-widget-synaxarium">';
        
        if (!empty($synaxarium)) {
            foreach ($synaxarium as $item) {
                echo '<div class="widget-synax-item">';
                echo '<strong>📿 ' . esc_html($item['name']) . '</strong><br>';
                echo '<small><em>' . esc_html($item['type_name']) . '</em></small>';
                echo '</div>';
            }
        } else {
            echo '<p>لا يوجد سنكسار لهذا اليوم.</p>';
        }
        
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('سنكسار اليوم', 'katamars');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('العنوان:', 'katamars'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * ويدجت الأعياد القادمة
 */
class Katamars_Upcoming_Feasts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'katamars_upcoming_feasts',
            __('الأعياد القادمة - Katamars', 'katamars'),
            ['description' => __('عرض الأعياد القادمة', 'katamars')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $limit = !empty($instance['limit']) ? (int)$instance['limit'] : 5;
        $feasts = Katamars_Feasts::get_upcoming_feasts($limit, 'ar');
        
        echo '<div class="katamars-widget-feasts">';
        
        if (!empty($feasts)) {
            echo '<ul class="widget-feast-list">';
            foreach ($feasts as $feast) {
                echo '<li>';
                echo '<strong>🎊 ' . esc_html($feast['name']) . '</strong><br>';
                echo '<small>' . esc_html(date('j F', strtotime($feast['date']))) . '</small>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>لا توجد أعياد قادمة.</p>';
        }
        
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('الأعياد القادمة', 'katamars');
        $limit = !empty($instance['limit']) ? $instance['limit'] : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('العنوان:', 'katamars'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php _e('عدد الأعياد:', 'katamars'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" 
                   type="number" step="1" min="1" max="20" value="<?php echo esc_attr($limit); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? absint($new_instance['limit']) : 5;
        return $instance;
    }
}