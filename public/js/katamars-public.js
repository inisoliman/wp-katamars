(function($) {
    "use strict";

    $(document).ready(function() {
        KatamarsFrontend.init();
    });

    var KatamarsFrontend = {
        
        init: function() {
            this.bindEvents();
            this.loadDynamicContent();
        },

        bindEvents: function() {
            // البحث
            $(document).on("submit", ".katamars-search-form", function(e) {
                e.preventDefault();
                KatamarsFrontend.performSearch();
            });

            // تبديل اللغة
            $(document).on("click", ".katamars-lang-toggle", function(e) {
                e.preventDefault();
                KatamarsFrontend.toggleLanguage();
            });

            // عرض المزيد من النص
            $(document).on("click", ".synax-read-more", function(e) {
                e.preventDefault();
                $(this).prev(".synax-story").toggleClass("expanded");
                $(this).text(function(i, text) {
                    return text === "اقرأ المزيد" ? "اقرأ أقل" : "اقرأ المزيد";
                });
            });
        },

        loadDynamicContent: function() {
            // تحميل المحتوى الديناميكي إذا كان مطلوباً
            $(".katamars-dynamic-readings").each(function() {
                var container = $(this);
                var date = container.data("date") || "";
                var service = container.data("service") || "liturgy";
                
                KatamarsFrontend.loadReadings(container, date, service);
            });
        },

        loadReadings: function(container, date, service) {
            $.ajax({
                url: katamars_public.rest_url + "readings",
                type: "GET",
                data: {
                    date: date,
                    service: service,
                    language: "ar"
                },
                beforeSend: function() {
                    container.html("<div class='loading'>جارٍ التحميل...</div>");
                },
                success: function(response) {
                    if (response.success && response.readings) {
                        var html = KatamarsFrontend.formatReadingsHTML(response.readings);
                        container.html(html);
                    } else {
                        container.html("<p>لا توجد قراءات متاحة.</p>");
                    }
                },
                error: function() {
                    container.html("<p>حدث خطأ في تحميل القراءات.</p>");
                }
            });
        },

        formatReadingsHTML: function(readings) {
            var html = "";
            
            readings.forEach(function(reading) {
                html += "<div class='katamars-reading'>";
                html += "<h3 class='reading-type'>" + reading.type_name + "</h3>";
                html += "<p class='reading-reference'><strong>" + reading.reference + "</strong></p>";
                html += "<div class='reading-text'>" + reading.text.replace(/\n/g, "<br>") + "</div>";
                html += "</div>";
            });
            
            return html;
        },

        performSearch: function() {
            var keyword = $(".katamars-search-input").val();
            
            if (keyword.length < 3) {
                alert("يرجى إدخال 3 أحرف على الأقل للبحث");
                return;
            }
            
            $.ajax({
                url: katamars_public.rest_url + "search",
                type: "GET",
                data: {
                    q: keyword,
                    type: "all",
                    language: "ar"
                },
                beforeSend: function() {
                    $(".katamars-search-results").html("<div class='loading'>جارٍ البحث...</div>");
                },
                success: function(response) {
                    if (response.success) {
                        var html = KatamarsFrontend.formatSearchResults(response.results);
                        $(".katamars-search-results").html(html);
                    }
                },
                error: function() {
                    $(".katamars-search-results").html("<p>حدث خطأ في البحث.</p>");
                }
            });
        },

        formatSearchResults: function(results) {
            var html = "<div class='search-results-container'>";
            
            if (results.readings && results.readings.length > 0) {
                html += "<h3>القراءات (" + results.readings.length + ")</h3>";
                results.readings.forEach(function(reading) {
                    html += "<div class='search-result-item'>";
                    html += "<strong>" + reading.reference + "</strong>";
                    html += "<p>" + reading.text_arabic.substring(0, 150) + "...</p>";
                    html += "</div>";
                });
            }
            
            if (results.synaxarium && results.synaxarium.length > 0) {
                html += "<h3>السنكسار (" + results.synaxarium.length + ")</h3>";
                results.synaxarium.forEach(function(item) {
                    html += "<div class='search-result-item'>";
                    html += "<strong>" + item.saint_name_ar + "</strong>";
                    html += "<p>" + item.story_ar.substring(0, 150) + "...</p>";
                    html += "</div>";
                });
            }
            
            if ((!results.readings || results.readings.length === 0) && 
                (!results.synaxarium || results.synaxarium.length === 0)) {
                html += "<p>لم يتم العثور على نتائج.</p>";
            }
            
            html += "</div>";
            
            return html;
        },

        toggleLanguage: function() {
            var currentLang = $("body").attr("data-katamars-lang") || "ar";
            var newLang = currentLang === "ar" ? "en" : "ar";
            
            $("body").attr("data-katamars-lang", newLang);
            
            // إعادة تحميل المحتوى باللغة الجديدة
            location.reload();
        }
    };

})(jQuery);