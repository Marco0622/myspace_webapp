const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#inputPassword');

togglePassword.addEventListener('click', function (e) {

    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);

    this.src = type === 'password' ? '/assets/images/icon/hide.png' : '/assets/images/icon/notHide.png';
});