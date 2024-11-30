document.addEventListener('DOMContentLoaded', function() {
    const openButton = document.getElementById('openPopup'); // Sélectionner l'élément qui ouvre la popup
    const popupOverlay = document.getElementById('popupOverlay'); // La popup à afficher

    // Ajouter un gestionnaire d'événements sur le bouton pour ouvrir la popup
    openButton.addEventListener('click', function(e) {
        e.preventDefault(); // Empêche le comportement par défaut du lien
        popupOverlay.style.display = 'flex'; // Affiche la popup (avec Flexbox pour centrer)
    });

    // Ajouter un gestionnaire d'événements sur la superposition pour fermer la popup
    popupOverlay.addEventListener('click', function(e) {
        // Vérifie si le clic s'est produit directement sur la superposition (pas à l'intérieur de la popup elle-même)
        if (e.target === popupOverlay) {
            popupOverlay.style.display = 'none'; // Cache la popup
        }
    });

    // Hamburger menu script
    const hamburger = document.querySelector('.hamburger-menu');
    const menu = document.querySelector('.header_btn');
    hamburger.addEventListener('click', function() {
        menu.classList.toggle('active');
    });
});
