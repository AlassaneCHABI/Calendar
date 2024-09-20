<?php

class EventController {

    private $event_model;

    public function __construct() {
        $this->event_model = new EventModel();
    }

    // Action pour ajouter un événement via Ajax
    public function add_event() {
        check_ajax_referer('ccp_nonce', 'nonce');

        $data = array(
            'link_post' => $_POST['link_post'],
            'title' => $_POST['title'],
            'date_time' => $_POST['date_time'],  // Tableau de dates
            'contact' => $_POST['contact'],      // Tableau de contacts
            'location' => $_POST['location'],
            'description' => $_POST['description'],
            'remember' => $_POST['remember'],
            'link' => $_POST['link'],
            'file' => $_POST['file'],
            'user_id' => get_current_user_id(),
        );

        $this->event_model->create_event($data);

        wp_send_json_success('Événement ajouté avec succès');
    }

    // Action pour récupérer les événements
    public function get_events() {
        $events = $this->event_model->get_events();
        wp_send_json_success($events);
    }

public function show_event_form() {
    $contact_model = new ContactModel();
    $contacts = $contact_model->get_all_contacts();
    ob_start(); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Style pour retirer la bordure des champs */
        #event_date, #event_time {
            border: none;
            border-bottom: 1px solid #ccc; /* Optionnel, juste une ligne en bas */
            padding: 5px;
            width: 100%;
            background-color: transparent; /* Rendre le fond transparent */
            font-size: 16px;
        }
        
        /* Supprimer les bordures lorsque le champ est sélectionné */
        #event_date:focus, #event_time:focus {
            outline: none;
            border-bottom: 1px solid #007bff; /* Optionnel, changer la couleur de la bordure au focus */
        }
    </style>
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var now = new Date();
            var currentDate = now.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
            var currentTime = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

            // Initialiser le champ de la date
            flatpickr("#event_date", {
                dateFormat: "l d M Y",
                defaultDate: now,
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.value = currentDate;
                }
            });

            // Initialiser le champ de l'heure
            flatpickr("#event_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: now,
                onReady: function(selectedDates, dateStr, instance) {
                    instance.input.value = currentTime;
                }
            });
        });

        let map;

function showMap() {
    // Afficher la carte si elle n'est pas déjà affichée
    if (!map) {
        // Initialiser la carte
        map = L.map('map').setView([48.8566, 2.3522], 13); // Coordonnées de Paris

        // Ajouter une couche OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    // Afficher la carte
    document.getElementById('map').style.display = 'block';
}
    </script>

    <form id="add-event-form">
        <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">
        <input type="text" id="title" name="title" required placeholder="Titre">

        <!-- Deux champs sur une même ligne pour la date et l'heure -->
        <div class="row mb-3">

            <div class="col-md-6">
                
                <input type="date" id="event_date" name="event_date" placeholder="Sélectionnez la date">
            </div>
            <div class="col-md-6">
                
                <input type="time" id="event_time" name="event_time" placeholder="Sélectionnez l'heure">
            </div>
        </div> 

        <div class="row mb-3">

            <div class="col-md-6">
                
                <input type="date" id="event_date" name="event_date" placeholder="Sélectionnez la date">
            </div>
            <div class="col-md-6">
                
                <input type="time" id="event_time" name="event_time" placeholder="Sélectionnez l'heure">
            </div>
        </div>

        <div class="mb-12">
        <div class="input-group">
           
            <input type="text" id="location" name="location" placeholder="Lieu" onclick="showMap()">
            </div>
        </div>

        
        <div id="map" style="height: 300px; display: none;"></div>
        <input type="text" id="contact" name="contact[]" readonly placeholder="Contact">
        <div id="search-container">
            <input type="text" id="contact-search" placeholder="Rechercher un contact...">
            <select id="contact-search-results" size="5">
                <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo esc_attr($contact->id); ?>"><?php echo esc_html($contact->contact_info); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="selected-contacts"></div>

        
        <textarea id="description" name="description" placeholder="Description"></textarea>
        <input type="text" id="remember" name="remember" placeholder="Rappel">
        <input type="text" id="link" name="link" placeholder="Ajouter un lien">
        <input type="file" id="file" name="file" placeholder="Ajouter un Fichier">
        <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
        <input type="submit" value="Valider">
    </form>
    <div id="event-message"></div>
    <?php return ob_get_clean();
}





    
}
