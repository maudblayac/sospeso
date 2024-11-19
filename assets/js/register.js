function togglePasswordVisibility() {
    const passwordInput = document.getElementById('passwordInput');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
}

// Vérification des critères de mot de passe
document.getElementById('passwordInput').addEventListener('input', function (e) {
    const value = e.target.value;
    // ASCII TABLE
    document.querySelector('#passwordHints').innerHTML = `
        <p class="${value.length >= 8 ? 'valid' : 'invalid'}">• Minimum 8 caractères</p>
        <p class="${/[A-Z]/.test(value) ? 'valid' : 'invalid'}">• Au moins une majuscule</p>
        <p class="${/[^a-zA-Z0-9]/.test(value) ? 'valid' : 'invalid'}">• Au moins un caractère spécial</p>
    `;
});
