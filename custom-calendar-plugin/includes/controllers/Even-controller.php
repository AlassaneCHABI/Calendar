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

    // Affichage du formulaire pour ajouter un événement
    public function show_event_form() {
        $contact_model = new ContactModel();
        $contacts = $contact_model->get_all_contacts();
        ob_start(); ?>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            /* Style pour retirer la bordure des champs */
            #event_date, #event_time {
                border: none;
                border-bottom: 1px solid #ccc;
                padding: 5px;
                width: 100%;
                background-color: transparent;
                font-size: 16px;
            }
            
            /* Supprimer les bordures lorsque le champ est sélectionné */
            #event_date:focus, #event_time:focus {
                outline: none;
                border-bottom: 1px solid #007bff;
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
                if (!map) {
                    map = L.map('map').setView([48.8566, 2.3522], 13); // Coordonnées de Paris

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);
                }

                document.getElementById('map').style.display = 'block';
            }

            let selectedContacts = [];

            // Fonction pour afficher/masquer le champ de recherche
            function toggleSearchContainer() {
                const searchContainer = document.getElementById('search-container');
                if (searchContainer.style.display === 'none' || searchContainer.style.display === '') {
                    searchContainer.style.display = 'block'; // Affiche le champ de recherche
                } else {
                    searchContainer.style.display = 'none'; // Masque le champ de recherche
                }
            }

        </script>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                let selectedContacts = [];

                // Rechercher les contacts lorsque l'utilisateur tape dans le champ de recherche
                document.getElementById('contact-search').addEventListener('input', function () {
                    const query = this.value.toLowerCase();
                    const resultsContainer = document.getElementById('contact-search-results');
                    resultsContainer.innerHTML = ''; // Efface les résultats précédents

                    <?php foreach ($contacts as $contact): ?>
                    const contactInfo = '<?php echo esc_js($contact->contact_info); ?>'.toLowerCase();
                    const contactId = '<?php echo esc_js($contact->id); ?>';

                    if (contactInfo.includes(query)) {
                        const resultItem = document.createElement('div');
                        resultItem.innerHTML = `
                            <span>${contactInfo}</span>
                            <button type="button" onclick="addContact('${contactId}', '${contactInfo}')">+</button>
                        `;
                        resultsContainer.appendChild(resultItem);
                    }
                    <?php endforeach; ?>
                });

                // Ajouter le contact sélectionné à la liste des contacts
                window.addContact = function(contactId, contactInfo) {
                    if (!selectedContacts.includes(contactId)) {
                        selectedContacts.push(contactId);
                        const selectedContactsContainer = document.getElementById('selected-contacts');
                        const contactItem = document.createElement('div');
                        contactItem.setAttribute('data-contact-id', contactId);
                        contactItem.innerHTML = `${contactInfo} <button type="button" onclick="removeContact('${contactId}')">x</button>`;
                        selectedContactsContainer.appendChild(contactItem);
                    }
                };

                // Supprimer un contact sélectionné
                window.removeContact = function(contactId) {
                    selectedContacts = selectedContacts.filter(id => id !== contactId);
                    const contactItem = document.querySelector(`[data-contact-id="${contactId}"]`);
                    if (contactItem) {
                        contactItem.remove();
                    }
                };
            
        </script>

        <form id="add-event-form">
            <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">
            <input type="text" id="title" name="title" required placeholder="Titre">

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="date" id="event_date" name="event_date" placeholder="Sélectionnez la date">
                </div>
                <div class="col-md-4">
                    <input type="time" id="event_time" name="event_time" placeholder="Sélectionnez l'heure">
                </div>
            </div>

            <div class="mb-12">
                <div class="input-group">
                    <input type="text" id="location" name="location" placeholder="Lieu" onclick="showMap()">
                </div>
            </div>

            <div id="map" style="height: 300px; display: none;"></div>

            <div id="contact-container">
                <input type="text" id="contact" name="contact[]" readonly placeholder="Contact" onclick="toggleSearchContainer()">
                <div id="search-container" style="display: none;">
                    <input type="text" id="contact-search" placeholder="Rechercher un contact...">
                    <div id="contact-search-results"></div>
                </div>
            </div>

            <div id="selected-contacts"></div>

            <textarea id="description" name="description" placeholder="Description"></textarea>
            <input type="text" id="remember" name="remember" placeholder="Rappel">
            <input type="text" id="link" name="link" placeholder="Ajouter un lien">
            <input type="file" id="file" name="file" placeholder="Ajouter un fichier">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="submit" value="Valider">
        </form>

        <div id="event-message"></div>
        <?php return ob_get_clean();
    }
}
