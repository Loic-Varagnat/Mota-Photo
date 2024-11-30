<?php
// single-photo.php - Affiche le contenu spécifique pour chaque photo

get_header(); // Inclure l'en-tête du site

if (have_posts()) :
    while (have_posts()) : the_post(); ?>
        <section class="photo-single-section">
            <div class="photo-single">
                <!-- Image mise en avant -->
                <div class="photo-image">
                    <?php 
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('large', ['alt' => get_the_title()]); // Image avec attribut alt
                    } else {
                        echo '<p>Aucune image mise en avant.</p>';
                    }
                    ?>
                </div>
                
                <!-- Informations sur la photo -->
                <div class="photo-info">
                    <h2><?php the_title(); ?></h2> <!-- Titre de la publication -->
                    
                    <?php
                    // Récupérer les métadonnées avec vérification
                    $reference_photo = get_post_meta(get_the_ID(), 'reference_photo', true) ?: 'Non spécifiée';
                    $annee_photo = get_post_meta(get_the_ID(), 'annee_photo', true) ?: 'Inconnue';
                    $type_photo = get_post_meta(get_the_ID(), 'type_photo', true) ?: 'Non défini';
                    ?>
                    
                    <p>Référence photo : <?php echo esc_html($reference_photo); ?></p>
                    
                    <!-- Affichage des catégories -->
                    <p>Catégorie : 
                        <?php 
                        $categories = get_the_terms(get_the_ID(), 'categories');
                        if ($categories && !is_wp_error($categories)) {
                            echo esc_html(implode(', ', wp_list_pluck($categories, 'name'))); 
                        } else {
                            echo 'Non catégorisé';
                        }
                        ?>
                    </p>
                    
                    <!-- Affichage des formats -->
                    <p>Format : 
                        <?php 
                        $formats = get_the_terms(get_the_ID(), 'formats');
                        if ($formats && !is_wp_error($formats)) {
                            echo esc_html(implode(', ', wp_list_pluck($formats, 'name')));
                        } else {
                            echo 'Non spécifié';
                        }
                        ?>
                    </p>
                    
                    <p>Type : <?php echo esc_html($type_photo); ?></p>
                    <p>Année : <?php echo esc_html($annee_photo); ?></p>

  
                </div>
            </div>
        </section>

        <!-- Section combinée pour contact et navigation -->
        <section class="photo-contact-navigation-section">
            <div class="photo-contact">
                <p>Cette photo vous intéresse ?</p>
                <a href="#" class="photo-contact-btn" data-ref="<?php echo esc_attr($reference_photo); ?>">Contact</a>
            </div>

            <!-- Liens de navigation -->
            <div class="photo-navigation" style="position: relative; display: flex; align-items: center;">
                <?php 
                // Récupérer les publications précédente et suivante
                $prev_post = get_previous_post();
                $next_post = get_next_post();

                // Si aucune photo précédente, on va chercher la dernière photo
                if (!$prev_post) {
                    $prev_post = get_posts([ 
                        'numberposts' => 1,
                        'order' => 'DESC',
                        'post_type' => 'photo',
                    ])[0];
                }

                // Si aucune photo suivante, on va chercher la première photo
                if (!$next_post) {
                    $next_post = get_posts([ 
                        'numberposts' => 1,
                        'order' => 'ASC',
                        'post_type' => 'photo',
                    ])[0];
                }

                // Affichage du bouton pour la photo suivante (flèche droite)
                if ($next_post) :
                    $next_thumb = get_the_post_thumbnail_url($next_post->ID, 'thumbnail');
                ?>
                    <div class="arrow-container arrow-right-container" style="display: inline-block; position: relative;">
                        <a href="<?php echo get_permalink($next_post->ID); ?>" 
                           class="photo-nav-link" 
                           data-thumb="<?php echo esc_url($next_thumb); ?>" title="Photo suivante">
                           <span class="arrow-right">&#8592;</span> <!-- Flèche droite pour photo suivante -->
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Conteneur pour afficher la miniature centré -->
                <div class="photo-preview" style="display: none; position: absolute;">
                    <img src="" alt="Miniature" id="photo-preview-img">
                </div>

                <!-- Affichage du bouton pour la photo précédente (flèche gauche) -->
                <?php 
                if ($prev_post) :
                    $prev_thumb = get_the_post_thumbnail_url($prev_post->ID, 'thumbnail');
                ?>
                    <div class="arrow-container arrow-left-container" style="display: inline-block; position: relative;">
                        <a href="<?php echo get_permalink($prev_post->ID); ?>" 
                           class="photo-nav-link" 
                           data-thumb="<?php echo esc_url($prev_thumb); ?>" title="Photo précédente">
                           <span class="arrow-left">&#8594;</span> <!-- Flèche gauche pour photo précédente -->
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section "Vous aimerez aussi" -->
        <?php
        $categories = get_the_terms(get_the_ID(), 'categories');

        if ($categories && !is_wp_error($categories)) {
            $category_ids = wp_list_pluck($categories, 'term_id'); // Récupérer les IDs des catégories

            // Récupérer deux photos de la même catégorie
            $related_photos = new WP_Query([
                'post_type' => 'photo',
                'posts_per_page' => 2,
                'post__not_in' => [get_the_ID()], // Exclure la photo actuelle
                'tax_query' => [
                    [
                        'taxonomy' => 'categories',
                        'field' => 'term_id',
                        'terms' => $category_ids, // Filtrer par catégories
                    ],
                ],
            ]);

            if ($related_photos->have_posts()) : ?>
                <section class="related-photos">
                    <h3>VOUS AIMEREZ AUSSI</h3>
                    <div class="related-photos-container">
                        <?php 
                        $photo_index = 1; // Initialiser un compteur pour indexer les photos
                        while ($related_photos->have_posts()) : $related_photos->the_post(); ?>
                            <div class="related-photo image-item related-photo-<?php echo $photo_index ; ?> " data-id="<?php the_ID(); ?>" > <!-- Classe avec index -->

                            <?php 
                if (has_post_thumbnail()) {
                    // Ajouter le data-id sur l'image
                    the_post_thumbnail('large', ['alt' => get_the_title(), ]);
                } else {
                    echo '<p>Aucune image.</p>';
                }
                ?>
                               

                                      <!-- Bouton en haut à droite pour la lightbox -->
                                      <button class="fullscreen-btn-related fullscreen-btn" data-id="<?php the_ID(); ?>">
                    <i class="fas fa-expand"></i> <!-- Icône plein écran -->
                </button>

                                <!-- Bouton en haut à droite pour afficher les infos -->
                                <button class="photo-info-btn">
                                    <a href="<?php the_permalink(); ?>">
                                        <i class="fas fa-eye"></i> <!-- Icône œil -->
                                    </a>
                                </button>

                                <!-- Référence photo -->
                                <div class="reference-photo">
                                    <?php 
                                    $reference_photo = get_post_meta(get_the_ID(), 'reference_photo', true);
                                    echo esc_html($reference_photo ? $reference_photo : 'Aucune référence');
                                    ?>
                                </div>

                                <!-- Catégorie de la photo -->
                                <div class="categorie-photo">
                                    <?php
                                    $categories_terms = get_the_terms(get_the_ID(), 'categories');
                                    if ($categories_terms && !is_wp_error($categories_terms)) {
                                        $category_name = $categories_terms[0]->name;
                                        echo esc_html($category_name);
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php 
                        $photo_index++; // Incrémenter l'index
                        endwhile; ?>
                    </div>
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
                </section>
            <?php endif;

            wp_reset_postdata(); // Réinitialiser la requête
        }
        ?>


    <?php endwhile;
endif;

get_footer(); // Inclure le pied de page du site
