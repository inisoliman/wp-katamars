(function() {
    const { registerBlockType } = wp.blocks;
    const { ToggleControl } = wp.components;
    const { InspectorControls } = wp.blockEditor;
    const { Fragment } = wp.element;

    registerBlockType("katamars/calendar", {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { showFast, showSeason, showCopticDate } = attributes;

            return Fragment(
                {},
                InspectorControls(
                    {},
                    ToggleControl({
                        label: "إظهار التاريخ القبطي",
                        checked: showCopticDate,
                        onChange: (value) => setAttributes({ showCopticDate: value })
                    }),
                    ToggleControl({
                        label: "إظهار الصوم الحالي", 
                        checked: showFast,
                        onChange: (value) => setAttributes({ showFast: value })
                    }),
                    ToggleControl({
                        label: "إظهار الموسم الكنسي",
                        checked: showSeason,
                        onChange: (value) => setAttributes({ showSeason: value })
                    })
                ),
                wp.element.createElement("div", {
                    className: "katamars-block-preview",
                    style: {
                        border: "2px dashed #ccc",
                        padding: "20px", 
                        textAlign: "center",
                        borderRadius: "8px",
                        background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                        color: "white"
                    }
                },
                    wp.element.createElement("div", {
                        style: { fontSize: "24px", marginBottom: "10px" }
                    }, "📅"),
                    wp.element.createElement("h3", {
                        style: { margin: "10px 0" }
                    }, "التقويم القبطي"),
                    showCopticDate && wp.element.createElement("p", {}, "15 توت 1741"),
                    showFast && wp.element.createElement("p", {}, "🕊️ الصوم الكبير"),
                    showSeason && wp.element.createElement("p", {}, "الزمن العادي")
                )
            );
        },

        save: function() {
            return null;
        }
    });
})();