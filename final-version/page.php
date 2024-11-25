<?php
get_header();  // Charge l'en-tête du site (header.php)
?>

<div id="content" class="page-content">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();  // Charge la page actuelle
            ?>
            <div class="page-content-body">
                <?php the_content(); ?>  <!-- Affiche uniquement le contenu de la page -->
            </div>
        <?php endwhile;
    else :
        echo '<p>Aucun contenu trouvé.</p>';
    endif;
    ?>
</div>

<?php
get_footer();  // Charge le pied de page (footer.php)
?>