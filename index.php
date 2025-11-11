<?php
/**
 * ===================================================================
 * PAGE PRINCIPALE DU COURS MULTI-TENANT (BILINGUE)
 * ===================================================================
 */

// 1. Déterminer la langue (par défaut 'fr')
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'fr';
$suffix = ($lang === 'en') ? '_en' : '';

// 2. Inclure le fichier de traduction correspondant
// Cela rendra la variable $strings disponible pour les autres fichiers
require_once './translations/' . $lang . '.php';

// 3. Inclure le fichier de configuration de la langue choisie
require_once 'config' . $suffix . '.php';

// 4. Inclure l'en-tête de la page (qui utilisera $strings)
require_once './layout/header.php';

// 5. Inclure le contenu de chaque partie dans la langue choisie
require_once './partials/partie_1' . $suffix . '.php';
require_once './partials/partie_2' . $suffix . '.php';
require_once './partials/partie_3' . $suffix . '.php';
require_once './partials/partie_4' . $suffix . '.php';
require_once './partials/partie_5' . $suffix . '.php';
require_once './partials/felicitations' . $suffix . '.php';

// 6. Inclure le pied de page (qui utilisera aussi $strings)
require_once './layout/footer.php';

?>