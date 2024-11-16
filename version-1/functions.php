<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ) {
    function chld_thm_cfg_locale_css( $uri ) {
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
}
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'child_theme_configurator_css' ) ) {
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', get_stylesheet_directory_uri() . '/style.css', array(), null );
    }
}
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 20 );

// END ENQUEUE PARENT ACTION
// Fonction AJAX pour filtrer les images
function filter_images_ajax() {
    // Récupérer les filtres sélectionnés
    $format = isset($_GET['format']) ? $_GET['format'] : '';
    $categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';
    $tri_date = isset($_GET['tri_date']) ? $_GET['tri_date'] : '';
    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1; // Page actuelle (pour pagination)

    // Paramètres de la requête
    $args = [
        'post_type' => 'photo',
        'posts_per_page' => 8,  // Limiter à 8 images par page
        'paged' => $paged,  // Page actuelle
        'orderby' => 'date',
        'order' => ($tri_date === 'recent' ? 'DESC' : ($tri_date === 'oldest' ? 'ASC' : 'DESC')),
    ];

    // Ajouter la taxonomie si sélectionnée
    if ($format) {
        $args['tax_query'][] = [
            'taxonomy' => 'formats',
            'field' => 'slug',
            'terms' => $format,
            'operator' => 'IN',
        ];
    }

    if ($categorie) {
        $args['tax_query'][] = [
            'taxonomy' => 'categories',
            'field' => 'slug',
            'terms' => $categorie,
            'operator' => 'IN',
        ];
    }

    // Exécuter la requête
    $query = new WP_Query($args);
    $images = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $alt_text = get_the_title();
            $formats_terms = get_the_terms(get_the_ID(), 'formats');
            $categories_terms = get_the_terms(get_the_ID(), 'categories');
            $format_name = $formats_terms ? $formats_terms[0]->name : '';
            $categorie_name = $categories_terms ? $categories_terms[0]->name : '';

            // Modifier ici pour avoir un lien <a> autour de l'image et un bouton dans un <span>
            $images .= '<div class="image-item" data-format="' . esc_attr($format_name) . '" data-categorie="' . esc_attr($categorie_name) . '">
                            <a href="#" class="image-link">
                                <img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt_text) . '">
                                <span class="button-placeholder">Voir plus</span> <!-- Bouton à ajouter plus tard -->
                            </a>
                        </div>';
        }
        wp_reset_postdata();
    } else {
        $images = '<p>Aucune image trouvée.</p>';
    }

    // Récupérer les formats et catégories à inclure dans les filtres
    $all_formats = get_terms([
        'taxonomy' => 'formats',
        'hide_empty' => false,
    ]);
    $all_categories = get_terms([
        'taxonomy' => 'categories',
        'hide_empty' => false,
    ]);

    // Retourner les images filtrées et les filtres mis à jour
    echo json_encode([
        'images' => $images,
        'filters' => [
            'format' => $format,
            'categorie' => $categorie,
            'tri_date' => $tri_date,
            'all_formats' => $all_formats,
            'all_categories' => $all_categories,
            'paged' => $paged
        ]
    ]);
    die();
}

add_action('wp_ajax_filter_images', 'filter_images_ajax');
add_action('wp_ajax_nopriv_filter_images', 'filter_images_ajax');

?>
