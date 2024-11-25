document.addEventListener("DOMContentLoaded", function () {
    const formatSelect = document.getElementById("format-select");
    const categorieSelect = document.getElementById("categorie-select");
    const dateSelect = document.getElementById("date-select");
    const imagesContainer = document.getElementById("galerie"); // Conteneur des images
    const loadMoreBtn = document.getElementById("load-more");
    const noImagesMessage = document.getElementById("no-images");
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightbox-img");
    const lightboxRef = document.getElementById("lightbox-ref");
    const lightboxCategorie = document.getElementById("lightbox-categorie");

    let visibleCount = 8;
    let currentImageIndex = null;
    let allImages = Array.from(imagesContainer.children); // Récupérer toutes les images, sans filtre au départ
    let filteredImages = []; // Tableau pour les images filtrées

    // Fonction pour filtrer et trier les images
    function filterImages() {
        const selectedFormat = formatSelect ? formatSelect.value : '';
        const selectedCategorie = categorieSelect ? categorieSelect.value : '';
        const selectedDate = dateSelect ? dateSelect.value : '';  // Laisser vide ou "oldest" par défaut

        // Filtrer les images
        filteredImages = allImages.filter((image) => {
            const imageFormat = image.getAttribute("data-format");
            const imageCategorie = image.getAttribute("data-categorie");

            const matchesFormat = !selectedFormat || imageFormat === selectedFormat;
            const matchesCategorie = !selectedCategorie || imageCategorie === selectedCategorie;

            return matchesFormat && matchesCategorie;
        });

        // Appliquer le tri par date selon le filtre sélectionné
        if (selectedDate === "recent") {
            filteredImages.sort((a, b) => new Date(b.getAttribute("data-date")) - new Date(a.getAttribute("data-date")));
        } else if (selectedDate === "oldest") {
            filteredImages.sort((a, b) => new Date(a.getAttribute("data-date")) - new Date(b.getAttribute("data-date")));
        }

        // Réorganiser les images dans le DOM
        imagesContainer.innerHTML = ''; // Vider le conteneur des images
        filteredImages.slice(0, visibleCount).forEach((image) => {
            image.style.display = 'block'; // Réafficher chaque image filtrée
            imagesContainer.appendChild(image); // Afficher les premières images filtrées
        });

        // Mettre à jour le bouton "Charger plus" et le message "Aucune image"
        if (filteredImages.length > visibleCount) {
            loadMoreBtn.style.display = "block"; // Afficher le bouton si des images supplémentaires sont disponibles
        } else {
            loadMoreBtn.style.display = "none"; // Masquer le bouton si toutes les images sont affichées
        }

        if (noImagesMessage) {
            noImagesMessage.style.display = filteredImages.length === 0 ? "block" : "none";
        }
    }

    // Fonction pour afficher une image dans la lightbox
    function openLightbox(imageId) {
        const image = filteredImages.find((img) => img.getAttribute("data-id") === imageId);
        if (!image) return;

        const imageUrl = image.querySelector("img").src;
        const imageRef = image.querySelector(".reference-photo").textContent;
        const imageCategorie = image.querySelector(".categorie-photo").textContent;

        if (lightbox) lightbox.style.display = "flex";
        if (lightboxImg) lightboxImg.src = imageUrl;
        if (lightboxRef) lightboxRef.textContent = imageRef;
        if (lightboxCategorie) lightboxCategorie.textContent = imageCategorie;

        currentImageIndex = filteredImages.indexOf(image);
    }

    // Fonction pour naviguer à l'image suivante dans les images filtrées
    function nextImage() {
        if (filteredImages.length === 0) return;
        currentImageIndex = (currentImageIndex + 1) % filteredImages.length;
        openLightbox(filteredImages[currentImageIndex].getAttribute("data-id"));
    }

    // Fonction pour naviguer à l'image précédente dans les images filtrées
    function prevImage() {
        if (filteredImages.length === 0) return;
        currentImageIndex = (currentImageIndex - 1 + filteredImages.length) % filteredImages.length;
        openLightbox(filteredImages[currentImageIndex].getAttribute("data-id"));
    }

    // Fermer la lightbox
    const lightboxClose = document.querySelector(".lightbox-close");
    if (lightboxClose) {
        lightboxClose.addEventListener("click", () => {
            if (lightbox) lightbox.style.display = "none";
        });
    }

    // Ajouter les événements de navigation dans la lightbox
    const lightboxArrowRight = document.querySelector(".lightbox-arrow.right");
    const lightboxArrowLeft = document.querySelector(".lightbox-arrow.left");
    if (lightboxArrowRight) {
        lightboxArrowRight.addEventListener("click", nextImage);
    }
    if (lightboxArrowLeft) {
        lightboxArrowLeft.addEventListener("click", prevImage);
    }

    // Ajouter un événement sur chaque bouton "fullscreen-btn"
    document.querySelectorAll(".fullscreen-btn").forEach((button) => {
        button.addEventListener("click", () => {
            const imageId = button.getAttribute("data-id");
            openLightbox(imageId);
        });
    });

    // Event listeners pour les filtres
    if (formatSelect) {
        formatSelect.addEventListener("change", filterImages);
    }
    if (categorieSelect) {
        categorieSelect.addEventListener("change", filterImages);
    }
    if (dateSelect) {
        dateSelect.addEventListener("change", filterImages);
    }

    // Fonction pour afficher plus d'images
    function loadMoreImages() {
        if (visibleCount < filteredImages.length) {
            visibleCount += 8; // Augmente le nombre d'images visibles
            filterImages(); // Re-applique le filtre avec les nouvelles images visibles
        }
    }

    // Event listener pour le bouton "Charger plus"
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", loadMoreImages);
    }

    // Afficher les images filtrées au chargement de la page
    filterImages();
});
