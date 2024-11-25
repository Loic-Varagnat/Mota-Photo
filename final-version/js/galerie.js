document.addEventListener("DOMContentLoaded", function () {

    const formatSelect = document.getElementById("format-select");
    const categorieSelect = document.getElementById("categorie-select");
    const dateSelect = document.getElementById("date-select");
    const images = document.querySelectorAll("#galerie .image-item");
    const loadMoreBtn = document.getElementById("load-more");
    const noImagesMessage = document.getElementById("no-images");
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightbox-img");
    const lightboxRef = document.getElementById("lightbox-ref");
    const lightboxCategorie = document.getElementById("lightbox-categorie");

    let visibleCount = 8;
    let currentImageIndex = null;
    let imageArray = [];

    // Ajouter les images au tableau
    images.forEach((image, index) => {
        imageArray.push(image);
    });

    // Fonction pour filtrer les images selon les filtres sélectionnés
    function filterImages() {
        const selectedFormat = formatSelect ? formatSelect.value : '';
        const selectedCategorie = categorieSelect ? categorieSelect.value : '';
        const selectedDate = dateSelect ? dateSelect.value : '';

        let filteredImages = [...images].filter((image) => {
            const imageFormat = image.getAttribute("data-format");
            const imageCategorie = image.getAttribute("data-categorie");

            const matchesFormat = !selectedFormat || imageFormat === selectedFormat;
            const matchesCategorie = !selectedCategorie || imageCategorie === selectedCategorie;

            return matchesFormat && matchesCategorie;
        });

        // Tri par date si sélectionné
        if (selectedDate === "recent") {
            filteredImages = filteredImages.sort((a, b) => {
                const dateA = new Date(a.getAttribute("data-date"));
                const dateB = new Date(b.getAttribute("data-date"));
                return dateB - dateA;
            });
        } else if (selectedDate === "oldest") {
            filteredImages = filteredImages.sort((a, b) => {
                const dateA = new Date(a.getAttribute("data-date"));
                const dateB = new Date(b.getAttribute("data-date"));
                return dateA - dateB;
            });
        }

        // Masquer toutes les images, puis afficher celles filtrées
        images.forEach((image) => image.style && (image.style.display = "none"));
        filteredImages.forEach((image, index) => {
            if (index < visibleCount) {
                image.style && (image.style.display = "block");
            }
        });

        if (loadMoreBtn) {
            loadMoreBtn.style.display = filteredImages.length > visibleCount ? "block" : "none";
        }
        if (noImagesMessage) {
            noImagesMessage.style.display = filteredImages.length === 0 ? "block" : "none";
        }
    }

    // Fonction pour afficher une image dans la lightbox
    function openLightbox(imageId) {
        const image = imageArray.find((img) => img.getAttribute("data-id") === imageId);
        if (!image) return; // Si l'image n'existe pas, on arrête

        const imageUrl = image.querySelector("img").src;
        const imageRef = image.querySelector(".reference-photo").textContent;
        const imageCategorie = image.querySelector(".categorie-photo").textContent;

        if (lightbox) lightbox.style.display = "flex";
        if (lightboxImg) lightboxImg.src = imageUrl;
        if (lightboxRef) lightboxRef.textContent = imageRef;
        if (lightboxCategorie) lightboxCategorie.textContent = imageCategorie;

        currentImageIndex = imageArray.indexOf(image);
    }

    // Fonction pour naviguer à l'image suivante
    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % imageArray.length;
        openLightbox(imageArray[currentImageIndex].getAttribute("data-id"));
    }

    // Fonction pour naviguer à l'image précédente
    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + imageArray.length) % imageArray.length;
        openLightbox(imageArray[currentImageIndex].getAttribute("data-id"));
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
        visibleCount += 8;  // Augmente le nombre d'images visibles
        filterImages();  // Re-applique le filtre avec les nouvelles images visibles
    }

    // Event listener pour le bouton "Charger plus"
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", loadMoreImages);
    }

    // Afficher les images filtrées au chargement de la page
    filterImages();
});
