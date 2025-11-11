
document.addEventListener('DOMContentLoaded', function () {
// === SCRIPT POUR LE BOUTON COPIER ===
document.querySelectorAll('.copy-btn').forEach(button => {
button.addEventListener('click', () => {
const codeBlock = button.previousElementSibling;
const code = codeBlock.innerText;

navigator.clipboard.writeText(code).then(() => {
const originalText = button.innerText;
button.innerText = 'Copié !';
setTimeout(() => {
button.innerText = originalText;
}, 2000);
}).catch(err => {
console.error('Erreur de copie : ', err);
});
});
});

// === SCRIPT POUR AFFICHER/MASQUER LA SOLUTION ===
document.querySelectorAll('.solution-toggle').forEach(button => {
button.addEventListener('click', () => {
const solutionContent = button.nextElementSibling;
if (solutionContent.style.display === 'block') {
solutionContent.style.display = 'none';
button.innerText = 'Voir la solution';
} else {
solutionContent.style.display = 'block';
button.innerText = 'Masquer la solution';
}
});
});
});
