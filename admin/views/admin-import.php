<?php
/**
 * قالب صفحة استيراد قاعدة البيانات
 */

if (!defined('ABSPATH')) {
    exit;
}

// معالجة الإجراءات
if (isset($_POST['katamars_import_action']) && check_admin_referer('katamars_import_action')) {
    require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-importer.php';
    
    $action = sanitize_text_field($_POST['katamars_import_action']);
    
    switch ($action) {
        case 'download_sql':
            $result = Katamars_Importer::download_sql_from_github();
            if (is_wp_error($result)) {
                $error_message = $result->get_error_message();
            } else {
                $success_message = 'تم تحميل ملف SQL بنجاح';
                $sql_file_path = $result;
            }
            break;
            
        case 'import_database':
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['basedir'] . '/katamars_old_database.sql';
            
            if (file_exists($file_path)) {
                // إنشاء نسخة احتياطية
                $backup_file = Katamars_Importer::create_backup();
                
                // تنظيف البيانات القديمة
                if (isset($_POST['cleanup_before_import'])) {
                    Katamars_Importer::cleanup_before_import();
                }
                
                // استيراد البيانات
                $import_result = Katamars_Importer::import_old_database($file_path);
                
                if (is_wp_error($import_result)) {
                    $error_message = $import_result->get_error_message();
                } else {
                    // إصلاح التشفير العربي
                    Katamars_Importer::fix_arabic_encoding();
                    
                    $success_message = "تم استيراد {$import_result['imported']} استعلام بنجاح";
                    if (!empty($import_result['errors'])) {
                        $error_message = "عدد الأخطاء: " . count($import_result['errors']);
                    }
                }
            } else {
                $error_message = 'ملف SQL غير موجود. يرجى تحميله أولاً';
            }
            break;
    }
}

// التحقق من وجود ملف SQL
$upload_dir = wp_upload_dir();
$sql_file_path = $upload_dir['basedir'] . '/katamars_old_database.sql';
$sql_file_exists = file_exists($sql_file_path);
$sql_file_size = $sql_file_exists ? size_format(filesize($sql_file_path)) : '';

// إحصائيات البيانات الحالية
require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-importer.php';
$current_stats = Katamars_Importer::verify_import();
?>

