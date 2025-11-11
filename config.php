<?php
// Configuration générale du cours
define('COURSE_TITLE', 'Création d\'une Application Laravel 12 Multi-Tenant, dynamique thèmes et développement modulaire  de A à Z');
define('COURSE_AUTHOR', 'F. Rahmouni Oussama');
define('COURSE_LAST_UPDATE', 'Novembre 2025');

// Structure du cours pour générer le sommaire dynamiquement
$course_parts = [
    "Partie 1 : Initialisation et Prérequis" => [
        ['id' => 'install-laravel', 'title' => "Chapitre 1 : Installation de Laravel 12 et du Layout"],
        ['id' => 'config-redis', 'title' => "Chapitre 2 : Intégration de Redis pour le Cache & Sessions"]
    ],
    "Partie 2 : Architecture Multi-Tenant" => [
        ['id' => 'install-tenancy', 'title' => "Chapitre 3 : Mise en place de Stancl/Tenancy"],
        ['id' => 'tenancy-migrations-routes', 'title' => "Chapitre 4 : Migrations, Seeders et Routage Tenant"]
    ],
    "Partie 3 : Gestion des Tenants" => [
        ['id' => 'crud-tenants', 'title' => "Chapitre 5 : Création du CRUD pour les Tenants"],
        ['id' => 'auto-db-creation', 'title' => "Chapitre 6 : Automatisation de la création des BDD"]
    ],
    "Partie 4 : Modularisation de l'Application" => [
        ['id' => 'install-modules', 'title' => "Chapitre 7 : Intégration de Laravel-Modules (Nwidart)"],
        ['id' => 'modules-migrations-routes', 'title' => "Chapitre 8 : Migrations et Routes dans les Modules"]
    ],
    "Partie 5 : Thèmes Dynamiques par Tenant" => [
        ['id' => 'themes-vite', 'title' => "Chapitre 9 : Configurer Vite.js et les Helpers de Thème"],
        ['id' => 'themes-service-provider', 'title' => "Chapitre 10 : Provider et Composants de Layouts dynamiques"]
    ]
];
?>