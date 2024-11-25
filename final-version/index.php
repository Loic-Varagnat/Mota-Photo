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
        'posts_per_page' => 16,
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
                    'id' => get_the_ID(),
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
                 data-id="<?php echo esc_attr($image['id']); ?>"> <!-- Utilisation correcte de l'ID -->
                <!-- Image -->
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">

                <!-- Bouton en haut à droite pour le fullscreen -->
                <button class="fullscreen-btn" data-id="<?php echo esc_attr($image['id']); ?>"> <!-- Ajout de data-id -->
                    <i class="fas fa-expand"></i> <!-- Icône du plein écran -->
                </button>

                <!-- Bouton en haut à droite pour le photo-info-btn -->
                <button class="photo-info-btn">
                    <a href="<?php echo get_permalink($image['id']); ?>">
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

    <!-- Lightbox (cachée par défaut) -->
    <div id="lightbox" class="lightbox">
        <div class="lightbox-content">
            <div class="lightbox-arrow left">
                &#8592; Précédente
            </div>
            <div class="lightbox-arrow right">
               Suivante &#8594; 
            </div>
            <img id="lightbox-img" src="" alt="">
            <div class="lightbox-info">
                <div class="lightbox-ref" id="lightbox-ref"></div>
                <div class="lightbox-categorie" id="lightbox-categorie"></div>
            </div>
            <button class="lightbox-close">
                <i class="fas fa-times"></i> <!-- Croisillon -->
            </button>
        </div>
    </div>



   
</main>
<?php get_footer(); ?>
