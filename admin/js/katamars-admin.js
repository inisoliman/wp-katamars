(function($) {
    "use strict";

    $(document).ready(function() {
        // إعداد الإدارة
        KatamarsDashboard.init();
    });

    var KatamarsDashboard = {
        
        init: function() {
            this.bindEvents();
            this.loadDashboardStats();
        },

        bindEvents: function() {
            // حفظ الإعدادات
            $(document).on("click", ".save-settings", function(e) {
                e.preventDefault();
                KatamarsDashboard.saveSettings();
            });

            // تحديث البيانات
            $(document).on("click", ".update-readings", function(e) {
                e.preventDefault();
                KatamarsDashboard.updateReadings();
            });

            // استيراد البيانات
            $(document).on("click", ".import-data", function(e) {
                e.preventDefault();
                KatamarsDashboard.importData();
            });

            // تصدير البيانات
            $(document).on("click", ".export-data", function(e) {
                e.preventDefault();
                KatamarsDashboard.exportData();
            });
        },

        loadDashboardStats: function() {
            $.ajax({
                url: katamars_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "katamars_get_stats",
                    nonce: katamars_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $("#total-readings").text(response.data.readings);
                        $("#total-synax").text(response.data.synaxarium);
                        $("#total-feasts").text(response.data.feasts);
                        $("#total-saints").text(response.data.saints);
                    }
                }
            });
        },

        saveSettings: function() {
            var formData = $("#katamars-settings-form").serialize();
            
            $.ajax({
                url: katamars_ajax.ajax_url,
                type: "POST",
                data: formData + "&action=katamars_save_settings&nonce=" + katamars_ajax.nonce,
                beforeSend: function() {
                    $(".save-settings").prop("disabled", true).text("جارٍ الحفظ...");
                },
                success: function(response) {
                    if (response.success) {
                        KatamarsDashboard.showAlert("تم حفظ الإعدادات بنجاح", "success");
                    } else {
                        KatamarsDashboard.showAlert("خطأ في حفظ الإعدادات", "error");
                    }
                },
                complete: function() {
                    $(".save-settings").prop("disabled", false).text("حفظ الإعدادات");
                }
            });
        },

        updateReadings: function() {
            $.ajax({
                url: katamars_ajax.ajax_url,
                type: "POST",
                data: {
                    action: "katamars_update_readings",
                    nonce: katamars_ajax.nonce
                },
                beforeSend: function() {
                    $(".update-readings").prop("disabled", true).text("جارٍ التحديث...");
                },
                success: function(response) {
                    if (response.success) {
                        KatamarsDashboard.showAlert("تم تحديث القراءات بنجاح", "success");
                        KatamarsDashboard.loadDashboardStats();
                    } else {
                        KatamarsDashboard.showAlert("خطأ في تحديث القراءات", "error");
                    }
                },
                complete: function() {
                    $(".update-readings").prop("disabled", false).text("تحديث القراءات");
                }
            });
        },

        importData: function() {
            var fileInput = document.getElementById("import-file");
            if (!fileInput.files.length) {
                KatamarsDashboard.showAlert("يرجى اختيار ملف للاستيراد", "error");
                return;
            }

            var formData = new FormData();
            formData.append("action", "katamars_import_data");
            formData.append("nonce", katamars_ajax.nonce);
            formData.append("import_file", fileInput.files[0]);

            $.ajax({
                url: katamars_ajax.ajax_url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $(".import-data").prop("disabled", true).text("جارٍ الاستيراد...");
                },
                success: function(response) {
                    if (response.success) {
                        KatamarsDashboard.showAlert("تم استيراد البيانات بنجاح", "success");
                        KatamarsDashboard.loadDashboardStats();
                    } else {
                        KatamarsDashboard.showAlert("خطأ في استيراد البيانات: " + response.data, "error");
                    }
                },
                complete: function() {
                    $(".import-data").prop("disabled", false).text("استيراد البيانات");
                }
            });
        },

        exportData: function() {
            window.location.href = katamars_ajax.ajax_url + "?action=katamars_export_data&nonce=" + katamars_ajax.nonce;
        },

        showAlert: function(message, type) {
            var alertClass = "alert-" + type;
            var alertHtml = "<div class='alert " + alertClass + "'>" + message + "</div>";
            
            $(".katamars-alerts").html(alertHtml);
            
            setTimeout(function() {
                $(".alert").fadeOut();
            }, 3000);
        }
    };

})(jQuery);