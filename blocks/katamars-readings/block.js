(function() {
    const { registerBlockType } = wp.blocks;
    const { SelectControl, ToggleControl } = wp.components;
    const { InspectorControls } = wp.blockEditor;
    const { Fragment } = wp.element;

    registerBlockType("katamars/readings", {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { service, language, showReferences, showTypes } = attributes;

            return Fragment(
                {},
                InspectorControls(
                    {},
                    SelectControl({
                        label: "نوع الخدمة",
                        value: service,
                        options: [
                            { label: "القداس الإلهي", value: "liturgy" },
                            { label: "رفع بخور باكر", value: "matins" },
                            { label: "رفع بخور عشية", value: "vespers" }
                        ],
                        onChange: (value) => setAttributes({ service: value })
                    }),
                    SelectControl({
                        label: "اللغة",
                        value: language,
                        options: [
                            { label: "العربية", value: "ar" },
                            { label: "English", value: "en" }
                        ],
                        onChange: (value) => setAttributes({ language: value })
                    }),
                    ToggleControl({
                        label: "إظهار المراجع",
                        checked: showReferences,
                        onChange: (value) => setAttributes({ showReferences: value })
                    }),
                    ToggleControl({
                        label: "إظهار أنواع القراءات",
                        checked: showTypes,
                        onChange: (value) => setAttributes({ showTypes: value })
                    })
                ),
                wp.element.createElement("div", {
                    className: "katamars-block-preview",
                    style: {
                        border: "2px dashed #ccc",
                        padding: "20px",
                        textAlign: "center",
                        borderRadius: "8px",
                        background: "#f9f9f9"
                    }
                },
                    wp.element.createElement("div", {
                        style: { fontSize: "24px", marginBottom: "10px" }
                    }, "📖"),
                    wp.element.createElement("h3", {
                        style: { margin: "10px 0", color: "#2c3e50" }
                    }, "قراءات اليوم - القطمارس"),
                    wp.element.createElement("p", {
                        style: { color: "#666", fontSize: "14px" }
                    }, `الخدمة: ${service === "liturgy" ? "القداس" : service === "matins" ? "باكر" : "عشية"}`),
                    wp.element.createElement("p", {
                        style: { color: "#666", fontSize: "14px" }
                    }, `اللغة: ${language === "ar" ? "العربية" : "الإنجليزية"}`)
                )
            );
        },

        save: function() {
            return null; // يتم الرندر في PHP
        }
    });
})();