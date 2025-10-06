<?php
/**
 * قالب صفحة إدارة السنكسار
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_synax = $wpdb->prefix . 'katamars_synaxarium';

// الحصول على السنكسار
$synaxarium = $wpdb->get_results("SELECT * FROM $table_synax ORDER BY month_coptic, day_coptic LIMIT 30");
?>

<div class="wrap">
    <div class="katamars-header">
        <h1>📜 إدارة السنكسار القبطي</h1>
        <p>سير القديسين والشهداء والأحداث الكنسية</p>
    </div>

    <div class="katamars-card">
        <h3>➕ إضافة سنكسار جديد</h3>
        <form method="post" class="katamars-form">
            <?php wp_nonce_field('katamars_add_synax'); ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>الشهر القبطي (1-13):</label>
                    <input type="number" name="month_coptic" min="1" max="13" required>
                </div>
                <div class="form-group">
                    <label>اليوم القبطي (1-30):</label>
                    <input type="number" name="day_coptic" min="1" max="30" required>
                </div>
            </div>

            <div class="form-group">
                <label>اسم القديس بالعربية:</label>
                <input type="text" name="saint_name_ar" required>
            </div>

            <div class="form-group">
                <label>اسم القديس بالإنجليزية:</label>
                <input type="text" name="saint_name_en">
            </div>

            <div class="form-group">
                <label>نوع القديس:</label>
                <select name="saint_type" required>
                    <option value="martyr">شهيد</option>
                    <option value="saint">قديس</option>
                    <option value="pope">بابا</option>
                    <option value="bishop">أسقف</option>
                    <option value="monk">راهب</option>
                    <option value="nun">راهبة</option>
                    <option value="event">حدث كنسي</option>
                </select>
            </div>

            <div class="form-group">
                <label>السيرة بالعربية:</label>
                <textarea name="story_ar" rows="8" required></textarea>
            </div>

            <div class="form-group">
                <label>السيرة بالإنجليزية:</label>
                <textarea name="story_en" rows="8"></textarea>
            </div>

            <div class="form-group">
                <label>رابط الصورة:</label>
                <input type="url" name="image_url" placeholder="https://...">
            </div>

            <button type="submit" name="katamars_add_synax" class="btn-primary">
                ➕ إضافة سنكسار
            </button>
        </form>
    </div>

    <div class="katamars-card" style="margin-top: 20px;">
        <h3>📋 السنكسار الموجود</h3>
        
        <?php if (!empty($synaxarium)): ?>
            <table class="katamars-table">
                <thead>
                    <tr>
                        <th>التاريخ القبطي</th>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($synaxarium as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item->day_coptic . '/' . $item->month_coptic); ?></td>
                        <td><?php echo esc_html($item->saint_name_ar); ?></td>
                        <td><?php echo esc_html($item->saint_type); ?></td>
                        <td>
                            <a href="#" class="button button-small">تعديل</a>
                            <a href="#" class="button button-small" style="color: red;">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>لا يوجد سنكسار بعد.</p>
        <?php endif; ?>
    </div>
</div>