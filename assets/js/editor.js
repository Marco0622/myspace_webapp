window.addEventListener('turbo:load', function () {

    const indicator = document.getElementById('save-indicator');
    const editor = document.getElementById('my-editor');
    const id = editor.dataset.id;
    const token = editor.dataset.token;
    const readonly = editor.dataset.bool;
    let lastContent = '';
    let timer = null;

    if (!editor) return;

    tinymce.init({
        selector: '#my-editor',
        language: 'fr_FR',
        language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@23.10.9/langs6/fr_FR.js',
        content_css: 'dark',

        height: '100%',
        menubar: false,
        statusbar: false,
        promotion: false,
        
        readonly: readonly === 'true',
           
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
            

            editor.on('input click keyup mouseup', () => {
                const contentEditor = editor.getContent();

                if (contentEditor === lastContent) return;

                lastContent = contentEditor;
                clearTimeout(timer);

                timer = setTimeout(() => {
                    const content = JSON.stringify({
                        content: contentEditor,
                        _token: token
                    });

                    sendRequest(content);
                }, 1000);
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



    function sendRequest(content) {
        console.log('save');
        fetch(`/page/save/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: content
        })
            .then(response => {
                if (!response.ok) throw new Error('Réponse serveur : ' + response.status);
                return response.json();
            })
            .then(data => {
                console.log('Succès :', data);
                showIndicator('success');
            })
            .catch(err => {
                console.error('Erreur :', err);
                showIndicator('error');
            });
    }


    function showIndicator(state) {
        indicator.className = 'save-indicator ' + state;

        if (state === 'success') {
            indicator.textContent = '✓ Sauvegardé';
            setTimeout(() => indicator.className = 'save-indicator', 2000);
        } else if (state === 'error') {
            indicator.textContent = '✕ Erreur de sauvegarde';
            setTimeout(() => indicator.className = 'save-indicator', 3000);
        }
    }
});

window.addEventListener('turbo:before-render', function () {
    if (tinymce.get('my-editor')) {
        tinymce.get('my-editor').destroy();
    }
});

