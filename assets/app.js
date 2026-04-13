import './stimulus_bootstrap.js';

// Import de Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import '@popperjs/core';

// Import du CSS de l'application
import './styles/app.css';


document.addEventListener('turbo:load', function () {
    document.querySelectorAll('.modal').forEach(function (modal) {
        document.body.appendChild(modal);
    });
});