<div class="wrap">
    <div class="katamars-header">
        <h1>📁 استيراد قاعدة البيانات القديمة</h1>
        <p>استيراد بيانات القطمارس والسنكسار من النظام القديم</p>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>✅ <?php echo esc_html($success_message); ?></strong></p>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>❌ <?php echo esc_html($error_message); ?></strong></p>
        </div>
    <?php endif; ?>

    <div class="katamars-dashboard">
        <!-- إحصائيات البيانات الحالية -->
        <div class="katamars-card">
            <h3>📈 إحصائيات البيانات الحالية</h3>
            <div class="katamars-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($current_stats['readings']); ?></span>
                    <span class="stat-label">قراءة</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($current_stats['synaxarium']); ?></span>
                    <span class="stat-label">سنكسار</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($current_stats['feasts']); ?></span>
                    <span class="stat-label">عيد</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo number_format($current_stats['saints']); ?></span>
                    <span class="stat-label">قديس</span>
                </div>
            </div>
        </div>

        <!-- حالة ملن SQL -->
        <div class="katamars-card">
            <h3>📁 حالة ملف قاعدة البيانات</h3>
            
            <?php if ($sql_file_exists): ?>
                <div class="alert alert-success">
                    <p><strong>✅ ملف SQL متوفر</strong></p>
                    <p><strong>الحجم:</strong> <?php echo esc_html($sql_file_size); ?></p>
                    <p><strong>المسار:</strong> <code><?php echo esc_html($sql_file_path); ?></code></p>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    <p><strong>❌ ملف SQL غير متوفر</strong></p>
                    <p>يجب تحميل ملف قاعدة البيانات أولاً</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="katamars-dashboard">
        <!-- خطوة 1: تحميل ملف SQL -->
        <div class="katamars-card">
            <h3>🔽 الخطوة 1: تحميل ملف قاعدة البيانات</h3>
            
            <p>تحميل ملف SQL من GitHub بشكل تلقائي</p>
            
            <form method="post" class="katamars-form">
                <?php wp_nonce_field('katamars_import_action'); ?>
                <input type="hidden" name="katamars_import_action" value="download_sql">
                
                <div class="form-group">
                    <label>🌐 رابط GitHub:</label>
                    <input type="url" readonly value="https://raw.githubusercontent.com/inisoliman/katamars/main/u626751827_katamars.sql" 
                           style="background: #f0f0f0;">
                </div>
                
                <button type="submit" class="btn-primary" <?php echo $sql_file_exists ? 'disabled' : ''; ?>>
                    <?php echo $sql_file_exists ? '✅ تم التحميل' : '📁 تحميل ملف SQL'; ?>
                </button>
            </form>
        </div>

        <!-- خطوة 2: استيراد البيانات -->
        <div class="katamars-card">
            <h3>🚀 الخطوة 2: استيراد البيانات</h3>
            
            <p>استيراد جميع بيانات القطمارس والسنكسار إلى قاعدة البيانات الجديدة</p>
            
            <form method="post" class="katamars-form" onsubmit="return confirm('هل أنت متأكد من بدء عملية الاستيراد؟ هذه العملية قد تستغرق عدة دقائق.');">;
                <?php wp_nonce_field('katamars_import_action'); ?>
                <input type="hidden" name="katamars_import_action" value="import_database">
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="cleanup_before_import" value="1" checked>
                        🗑️ تنظيف البيانات القديمة قبل الاستيراد
                    </label>
                    <small>ينصح بتفعيل هذا الخيار لضمان استيراد نظيف</small>
                </div>
                
                <div class="alert alert-info">
                    <h4>⚠️ مهم:</h4>
                    <ul>
                        <li>سيتم إنشاء نسخة احتياطية تلقائياً</li>
                        <li>لا تغلق الصفحة أثناء الاستيراد</li>
                        <li>قد تستغرق العملية عدة دقائق</li>
                    </ul>
                </div>
                
                <button type="submit" class="btn-primary" <?php echo !$sql_file_exists ? 'disabled' : ''; ?>>
                    <?php echo !$sql_file_exists ? 'احتاج ملف SQL أولاً' : '🚀 بدء الاستيراد'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- معلومات إضافية -->
    <div class="katamars-card" style="margin-top: 30px;">
        <h3>📝 معلومات مهمة</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4>📊 ما سيتم استيراده:</h4>
                <ul>
                    <li>✅ جميع قراءات القداس والصلوات</li>
                    <li>✅ سنكسار شامل للسنة كاملة</li>
                    <li>✅ جميع الأعياد والمناسبات</li>
                    <li>✅ بيانات القديسين والشهداء</li>
                </ul>
            </div>
            
            <div>
                <h4>⚠️ ملاحظات هامة:</h4>
                <ul>
                    <li>سيتم حفظ نسخة احتياطية تلقائياً</li>
                    <li>سيتم إصلاح تشفير العربي تلقائياً</li>
                    <li>العملية آمنة وقابلة للعكس</li>
                    <li>يمكن تكرار العملية عند الحاجة</li>
                </ul>
            </div>
        </div>
        
        <div class="alert alert-success" style="margin-top: 20px;">
            <p><strong>📞 دعم فني:</strong> في حالة مواجهة أي مشاكل، يرجى فتح تذكرة في 
            <a href="https://github.com/inisoliman/wp-katamars/issues" target="_blank">GitHub Issues</a></p>
        </div>
    </div>
</div>

<style>
.katamars-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.stat-item {
    flex: 1;
}

.stat-number {
    display: block;
    font-size: 2.5em;
    font-weight: bold;
    color: #27ae60;
    margin-bottom: 5px;
}

.stat-label {
    color: #7f8c8d;
    font-size: 0.9em;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin: 15px 0;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.btn-primary:disabled {
    background: #6c757d;
    cursor: not-allowed;
}
</style>