window.addEventListener('turbo:load', function() {
    tinymce.init({
        selector: '#my-editor',
        language: 'fr_FR',
        language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.10.9/langs6/fr_FR.js',
        content_css: 'dark',

        height: '100%',
        menubar: false,
        statusbar: false,
        promotion: false,
        //readonly: true, pour le visiteur

        plugins: 'lists checklist table link',

        toolbar: 'undo redo | fontfamily fontsize | bold italic underline forecolor | alignleft aligncenter alignright | checklist bullist numlist | table | link',

        font_family_formats: [
            'Inter=Inter,sans-serif',
            'Georgia=Georgia,serif',
            'Courier New=Courier New,monospace',
            'Arial=Arial,sans-serif',
            'Helvetica=Helvetica,sans-serif',
        ].join('; '),

        font_size_formats: '12px 14px 16px 18px 20px 24px 30px 36px 48px 64px',


        table_default_styles: {
            'border-collapse': 'collapse',
            'width': '100%',
        },
        table_style_by_css: true,

        setup: function (editor) {
            editor.on('init', function () {
                editor.getContainer().style.height = "100%";
            });
        },

        content_style: `
					body { 
						font-family: 'Inter', -apple-system, sans-serif; 
						
						margin: 2rem auto; 
						padding: 2rem;
						background-color: #1C1C1C;
						color: #F8F5F0;
					}

					/* Style des tableau */
					table {
						border-collapse: collapse;
						width: 100%;
						margin: 1rem 0;
					}
					th, td {
						border: 1px solid #444;
						padding: 8px 12px;
						text-align: left;
					}
					th {
						background-color: #2a2a2a;
						color: #F8F5F0;
						font-weight: 500;
					}
					tr:nth-child(even) {
						background-color: #242424;
					}
				`
    });
});