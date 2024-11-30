<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
if (!function_exists('chld_thm_cfg_locale_css')) {
    function chld_thm_cfg_locale_css($uri) {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css')) {
            $uri = get_template_directory_uri() . '/rtl.css';
        }
        return $uri;
    }
}
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')) {
    function child_theme_configurator_css() {
        wp_enqueue_style('chld_thm_cfg_child', get_stylesheet_directory_uri() . '/style.css', array(), null);
    }
}
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 20);
// END ENQUEUE PARENT ACTION

function filter_images_ajax() {
    $format = isset($_GET['format']) ? sanitize_text_field($_GET['format']) : '';
    $categorie = isset($_GET['categorie']) ? sanitize_text_field($_GET['categorie']) : '';
    $tri_date = isset($_GET['tri_date']) ? sanitize_text_field($_GET['tri_date']) : '';
    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

    // Déterminer l'ordre de tri
    $order = ($tri_date === 'recent') ? 'DESC' : 'ASC';

    $args = [
        'post_type' => 'photo',
        'posts_per_page' => 8,
        'paged' => $paged,
        'orderby' => 'date',
        'order' => $order,
    ];

    // Ajout des taxonomies si nécessaires
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

    // Requête WP_Query
    $query = new WP_Query($args);

    // Construire les résultats
    $images = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $alt_text = get_the_title();

            // Ajouter chaque image à la galerie
            $images .= '<div class="image-item" data-id="' . get_the_ID() . '">
                <a href="#" class="image-link" onclick="openLightbox(' . get_the_ID() . ')">
                    <img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt_text) . '">
                    <span class="button-placeholder">Voir plus</span>
                </a>
            </div>';
        }
        wp_reset_postdata();
    } else {
        $images = '<p>Aucune image trouvée.</p>';
    }

    // Retourner les résultats JSON
    echo json_encode([
        'images' => ($order === 'ASC') ? $images : $images,
        'filters' => [
            'format' => $format,
            'categorie' => $categorie,
            'tri_date' => $tri_date,
            'paged' => $paged,
        ],
    ]);

    die();
}


add_action('wp_ajax_filter_images', 'filter_images_ajax');
add_action('wp_ajax_nopriv_filter_images', 'filter_images_ajax');

// Enregistrer le type de contenu personnalisé 'photo'
function custom_post_type_photos() {
    $args = array(
        'labels' => array(
            'name' => 'Photos',
            'singular_name' => 'Photo',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'photo'),
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
    );
    register_post_type('photo', $args);
}
add_action('init', 'custom_post_type_photos');

// Ajouter le shortcode pour tester les permaliens et ID
function test_retrieve_photo_data() {
    $args = [
        'post_type' => 'photo',
        'posts_per_page' => 10,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<li>';
            echo 'ID: ' . get_the_ID() . '<br>';
            echo 'Titre: ' . get_the_title() . '<br>';
            echo 'Permalien: <a href="' . get_permalink() . '">' . get_permalink() . '</a><br>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo 'Aucune photo trouvée.';
    }
}
add_shortcode('test_photo_data', 'test_retrieve_photo_data');

// Enregistrement et chargement des scripts
function enqueue_galerie_script() {
    wp_enqueue_script(
        'galerie-script',
        get_stylesheet_directory_uri() . '/js/galerie.js',
        array('jquery'),
        null
    );

    wp_localize_script(
        'galerie-script',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gallery_nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'enqueue_galerie_script');

function enqueue_related_photo_script() {
    if (is_singular('photo')) {
        wp_enqueue_script('related-photo-script', get_stylesheet_directory_uri() . '/js/related-photo.js', array('jquery'), null);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_related_photo_script');

function my_theme_enqueue_scripts() {
    wp_enqueue_script(
        'header-animation',
        get_stylesheet_directory_uri() . '/js/header-animation.js',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

// Ajouter les styles et les scripts de Choices.js
function ajouter_choices_js() {
    // Styles
    wp_enqueue_style(
        'choices-css',
        'https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css',
        [],
        '10.0.1'
    );

    // Scripts
    wp_enqueue_script(
        'choices-js',
        'https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js',
        ['jquery'],  // Dépendance à jQuery (si nécessaire)
        '10.0.1',
        
    );
}
add_action('wp_enqueue_scripts', 'ajouter_choices_js');

function enqueue_choices_init_script() {
    // Enqueue Choices.js initialisation script
    wp_enqueue_script(
        'choices-init', // Le nom du script
        get_stylesheet_directory_uri() . '/js/choices-init.js', // Le chemin du fichier JS
        array('choices-js'), // Dépend de choices-js, donc on le charge d'abord
        null, // Pas de version spécifique

    );
}
add_action('wp_enqueue_scripts', 'enqueue_choices_init_script');
