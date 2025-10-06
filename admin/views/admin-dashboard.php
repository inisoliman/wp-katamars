<?php
/**
 * قالب لوحة التحكم الرئيسية
 */

// التأكد من الأمان
if (!defined('ABSPATH')) {
    exit;
}

// الحصول على الإحصائيات
global $wpdb;
$stats = [
    'readings' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_readings"),
    'synaxarium' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_synaxarium"),
    'feasts' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_feasts"),
    'saints' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_saints")
];

$today = current_time('Y-m-d');
?>

<div class="wrap">
    <div class="katamars-header">
        <h1>🏛️ لوحة تحكم القطمارس القبطي</h1>
        <p>إدارة شاملة للقراءات اليومية والسنكسار والتقويم القبطي الأرثوذكسي</p>
    </div>

    <div class="katamars-alerts"></div>

    <!-- الإحصائيات السريعة -->
    <div class="katamars-dashboard">
        <div class="katamars-card">
            <h3>📊 الإحصائيات العامة</h3>
            <div class="katamars-stats">
                <div class="stat-item">
                    <span class="stat-number" id="total-readings"><?php echo number_format($stats['readings']); ?></span>
                    <span class="stat-label">القراءات</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="total-synax"><?php echo number_format($stats['synaxarium']); ?></span>
                    <span class="stat-label">السنكسار</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="total-feasts"><?php echo number_format($stats['feasts']); ?></span>
                    <span class="stat-label">الأعياد</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="total-saints"><?php echo number_format($stats['saints']); ?></span>
                    <span class="stat-label">القديسون</span>
                </div>
            </div>
        </div>

        <div class="katamars-card">
            <h3>📅 اليوم</h3>
            <p><strong>التاريخ الميلادي:</strong> <?php echo date('j F Y', strtotime($today)); ?></p>
            <p><strong>التاريخ القبطي:</strong> <span id="coptic-date">جارٍ التحميل...</span></p>
            <p><strong>الصوم:</strong> <span id="current-fast">جارٍ التحميل...</span></p>
            <p><strong>القراءات:</strong> <span id="readings-count">جارٍ التحميل...</span></p>
        </div>

        <div class="katamars-card">
            <h3>⚡ إجراءات سريعة</h3>
            <p>
                <button class="btn-primary update-readings" style="margin: 5px;">
                    🔄 تحديث القراءات
                </button>
            </p>
            <p>
                <button class="btn-primary" onclick="window.location.href='admin.php?page=katamars-readings'" style="margin: 5px;">
                    📖 إدارة القراءات
                </button>
            </p>
            <p>
                <button class="btn-primary" onclick="window.location.href='admin.php?page=katamars-synaxarium'" style="margin: 5px;">
                    📜 إدارة السنكسار
                </button>
            </p>
        </div>

        <div class="katamars-card">
            <h3>📁 إدارة البيانات</h3>
            <div style="margin-bottom: 15px;">
                <input type="file" id="import-file" accept=".json,.sql" style="margin-bottom: 10px;">
                <br>
                <button class="btn-primary import-data">📥 استيراد البيانات</button>
            </div>
            <div>
                <button class="btn-primary export-data">📤 تصدير البيانات</button>
            </div>
        </div>
    </div>

    <!-- الروابط السريعة -->
    <div class="katamars-card">
        <h3>🔗 روابط مفيدة</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="admin.php?page=katamars-readings" class="button button-secondary">
                📖 القراءات اليومية
            </a>
            <a href="admin.php?page=katamars-synaxarium" class="button button-secondary">
                📜 السنكسار
            </a>
            <a href="admin.php?page=katamars-feasts" class="button button-secondary">
                🎊 الأعياد والمناسبات
            </a>
            <a href="admin.php?page=katamars-settings" class="button button-secondary">
                ⚙️ الإعدادات
            </a>
        </div>
    </div>

    <!-- معلومات النظام -->
    <div class="katamars-card">
        <h3>ℹ️ معلومات النظام</h3>
        <table class="widefat">
            <tr>
                <td><strong>إصدار الإضافة:</strong></td>
                <td><?php echo KATAMARS_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong>إصدار ووردبريس:</strong></td>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td><strong>إصدار PHP:</strong></td>
                <td><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong>آخر تحديث:</strong></td>
                <td><?php echo get_option('katamars_last_update', 'غير محدد'); ?></td>
            </tr>
        </table>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // تحميل التاريخ القبطي
    loadCopticInfo();
    
    function loadCopticInfo() {
        $.post(ajaxurl, {
            action: 'katamars_get_coptic_info',
            date: '<?php echo $today; ?>'
        }, function(response) {
            if (response.success) {
                $('#coptic-date').text(response.data.coptic_date);
                $('#current-fast').text(response.data.fast || 'لا يوجد');
                $('#readings-count').text(response.data.readings_count + ' قراءة');
            }
        });
    }
});
</script>