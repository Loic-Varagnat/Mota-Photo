<?php get_header(); ?>
<main>
    <section>
        <div class="hero-container">
            <img class="hero-img"
                 src="http://motaphoto.com/wp-content/themes/MotaPhoto/images/nathalie-11.jpeg"
                 alt="">
            <h1 class="hero-titre">PHOTOGRAPHE EVENT</h1>
        </div>
    </section>

    <?php
    // Requête pour récupérer les images du type de publication 'photo'
    $args = [
        'post_type' => 'photo',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ];

    $query = new WP_Query($args);

    // Récupère tous les termes de la taxonomie 'formats' et 'categories'
    $all_formats = get_terms([
        'taxonomy' => 'formats',
        'hide_empty' => false,
    ]);
    $all_categories = get_terms([
        'taxonomy' => 'categories',
        'hide_empty' => false,
    ]);

    $images = [];
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $formats_terms = get_the_terms(get_the_ID(), 'formats');
            $categories_terms = get_the_terms(get_the_ID(), 'categories');

            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $alt_text = get_the_title();
            $post_date = get_the_date('Y-m-d H:i:s'); // Ajout de l'heure de publication

            // Récupère la référence photo depuis SCF
            $reference_photo = get_post_meta(get_the_ID(), 'reference_photo', true);

            $format_name = $formats_terms ? $formats_terms[0]->slug : '';
            $categorie_name = $categories_terms ? $categories_terms[0]->slug : '';

            if ($image_url) {
                $images[] = [
                    'url' => $image_url,
                    'alt' => $alt_text,
                    'reference' => $reference_photo,  // Référence photo
                    'format' => $format_name,
                    'categorie' => $categorie_name,
                    'date' => $post_date,
                ];
            }
        }
        wp_reset_postdata();
    }
    ?>

    <section class="img-container">
        <div class="filters">
            <select id="format-select" name="format">
                <option value="">Formats</option>
                <?php foreach ($all_formats as $format) : ?>
                    <option value="<?php echo esc_attr($format->slug); ?>"><?php echo esc_html($format->name); ?></option>
                <?php endforeach; ?>
            </select>

            <select id="categorie-select" name="categorie">
                <option value="">Catégories</option>
                <?php foreach ($all_categories as $categorie) : ?>
                    <option value="<?php echo esc_attr($categorie->slug); ?>"><?php echo esc_html($categorie->name); ?></option>
                <?php endforeach; ?>
            </select>

            <select id="date-select" name="date">
                <option value="">Trier par</option>
                <option value="recent">Plus récentes</option>
                <option value="oldest">Plus anciennes</option>
            </select>
        </div>

        <div id="galerie">
            <?php 
            $visible_count = 8; 
            if (!empty($images)) : 
                foreach ($images as $index => $image) : 
                    $is_hidden = $index >= $visible_count ? 'hidden' : ''; 
            ?>
<div class="image-item <?php echo $is_hidden; ?>" 
     data-format="<?php echo esc_attr($image['format']); ?>" 
     data-categorie="<?php echo esc_attr($image['categorie']); ?>" 
     data-date="<?php echo esc_attr($image['date']); ?>" 
     data-id="<?php echo get_the_ID(); ?>"> <!-- ID de l'image -->
    <!-- Image -->
    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">

    <!-- Bouton en haut à droite pour le fullscreen -->
    <button class="fullscreen-btn" data-id="<?php echo get_the_ID(); ?>"> <!-- Ajout de data-id -->
        <i class="fas fa-expand"></i> <!-- Icône du plein écran -->
    </button>

    <!-- Bouton en haut à droite pour le photo-info-btn -->
    <button class="photo-info-btn">
        <a href="<?php echo get_permalink(get_the_ID()); ?>">
            <i class="fas fa-eye"></i> <!-- Icône œil -->
        </a>
    </button>

    <!-- Affichage de la référence photo -->
    <div class="reference-photo">
        <?php echo esc_html($image['reference']); ?>
    </div>

    <!-- Affichage de la catégorie -->
    <div class="categorie-photo">
        <?php echo esc_html($image['categorie']); ?>
    </div>
