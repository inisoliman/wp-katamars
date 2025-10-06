<?php
/**
 * Plugin Name: Katamars - القطمارس القبطي
 * Plugin URI: https://github.com/inisoliman/wp-katamars
 * Description: إضافة ووردبريس شاملة لعرض القراءات اليومية والسنكسار والتقويم القبطي الأرثوذكسي مع دعم API كامل
 * Version: 2.0.0
 * Author: Ini Soliman
 * Author URI: https://github.com/inisoliman
 * Text Domain: katamars
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 */

// منع الوصول المباشر
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

// التحقق من إصدار PHP
if (version_compare(PHP_VERSION, '7.2', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo __('إضافة Katamars تتطلب PHP 7.2 أو أحدث. الإصدار الحالي: ' . PHP_VERSION, 'katamars');
        echo '</p></div>';
    });
    return;
}

// تعريف الثوابت
define('KATAMARS_VERSION', '2.0.0');
define('KATAMARS_PLUGIN_FILE', __FILE__);
define('KATAMARS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATAMARS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATAMARS_PLUGIN_BASENAME', plugin_basename(__FILE__));

// تفعيل الإضافة
function activate_katamars() {
    require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-activator.php';
    Katamars_Activator::activate();
}

// إلغاء تفعيل الإضافة
function deactivate_katamars() {
    require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-deactivator.php';
    Katamars_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_katamars');
register_deactivation_hook(__FILE__, 'deactivate_katamars');

// تحميل الكلاس الرئيسي
require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars.php';

// بدء الإضافة
function run_katamars() {
    $katamars = new Katamars();
    $katamars->run();
}

// تشغيل الإضافة بعد تحميل ووردبريس
add_action('plugins_loaded', 'run_katamars');