<?php
/**
 * قالب صفحة إدارة القراءات
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_readings = $wpdb->prefix . 'katamars_readings';

// معالجة الإجراءات
if (isset($_POST['katamars_add_reading']) && check_admin_referer('katamars_add_reading')) {
    // إضافة قراءة جديدة
    $wpdb->insert($table_readings, [
        'date_gregorian' => sanitize_text_field($_POST['date_gregorian']),
        'date_coptic' => sanitize_text_field($_POST['date_coptic']),
        'service_type' => sanitize_text_field($_POST['service_type']),
        'reading_type' => sanitize_text_field($_POST['reading_type']),
        'book' => sanitize_text_field($_POST['book']),
        'chapter_start' => absint($_POST['chapter_start']),
        'verse_start' => absint($_POST['verse_start']),
        'chapter_end' => absint($_POST['chapter_end']),
        'verse_end' => absint($_POST['verse_end']),
        'text_arabic' => wp_kses_post($_POST['text_arabic']),
        'text_english' => wp_kses_post($_POST['text_english']),
        'reference' => sanitize_text_field($_POST['reference']),
        'season' => sanitize_text_field($_POST['season']),
        'fast_type' => sanitize_text_field($_POST['fast_type'])
    ]);
    
    echo '<div class="notice notice-success"><p>تم إضافة القراءة بنجاح!</p></div>';
}

// الحصول على القراءات
$readings = $wpdb->get_results("SELECT * FROM $table_readings ORDER BY date_gregorian DESC LIMIT 50");
?>

<div class="wrap">
    <div class="katamars-header">
        <h1>📖 إدارة القراءات اليومية</h1>
        <p>إضافة وتعديل وحذف القراءات من الكتاب المقدس</p>
    </div>

    <div class="katamars-dashboard">
        <div class="katamars-card">
            <h3>➕ إضافة قراءة جديدة</h3>
            <form method="post" class="katamars-form">
                <?php wp_nonce_field('katamars_add_reading'); ?>
                
                <div class="form-group">
                    <label>التاريخ الميلادي:</label>
                    <input type="date" name="date_gregorian" required>
                </div>

                <div class="form-group">
                    <label>التاريخ القبطي:</label>
                    <input type="text" name="date_coptic" placeholder="مثال: 15 توت 1741">
                </div>

                <div class="form-group">
                    <label>نوع الخدمة:</label>
                    <select name="service_type" required>
                        <option value="liturgy">القداس الإلهي</option>
                        <option value="matins">رفع بخور باكر</option>
                        <option value="vespers">رفع بخور عشية</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>نوع القراءة:</label>
                    <select name="reading_type" required>
                        <option value="pauline">البولس</option>
                        <option value="catholic">الكاثوليكون</option>
                        <option value="acts">الإبركسيس</option>
                        <option value="psalm">المزمور</option>
                        <option value="gospel">الإنجيل</option>
                        <option value="prophecies">النبوات</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>السفر:</label>
                    <input type="text" name="book" placeholder="مثال: إنجيل متى" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>الإصحاح من:</label>
                        <input type="number" name="chapter_start" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>العدد من:</label>
                        <input type="number" name="verse_start" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>الإصحاح إلى:</label>
                        <input type="number" name="chapter_end" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>العدد إلى:</label>
                        <input type="number" name="verse_end" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>المرجع:</label>
                    <input type="text" name="reference" placeholder="مثال: متى 5: 1-12" required>
                </div>

                <div class="form-group">
                    <label>النص بالعربية:</label>
                    <textarea name="text_arabic" rows="6" required></textarea>
                </div>

                <div class="form-group">
                    <label>النص بالإنجليزية:</label>
                    <textarea name="text_english" rows="6"></textarea>
                </div>

                <div class="form-group">
                    <label>الموسم:</label>
                    <input type="text" name="season" placeholder="مثال: الخماسين المقدسة">
                </div>

                <div class="form-group">
                    <label>نوع الصوم:</label>
                    <select name="fast_type">
                        <option value="">لا يوجد</option>
                        <option value="lent">الصوم الكبير</option>
                        <option value="advent">صوم الميلاد</option>
                        <option value="apostles">صوم الرسل</option>
                        <option value="mary">صوم العذراء</option>
                    </select>
                </div>

                <button type="submit" name="katamars_add_reading" class="btn-primary">
                    ➕ إضافة القراءة
                </button>
            </form>
        </div>

        <div class="katamars-card">
            <h3>📋 القراءات الموجودة</h3>
            
            <?php if (!empty($readings)): ?>
                <table class="katamars-table">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الخدمة</th>
                            <th>النوع</th>
                            <th>المرجع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($readings as $reading): ?>
                        <tr>
                            <td><?php echo esc_html($reading->date_gregorian); ?></td>
                            <td><?php echo esc_html($reading->service_type); ?></td>
                            <td><?php echo esc_html($reading->reading_type); ?></td>
                            <td><?php echo esc_html($reading->reference); ?></td>
                            <td>
                                <a href="#" class="button button-small">تعديل</a>
                                <a href="#" class="button button-small" style="color: red;">حذف</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>لا توجد قراءات بعد. قم بإضافة القراءة الأولى!</p>
            <?php endif; ?>
        </div>
    </div>
</div>