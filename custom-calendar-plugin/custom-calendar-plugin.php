<?php
/**
 * Plugin Name: Custom Calendar Plugin
 * Description: Un plugin de calendrier structuré en MVC.
 * Version: 1.0
 * Author: Alassane
 */

if (!defined('ABSPATH')) {
    exit;
}

// Inclure les modèles nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/models/Calendar-model.php';
require_once plugin_dir_path(__FILE__) . 'includes/models/Contact-model.php';
require_once plugin_dir_path(__FILE__) . 'includes/models/Even-Model.php'; // Ajout du modèle des événements

// Inclure les contrôleurs nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/controllers/Calendar-controller.php';
require_once plugin_dir_path(__FILE__) . 'includes/controllers/Contact-controller.php'; 
require_once plugin_dir_path(__FILE__) . 'includes/controllers/Even-Controller.php'; // Ajout du contrôleur des événements
require_once plugin_dir_path(__FILE__) . 'includes/controllers/Accueil-Controller.php'; // Ajout du contrôleur des événements

// Inclure les vues nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/views/calendar-view.php';

// Initialiser les contrôleurs
$calendar_controller = new CalendarController();
$event_controller = new EventController();
$accueil_controller = new AccueilController();

// Fonction pour créer les tables lors de l'activation du plugin
function ccp_activate_plugin() {
    $calendar_model = new CalendarModel();
    $contact_model = new ContactModel();
    $event_model = new EventModel(); // Modèle des événements

    $calendar_model->create_table();
    $contact_model->create_table();
    $event_model->create_table(); // Créer la table des événements
}
register_activation_hook(__FILE__, 'ccp_activate_plugin');

// Ajouter le shortcode pour afficher le formulaire d'ajout d'événement
add_shortcode('add_event_form', array($event_controller, 'show_event_form'));
// Ajouter le shortcode pour afficher le calendrier
add_shortcode('show_calendar', array($accueil_controller, 'show_calendar'));

function ccp_enqueue_accueil_assets() {
    // Enqueue Google Fonts
    wp_enqueue_style('google-material-font', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', array(), null);

    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', array(), null);

    // Enqueue Bootstrap Icons
    wp_enqueue_style('bootstrap-icons', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css', array(), null);

    // Enqueue Leaflet CSS
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet/dist/leaflet.css', array(), null);

    // Enqueue custom CSS
    wp_enqueue_style('ccp-custom-css', plugins_url('includes/public/assets/css/style.css', __FILE__), array(), null);

    // Enqueue custom script
    wp_enqueue_script('ccp-script', plugins_url('includes/public/assets/js/script.js', __FILE__), array('jquery'), null, true);
    
    // Enqueue Leaflet JS
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet/dist/leaflet.js', array('jquery'), null, true);

    // Enqueue Bootstrap JS (si nécessaire)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    
    // Localiser le script pour ajouter des variables JavaScript
    wp_localize_script('ccp-script', 'ccp_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ccp_nonce' => wp_create_nonce('ccp_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'ccp_enqueue_accueil_assets');

// Enregistrer les actions AJAX pour l'ajout d'événements
add_action('wp_ajax_add_event', array($event_controller, 'add_event'));
add_action('wp_ajax_nopriv_add_event', array($event_controller, 'add_event'));
