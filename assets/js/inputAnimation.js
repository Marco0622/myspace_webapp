window.addEventListener('turbo:load', () => {
    const fields = document.querySelectorAll('.animate-in');
    fields.forEach((el, i) => {
        setTimeout(() => el.classList.add('visible'), i * 100);
    });
});