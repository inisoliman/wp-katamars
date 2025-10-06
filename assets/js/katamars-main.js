/**
 * ملف JavaScript الرئيسي لإضافة Katamars
 */

(function($) {
    "use strict";

    // تشغيل عند تحميل الصفحة
    $(document).ready(function() {
        KatamarsFrontend.init();
    });

    // الكائن الرئيسي
    var KatamarsFrontend = {
        
        // المتغيرات العامة
        settings: {
            language: "ar",
            cacheTime: 300000, // 5 دقائق
            animationSpeed: 300
        },

        // التهيئة الأساسية
        init: function() {
            this.bindEvents();
            this.loadSettings();
            this.initWidgets();
            this.setupAjax();
        },

        // ربط الأحداث
        bindEvents: function() {
            // البحث المباشر
            $(document).on("input", ".katamars-search-input", this.debounce(this.performSearch, 500));
            
            // تبديل اللغة
            $(document).on("click", ".katamars-lang-toggle", this.toggleLanguage);
            
            // عرض المزيد من النص
            $(document).on("click", ".katamars-read-more", this.toggleReadMore);
            
            // فتح النوافذ المنبثقة
            $(document).on("click", "[data-katamars-modal]", this.openModal);
            
            // إغلاق النوافذ المنبثقة
            $(document).on("click", ".katamars-modal-close, .katamars-modal-backdrop", this.closeModal);
            
            // تحديث المحتوى التلقائي
            if ($(".katamars-auto-refresh").length > 0) {
                setInterval(this.refreshContent, 300000); // كل 5 دقائق
            }

            // التمرير السلس
            $(document).on("click", "a[href^='#katamars']", this.smoothScroll);

            // نسخ النص
            $(document).on("click", ".katamars-copy-text", this.copyToClipboard);
        },

        // تحميل الإعدادات
        loadSettings: function() {
            var stored = localStorage.getItem("katamars_settings");
            if (stored) {
                try {
                    $.extend(this.settings, JSON.parse(stored));
                } catch(e) {
                    console.warn("خطأ في تحميل إعدادات Katamars");
                }
            }
        },

        // حفظ الإعدادات
        saveSettings: function() {
            localStorage.setItem("katamars_settings", JSON.stringify(this.settings));
        },

        // تهيئة الويدجتات
        initWidgets: function() {
            // تحميل التقويم القبطي
            $(".katamars-calendar-widget").each(function() {
                KatamarsFrontend.loadCopticCalendar($(this));
            });

            // تحميل القراءات اليومية
            $(".katamars-readings-widget").each(function() {
                KatamarsFrontend.loadTodayReadings($(this));
            });

            // تحميل السنكسار
            $(".katamars-synax-widget").each(function() {
                KatamarsFrontend.loadTodaySynaxarium($(this));
            });
        },

        // إعداد AJAX
        setupAjax: function() {
            $(document).ajaxStart(function() {
                $(".katamars-loading").show();
            });
            
            $(document).ajaxStop(function() {
                $(".katamars-loading").hide();
            });
        },

        // البحث المباشر
        performSearch: function() {
            var query = $(".katamars-search-input").val();
            var container = $(".katamars-search-results");
            
            if (query.length < 3) {
                container.empty();
                return;
            }

            container.html("<div class='katamars-loading'>جارٍ البحث...</div>");

            $.ajax({
                url: katamars_ajax.rest_url + "search",
                method: "GET",
                data: {
                    q: query,
                    language: KatamarsFrontend.settings.language
                },
                success: function(response) {
                    if (response.success) {
                        var html = KatamarsFrontend.formatSearchResults(response.results);
                        container.html(html);
                    } else {
                        container.html("<p>لم يتم العثور على نتائج.</p>");
                    }
                },
                error: function() {
                    container.html("<p>حدث خطأ في البحث.</p>");
                }
            });
        },

        // تنسيق نتائج البحث
        formatSearchResults: function(results) {
            var html = "<div class='katamars-search-container'>";
            
            if (results.readings && results.readings.length > 0) {
                html += "<h3>📖 القراءات (" + results.readings.length + ")</h3>";
                results.readings.forEach(function(reading) {
                    html += "<div class='katamars-search-item'>";
                    html += "<h4>" + reading.reference + "</h4>";
                    html += "<p>" + KatamarsFrontend.truncateText(reading.text_arabic, 100) + "</p>";
                    html += "</div>";
                });
            }
            
            if (results.synaxarium && results.synaxarium.length > 0) {
                html += "<h3>📜 السنكسار (" + results.synaxarium.length + ")</h3>";
                results.synaxarium.forEach(function(item) {
                    html += "<div class='katamars-search-item'>";
                    html += "<h4>" + item.saint_name_ar + "</h4>";
                    html += "<p>" + KatamarsFrontend.truncateText(item.story_ar, 100) + "</p>";
                    html += "</div>";
                });
            }
            
            html += "</div>";
            return html;
        },

        // تبديل اللغة
        toggleLanguage: function(e) {
            e.preventDefault();
            
            KatamarsFrontend.settings.language = 
                KatamarsFrontend.settings.language === "ar" ? "en" : "ar";
            
            KatamarsFrontend.saveSettings();
            
            // إعادة تحميل المحتوى
            location.reload();
        },

        // عرض/إخفاء المزيد من النص
        toggleReadMore: function(e) {
            e.preventDefault();
            
            var button = $(this);
            var content = button.siblings(".katamars-expandable-content");
            
            if (content.hasClass("expanded")) {
                content.removeClass("expanded");
                button.text("اقرأ المزيد");
            } else {
                content.addClass("expanded");
                button.text("اقرأ أقل");
            }
        },

        // فتح النافذة المنبثقة
        openModal: function(e) {
            e.preventDefault();
            
            var modalId = $(this).data("katamars-modal");
            var modal = $("#" + modalId);
            
            if (modal.length === 0) {
                // إنشاء النافذة ديناميكياً
                KatamarsFrontend.createModal(modalId, $(this).data());
                modal = $("#" + modalId);
            }
            
            modal.addClass("active");
            $("body").addClass("katamars-modal-open");
        },

        // إغلاق النافذة المنبثقة
        closeModal: function(e) {
            if ($(e.target).hasClass("katamars-modal-backdrop") || 
                $(e.target).hasClass("katamars-modal-close")) {
                
                $(".katamars-modal").removeClass("active");
                $("body").removeClass("katamars-modal-open");
            }
        },

        // إنشاء نافذة منبثقة
        createModal: function(id, data) {
            var modal = $("<div>", {
                id: id,
                class: "katamars-modal"
            });
            
            var backdrop = $("<div>", {class: "katamars-modal-backdrop"});
            var content = $("<div>", {class: "katamars-modal-content"});
            var header = $("<div>", {class: "katamars-modal-header"});
            var body = $("<div>", {class: "katamars-modal-body"});
            var close = $("<button>", {class: "katamars-modal-close", text: "×"});
            
            header.append("<h3>" + (data.title || "تفاصيل") + "</h3>").append(close);
            body.html(data.content || "جارٍ التحميل...");
            
            content.append(header).append(body);
            modal.append(backdrop).append(content);
            
            $("body").append(modal);
        },

        // التمرير السلس
        smoothScroll: function(e) {
            e.preventDefault();
            
            var target = $(this.hash);
            if (target.length) {
                $("html, body").animate({
                    scrollTop: target.offset().top - 20
                }, KatamarsFrontend.settings.animationSpeed);
            }
        },

        // نسخ النص
        copyToClipboard: function(e) {
            e.preventDefault();
            
            var text = $(this).data("text") || $(this).text();
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    KatamarsFrontend.showNotification("تم نسخ النص", "success");
                });
            } else {
                // للمتصفحات القديمة
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("copy");
                document.body.removeChild(textArea);
                
                KatamarsFrontend.showNotification("تم نسخ النص", "success");
            }
        },

        // تحميل التقويم القبطي
        loadCopticCalendar: function(container) {
            $.ajax({
                url: katamars_ajax.rest_url + "calendar",
                method: "GET",
                success: function(response) {
                    if (response.success && response.calendar) {
                        var html = KatamarsFrontend.formatCalendar(response.calendar);
                        container.html(html);
                    }
                },
                error: function() {
                    container.html("<p>خطأ في تحميل التقويم.</p>");
                }
            });
        },

        // تحميل القراءات اليومية
        loadTodayReadings: function(container) {
            var service = container.data("service") || "liturgy";
            
            $.ajax({
                url: katamars_ajax.rest_url + "readings",
                method: "GET",
                data: {
                    service: service,
                    language: KatamarsFrontend.settings.language
                },
                success: function(response) {
                    if (response.success && response.readings) {
                        var html = KatamarsFrontend.formatReadings(response.readings);
                        container.html(html);
                    }
                }
            });
        },

        // تحميل السنكسار
        loadTodaySynaxarium: function(container) {
            $.ajax({
                url: katamars_ajax.rest_url + "synaxarium",
                method: "GET",
                data: {
                    language: KatamarsFrontend.settings.language
                },
                success: function(response) {
                    if (response.success && response.synaxarium) {
                        var html = KatamarsFrontend.formatSynaxarium(response.synaxarium);
                        container.html(html);
                    }
                }
            });
        },

        // تنسيق التقويم
        formatCalendar: function(calendar) {
            var html = "<div class='katamars-calendar-display'>";
            html += "<div class='coptic-date'>" + calendar.coptic.formatted + "</div>";
            
            if (calendar.fast) {
                html += "<div class='fast-info'>";
                html += "<span class='fast-name'>" + calendar.fast.name + "</span>";
                if (calendar.fast.days_remaining) {
                    html += "<span class='days-left'>" + calendar.fast.days_remaining + " يوم متبقي</span>";
                }
                html += "</div>";
            }
            
            html += "<div class='season'>" + calendar.season.name + "</div>";
            html += "</div>";
            
            return html;
        },

        // تنسيق القراءات
        formatReadings: function(readings) {
            var html = "<div class='katamars-readings-list'>";
            
            readings.forEach(function(reading) {
                html += "<div class='reading-item'>";
                html += "<h4>" + reading.type_name + "</h4>";
                html += "<p class='reference'>" + reading.reference + "</p>";
                html += "</div>";
            });
            
            html += "</div>";
            return html;
        },

        // تنسيق السنكسار
        formatSynaxarium: function(synaxarium) {
            var html = "<div class='katamars-synax-list'>";
            
            synaxarium.forEach(function(item) {
                html += "<div class='synax-item'>";
                html += "<h4>" + item.name + "</h4>";
                html += "<p class='saint-type'>" + item.type_name + "</p>";
                html += "</div>";
            });
            
            html += "</div>";
            return html;
        },

        // تحديث المحتوى
        refreshContent: function() {
            $(".katamars-auto-refresh").each(function() {
                var widget = $(this);
                var type = widget.data("type");
                
                switch(type) {
                    case "calendar":
                        KatamarsFrontend.loadCopticCalendar(widget);
                        break;
                    case "readings":
                        KatamarsFrontend.loadTodayReadings(widget);
                        break;
                    case "synaxarium":
                        KatamarsFrontend.loadTodaySynaxarium(widget);
                        break;
                }
            });
        },

        // إظهار إشعار
        showNotification: function(message, type) {
            type = type || "info";
            
            var notification = $("<div>", {
                class: "katamars-notification katamars-notification-" + type,
                text: message
            });
            
            $("body").append(notification);
            
            setTimeout(function() {
                notification.addClass("show");
            }, 10);
            
            setTimeout(function() {
                notification.removeClass("show");
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
        },

        // قطع النص
        truncateText: function(text, length) {
            if (text.length <= length) return text;
            return text.substring(0, length) + "...";
        },

        // تأجيل تنفيذ الدالة (Debounce)
        debounce: function(func, wait) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }
    };

    // إتاحة الكائن عالمياً
    window.KatamarsFrontend = KatamarsFrontend;

})(jQuery);