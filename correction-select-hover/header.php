<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    
</head>
<body <?php body_class(); ?>>
    <header>
        <section class="header_container">
            <div class="logo_header">
            <img
                    src="http://motaphoto.com/wp-content/themes/MotaPhoto/images/Logo-header.png"
                    alt="Logo-header"
                />
            </div>
            <div class="menu-container">
            <button class="hamburger-menu" aria-label="Ouvrir le menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
                <ul class="header_btn">
                  <li class="header_btn_accueil"><a href="<?php echo home_url(); ?>">ACCUEIL</a></li>
                  <li class="header_btn_a_propos"><a href="<?php echo site_url('/a-propos'); ?>">À PROPOS</a></li>
                  <li class="header_btn_contact"><a href="#" id="openPopup">CONTACT</a></li>

                </ul>
            </div>
        </section>
        <section>
<!-- Popup structure -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-salon">
        <div class="popup-header">
            <!-- Contenu de l'en-tête de la popup (ex : titre, close button, etc.) -->
        </div>
        <h2 class="popup-titre">CONTACTCONTACTCONTACT<br>CONTACTCONTACTCONTACT</h2>
        
        <!-- Formulaire avec l'ID qui le permet de s'afficher avec le shortcode de WordPress -->
        <div id="form-container">
            <?php echo FrmFormsController::get_form_shortcode( array( 'id' => 3 ) ); ?>
        </div>

    </div>
</div>

        </section>
    </header>
