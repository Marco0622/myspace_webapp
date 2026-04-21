window.addEventListener('turbo:load', function () {
    document.getElementById('copyCode').addEventListener('click', () => {
        navigator.clipboard.writeText(document.getElementById('code-display').textContent.trim());
        const t = document.getElementById('copy-feedback');
        t.style.opacity = 1;
        setTimeout(() => t.style.opacity = 0, 2000);
    });
});