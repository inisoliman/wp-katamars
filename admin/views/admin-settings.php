<?php
/**
 * قالب صفحة الإعدادات
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <div class="katamars-header">
        <h1>⚙️ إعدادات القطمارس القبطي</h1>
        <p>تخصيص وإعداد الإضافة حسب احتياجاتك</p>
    </div>

    <form method="post" action="options.php" id="katamars-settings-form">
        <?php
        settings_fields('katamars_settings_group');
        do_settings_sections('katamars-settings');
        submit_button(__('حفظ الإعدادات', 'katamars'), 'primary', 'submit', true, ['class' => 'btn-primary save-settings']);
        ?>
    </form>

    <div class="katamars-card" style="margin-top: 30px;">
        <h3>🔧 أدوات إضافية</h3>
        
        <div style="margin-bottom: 20px;">
            <h4>مسح الكاش</h4>
            <p>مسح جميع البيانات المخزنة مؤقتاً</p>
            <button class="button clear-cache" onclick="katamarsClearCache()">🗑️ مسح الكاش</button>
        </div>

        <div style="margin-bottom: 20px;">
            <h4>إعادة بناء الفهارس</h4>
            <p>تحسين أداء قاعدة البيانات</p>
            <button class="button rebuild-indexes" onclick="katamarsRebuildIndexes()">🔄 إعادة البناء</button>
        </div>

        <div style="margin-bottom: 20px;">
            <h4>تصدير الإعدادات</h4>
            <p>تصدير الإعدادات الحالية لاستخدامها لاحقاً</p>
            <button class="button export-settings" onclick="katamarsExportSettings()">📤 تصدير</button>
        </div>

        <div>
            <h4>استيراد الإعدادات</h4>
            <p>استيراد إعدادات من ملف سابق</p>
            <input type="file" id="import-settings-file" accept=".json">
            <button class="button import-settings" onclick="katamarsImportSettings()">📥 استيراد</button>
        </div>
    </div>

    <div class="katamars-card" style="margin-top: 20px;">
        <h3>ℹ️ معلومات النظام</h3>
        <table class="widefat">
            <tr>
                <td><strong>إصدار الإضافة:</strong></td>
                <td><?php echo KATAMARS_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong>مسار الإضافة:</strong></td>
                <td><code><?php echo KATAMARS_PLUGIN_DIR; ?></code></td>
            </tr>
            <tr>
                <td><strong>REST API URL:</strong></td>
                <td><code><?php echo rest_url('katamars/v1/'); ?></code></td>
            </tr>
            <tr>
                <td><strong>الكاش النشط:</strong></td>
                <td><?php 
                    $options = get_option('katamars_settings');
                    echo $options['cache_duration'] > 0 ? 'نعم' : 'لا';
                ?></td>
            </tr>
        </table>
    </div>
</div>

<script>
function katamarsClearCache() {
    if (!confirm('هل أنت متأكد من مسح الكاش؟')) return;
    
    jQuery.post(ajaxurl, {
        action: 'katamars_clear_cache',
        nonce: katamars_ajax.nonce
    }, function(response) {
        alert(response.success ? 'تم مسح الكاش بنجاح' : 'حدث خطأ');
        if (response.success) location.reload();
    });
}

function katamarsRebuildIndexes() {
    if (!confirm('هل تريد إعادة بناء الفهارس؟')) return;
    
    jQuery.post(ajaxurl, {
        action: 'katamars_rebuild_indexes',
        nonce: katamars_ajax.nonce
    }, function(response) {
        alert(response.success ? 'تم إعادة بناء الفهارس بنجاح' : 'حدث خطأ');
    });
}

function katamarsExportSettings() {
    window.location.href = ajaxurl + '?action=katamars_export_settings&nonce=' + katamars_ajax.nonce;
}

function katamarsImportSettings() {
    var file = document.getElementById('import-settings-file').files[0];
    if (!file) {
        alert('يرجى اختيار ملف');
        return;
    }
    
    var formData = new FormData();
    formData.append('action', 'katamars_import_settings');
    formData.append('nonce', katamars_ajax.nonce);
    formData.append('settings_file', file);
    
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert(response.success ? 'تم الاستيراد بنجاح' : 'حدث خطأ: ' + response.data);
            if (response.success) location.reload();
        }
    });
}
</script>