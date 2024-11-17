<?php
// single-photo.php - Affiche le contenu spécifique pour chaque photo

get_header(); // Inclure l'en-tête du site

if (have_posts()) :
    while (have_posts()) : the_post(); ?>
        <div class="photo-single">
            <h1><?php the_title(); ?></h1>
            
            <div class="photo-image">
                <?php 
                if (has_post_thumbnail()) {
                    the_post_thumbnail('large'); // Afficher l'image mise en avant
                } else {
                    echo '<p>Aucune image mise en avant.</p>';
                }
                ?>
            </div>
            
            <div class="photo-description">
                <?php the_content(); ?> <!-- Description ou contenu -->
            </div>
            
            <p><strong>Référence photo :</strong> <?php the_title(); ?></p>
            <p><strong>Catégorie :</strong> <?php echo get_the_term_list(get_the_ID(), 'categories', '', ', ', ''); ?></p>
            <p><strong>Format :</strong> <?php echo get_the_term_list(get_the_ID(), 'formats', '', ', ', ''); ?></p>
            <p><strong>Date de publication :</strong> <?php echo get_the_date(); ?></p>
        </div>
    <?php endwhile;
else :
    echo '<p>Aucune photo trouvée.</p>';
endif;

get_footer(); // Inclure le pied de page du site
