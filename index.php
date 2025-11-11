<?php
/**
 * ===================================================================
 * PAGE PRINCIPALE DU COURS MULTI-TENANT (BILINGUE)
 * ===================================================================
 * 
 * Ce script détecte la langue choisie via le paramètre GET 'lang'
 * et inclut les fichiers de configuration et de contenu correspondants.
 * 
 */

// 1. Déterminer la langue (par défaut 'fr')
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'fr';
$suffix = ($lang === 'en') ? '_en' : '';

// 2. Inclure le fichier de configuration de la langue choisie
// require_once 'config_en.php' ou 'config.php'
require_once 'config' . $suffix . '.php';

// 3. Inclure l'en-tête de la page (commun aux deux langues)
require_once './layout/header.php';

// 4. Inclure le contenu de chaque partie dans la langue choisie
// require_once './partials/partie_1_en.php' ou './partials/partie_1.php'
require_once './partials/partie_1' . $suffix . '.php';
require_once './partials/partie_2' . $suffix . '.php';
require_once './partials/partie_3' . $suffix . '.php';
require_once './partials/partie_4' . $suffix . '.php';
require_once './partials/partie_5' . $suffix . '.php';

// Inclure la section de félicitations dans la langue choisie
require_once './partials/felicitations' . $suffix . '.php';

// 5. Inclure le pied de page (qui contiendra les liens de langue)
require_once './layout/footer.php';

?>