</div>

            <?php endforeach; ?>
            <?php else : ?>
                <p id="no-images" style="display: none;">Aucune image trouvée.</p>
            <?php endif; ?>
        </div>
        <button id="load-more" style="display: <?php echo count($images) > $visible_count ? 'block' : 'none'; ?>;">Charger plus</button>
    </section>

    <script>
   document.addEventListener("DOMContentLoaded", function () {

const formatSelect = document.getElementById("format-select");
const categorieSelect = document.getElementById("categorie-select");
const dateSelect = document.getElementById("date-select");
const images = document.querySelectorAll("#galerie .image-item");
const loadMoreBtn = document.getElementById("load-more");
const noImagesMessage = document.getElementById("no-images");

let visibleCount = 8;

function filterImages() {
    const selectedFormat = formatSelect.value;
    const selectedCategorie = categorieSelect.value;
    const selectedDate = dateSelect.value;

    let filteredImages = [...images].filter((image) => {
        const imageFormat = image.getAttribute("data-format");
        const imageCategorie = image.getAttribute("data-categorie");

        const matchesFormat = !selectedFormat || imageFormat === selectedFormat;
        const matchesCategorie = !selectedCategorie || imageCategorie === selectedCategorie;

        return matchesFormat && matchesCategorie;
    });

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

    images.forEach((image) => image.style.display = "none");
    filteredImages.forEach((image, index) => {
        if (index < visibleCount) {
            image.style.display = "block";
        }
    });

    loadMoreBtn.style.display = filteredImages.length > visibleCount ? "block" : "none";
    noImagesMessage.style.display = filteredImages.length === 0 ? "block" : "none";
}

function loadMoreImages() {
    visibleCount += 8;
    filterImages();
}

// Fonction pour ouvrir la lightbox
function openLightbox(imageId) {
    const lightbox = document.createElement('div');
    lightbox.classList.add('lightbox');
    document.body.appendChild(lightbox);

    // Récupérer les informations de l'image
    fetch('<?php echo esc_url(home_url('/wp-json/wp/v2/photo/')); ?>' + imageId)
        .then(response => response.json())
        .then(data => {
            const imageUrl = data.featured_media_url;
            const imageTitle = data.title.rendered;
            const imageCategorie = data.categories[0].name;

            // Créer l'image et les informations
            const image = document.createElement('img');
            image.src = imageUrl;
            image.alt = imageTitle;

            const info = document.createElement('div');
            info.classList.add('lightbox-info');
            info.innerHTML = `<div class="photo-ref">${imageTitle}</div><div class="categorie">${imageCategorie}</div>`;

            // Ajouter l'image et les informations à la lightbox
            lightbox.appendChild(image);
            lightbox.appendChild(info);

            // Flèches de navigation
            const leftArrow = document.createElement('div');
            leftArrow.classList.add('lightbox-arrow', 'left');
            leftArrow.innerText = 'Précédente';
            lightbox.appendChild(leftArrow);

            const rightArrow = document.createElement('div');
            rightArrow.classList.add('lightbox-arrow', 'right');
            rightArrow.innerText = 'Suivante';
            lightbox.appendChild(rightArrow);

            // Fermer la lightbox
            const closeButton = document.createElement('div');
            closeButton.classList.add('lightbox-close');
            closeButton.innerText = 'Fermer';
            closeButton.addEventListener('click', () => lightbox.remove());
            lightbox.appendChild(closeButton);

            // Ajouter une navigation entre les images
            leftArrow.addEventListener('click', () => navigateLightbox('prev'));
            rightArrow.addEventListener('click', () => navigateLightbox('next'));
        });
}

function navigateLightbox(direction) {
    const currentImageId = parseInt(lightbox.getAttribute('data-image-id'));
    const nextImageId = direction === 'next' ? currentImageId + 1 : currentImageId - 1;

    if (nextImageId >= 0 && nextImageId < images.length) {
        openLightbox(nextImageId);
    }
}

// Evenement de filtrage des images
formatSelect.addEventListener("change", filterImages);
categorieSelect.addEventListener("change", filterImages);
dateSelect.addEventListener("change", filterImages);
loadMoreBtn.addEventListener("click", loadMoreImages);

// Ouvrir la lightbox au clic sur le bouton fullscreen
document.querySelectorAll('.fullscreen-btn').forEach(button => {
    button.addEventListener('click', function () {
        const imageId = this.getAttribute('data-id'); // Récupérer l'ID spécifique
        openLightbox(imageId);
    });
});

filterImages();  // Appliquer les filtres au chargement initial

});

    </script>
</main>

<?php get_footer(); ?>
