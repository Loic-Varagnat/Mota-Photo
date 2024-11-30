// Initialisation de Choices.js pour les éléments select sans recherche
document.addEventListener("DOMContentLoaded", function () {
    // Initialiser Choices.js sur les éléments select avec les IDs correspondants
    var formatSelect = document.getElementById("format-select");
    var categorieSelect = document.getElementById("categorie-select");
    var dateSelect = document.getElementById("date-select");

    if (formatSelect) {
        new Choices(formatSelect, {
            searchEnabled: false,  // Désactive la fonction de recherche
            placeholder: true,
            itemSelectText: ""
        });
    }

    if (categorieSelect) {
        new Choices(categorieSelect, {
            searchEnabled: false,  // Désactive la fonction de recherche
            placeholder: true,
            itemSelectText: ""
        });
    }

    if (dateSelect) {
        new Choices(dateSelect, {
            searchEnabled: false,  // Désactive la fonction de recherche
            placeholder: true,
            itemSelectText: ""
        });
    }
});
