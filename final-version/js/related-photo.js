document.addEventListener('DOMContentLoaded', function () {
    // Sélectionner les éléments principaux
    const contactButton = document.querySelector('.photo-contact-btn'); // Bouton de contact spécifique
    const formField = document.querySelector('#field_e6lis63'); // Champ Référence photo dans le formulaire
    const popupOverlay = document.getElementById('popupOverlay'); // La popup de contact
    const popupContent = document.querySelector('.popup-salon');
    const openPopup = document.querySelector('ul li a#openPopup'); // Sélectionne le lien dans le header

    if (contactButton && formField && popupOverlay && popupContent) {
        // Gestion du clic sur le bouton de contact
        contactButton.addEventListener('click', function (event) {
            event.preventDefault();
            const referencePhoto = contactButton.getAttribute('data-ref');
            if (referencePhoto) formField.value = referencePhoto;
            popupOverlay.style.display = 'flex';
        });

        // Gestion du clic sur le lien dans le header
        if (openPopup) {
            openPopup.addEventListener('click', function (event) {
                event.preventDefault(); // Empêche la navigation par défaut
                popupOverlay.style.display = 'flex'; // Ouvrir la popup
            });
        }

        // Gestion de la fermeture en cliquant en dehors de la popup
        document.addEventListener('click', function (event) {
            const isClickInside = popupContent.contains(event.target);
            const isClickOnButton = contactButton.contains(event.target) || (openPopup && openPopup.contains(event.target));
            if (!isClickInside && !isClickOnButton) popupOverlay.style.display = 'none';
        });
    } else {
        console.error('Erreur : éléments de la popup manquants.');
    }
     // Navigation avec survol de la miniature
     const navLinks = document.querySelectorAll('.photo-nav-link');
                const previewContainer = document.querySelector('.photo-preview');
                const previewImg = document.querySelector('#photo-preview-img');

                navLinks.forEach(link => {
                    link.addEventListener('mouseover', function () {
                        const thumbUrl = link.getAttribute('data-thumb');
                        if (thumbUrl) {
                            previewImg.src = thumbUrl; // Met à jour la source de l'image
                            previewContainer.style.display = 'block'; // Affiche le conteneur
                        }
                    });

                    link.addEventListener('mouseout', function () {
                        previewContainer.style.display = 'none'; // Masque le conteneur
                    });
                });


    // Sélectionner tous les boutons fullscreen-btn
    const fullscreenBtns = document.querySelectorAll('.fullscreen-btn-related');
    console.log('Boutons fullscreen trouvés :', fullscreenBtns); // Debug

    // Code pour la gestion de la lightbox
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxRef = document.getElementById('lightbox-ref');
    const lightboxCategorie = document.getElementById('lightbox-categorie');
    let currentImageIndex = null;
    let currentImageItem = null;
    let visibleImages = [];

    // Mettre à jour les images visibles
    const images = document.querySelectorAll('.related-photos-container .image-item');
    console.log('Images visibles:', images); // Debug

    // Remplir visibleImages avec les éléments de la galerie visibles (par exemple les deux images visibles sur la page)
    function updateVisibleImages() {
        visibleImages = [];
        images.forEach((image) => {
            if (image.style.display !== 'none') { // Filtrer les images visibles
                visibleImages.push(image);
            }
        });
        console.log('Images visibles mises à jour:', visibleImages); // Debug
    }

    // Fonction pour ouvrir la lightbox avec l'image correspondante
    function openLightbox(imageId) {
        updateVisibleImages();  // Met à jour les images visibles
        console.log('Images visibles après mise à jour:', visibleImages); // Debug

        // Afficher tous les IDs des images pour le débogage
        visibleImages.forEach((img) => {
            console.log('ID de l\'image:', img.getAttribute('data-id')); // Debug
        });

        const image = visibleImages.find((img) => img.getAttribute('data-id') === imageId);
        if (!image) {
            console.log('Image non trouvée:', imageId); // Debug
            return;
        }

        const imageUrl = image.querySelector('img').src;
        const imageRef = image.querySelector('.reference-photo').textContent;
        const imageCategorie = image.querySelector('.categorie-photo').textContent;

        console.log('Ouvrir la lightbox avec:', imageUrl, imageRef, imageCategorie); // Debug

        lightbox.style.display = 'flex';  // Ouvre la lightbox
        lightboxImg.src = imageUrl;  // Charge l'image
        lightboxRef.textContent = imageRef;  // Affiche la référence photo
        lightboxCategorie.textContent = imageCategorie;  // Affiche la catégorie

        currentImageItem = image;  // Mise à jour de l'image actuelle
        currentImageIndex = visibleImages.indexOf(image);  // Mise à jour de l'index de l'image visible
    }

    // Fonction pour naviguer à l'image suivante parmi les images visibles
    function nextImage() {
        if (visibleImages.length === 0) return;
        currentImageIndex = (currentImageIndex + 1) % visibleImages.length;  // Calcul de l'image suivante
        const nextImage = visibleImages[currentImageIndex];
        const imageId = nextImage.getAttribute('data-id');
        openLightbox(imageId);  // Ouvre la lightbox avec la nouvelle image
    }

    // Fonction pour naviguer à l'image précédente parmi les images visibles
    function prevImage() {
        if (visibleImages.length === 0) return;
        currentImageIndex = (currentImageIndex - 1 + visibleImages.length) % visibleImages.length;  // Calcul de l'image précédente
        const prevImage = visibleImages[currentImageIndex];
        const imageId = prevImage.getAttribute('data-id');
        openLightbox(imageId);  // Ouvre la lightbox avec la nouvelle image
    }

    // Ajout des écouteurs d'événements pour les boutons fullscreen-btn
    fullscreenBtns.forEach((btn) => {
        const imageId = btn.getAttribute('data-id');
        console.log('ID de l\'image pour ce bouton:', imageId); // Debug
        if (imageId) {
            btn.addEventListener('click', function () {
                openLightbox(imageId);  // Ouvre la lightbox avec l'image correspondante
            });
        }
    });

    // Ajout des événements pour la navigation dans la lightbox
    const lightboxPrevBtn = document.querySelector('.lightbox-arrow.left');
    const lightboxNextBtn = document.querySelector('.lightbox-arrow.right');
    const lightboxCloseBtn = document.querySelector('.lightbox-close');

    if (lightboxPrevBtn) {
        lightboxPrevBtn.addEventListener('click', prevImage);
    }
    if (lightboxNextBtn) {
        lightboxNextBtn.addEventListener('click', nextImage);
    }
    if (lightboxCloseBtn) {
        lightboxCloseBtn.addEventListener('click', function () {
            lightbox.style.display = 'none';  // Fermer la lightbox
        });
    }

    // Fermer la lightbox lorsque l'utilisateur clique en dehors de l'image
    lightbox.addEventListener('click', function (event) {
        if (event.target === lightbox) {
            lightbox.style.display = 'none';  // Fermer la lightbox
        }
    });
});
