<?php
/**
 * قالب صفحة إدارة الأعياد
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_feasts = $wpdb->prefix . 'katamars_feasts';

// الحصول على الأعياد
$feasts = $wpdb->get_results("SELECT * FROM $table_feasts ORDER BY rank_level DESC, date_gregorian LIMIT 50");
?>

<div class="wrap">
    <div class="katamars-header">
        <h1>🎊 إدارة الأعياد والمناسبات</h1>
        <p>الأعياد السيدية والقديسين والمناسبات الكنسية</p>
    </div>

    <div class="katamars-card">
        <h3>➕ إضافة عيد جديد</h3>
        <form method="post" class="katamars-form">
            <?php wp_nonce_field('katamars_add_feast'); ?>
            
            <div class="form-group">
                <label>اسم العيد بالعربية:</label>
                <input type="text" name="feast_name_ar" required>
            </div>

            <div class="form-group">
                <label>اسم العيد بالإنجليزية:</label>
                <input type="text" name="feast_name_en">
            </div>

            <div class="form-group">
                <label>نوع العيد:</label>
                <select name="feast_type" required>
                    <option value="major">عيد سيدي كبير</option>
                    <option value="minor">عيد سيدي صغير</option>
                    <option value="lord">عيد ربّاني</option>
                    <option value="virgin">عيد السيدة العذراء</option>
                    <option value="angel">عيد الملائكة</option>
                    <option value="apostle">عيد رسولي</option>
                    <option value="saint">عيد قديس</option>
                    <option value="commemoration">تذكار</option>
                </select>
            </div>

            <div class="form-group">
                <label>التاريخ الميلادي:</label>
                <input type="date" name="date_gregorian">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>الشهر القبطي:</label>
                    <input type="number" name="month_coptic" min="1" max="13">
                </div>
                <div class="form-group">
                    <label>اليوم القبطي:</label>
                    <input type="number" name="day_coptic" min="1" max="30">
                </div>
            </div>

            <div class="form-group">
                <label>مستوى الأهمية (1-5):</label>
                <input type="number" name="rank_level" min="1" max="5" value="3" required>
            </div>

            <div class="form-group">
                <label>الوصف بالعربية:</label>
                <textarea name="description_ar" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>الوصف بالإنجليزية:</label>
                <textarea name="description_en" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>لون المظهر:</label>
                <select name="color_theme">
                    <option value="gold">ذهبي</option>
                    <option value="white">أبيض</option>
                    <option value="red">أحمر</option>
                    <option value="green">أخضر</option>
                    <option value="purple">بنفسجي</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="fast_breaking" value="1">
                    إفطار (يُفطر في هذا العيد)
                </label>
            </div>

            <button type="submit" name="katamars_add_feast" class="btn-primary">
                ➕ إضافة العيد
            </button>
        </form>
    </div>

    <div class="katamars-card" style="margin-top: 20px;">
        <h3>📋 الأعياد الموجودة</h3>
        
        <?php if (!empty($feasts)): ?>
            <table class="katamars-table">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>التاريخ</th>
                        <th>الأهمية</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feasts as $feast): ?>
                    <tr>
                        <td><?php echo esc_html($feast->feast_name_ar); ?></td>
                        <td><?php echo esc_html($feast->feast_type); ?></td>
                        <td><?php echo esc_html($feast->date_gregorian); ?></td>
                        <td><?php echo str_repeat('⭐', $feast->rank_level); ?></td>
                        <td>
                            <a href="#" class="button button-small">تعديل</a>
                            <a href="#" class="button button-small" style="color: red;">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>لا توجد أعياد بعد.</p>
        <?php endif; ?>
    </div>
</div>