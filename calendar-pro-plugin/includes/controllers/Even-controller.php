<?php

class EventController {

    private $event_model;

    public function __construct() {
        $this->event_model = new EventModel();
        add_action('wp_ajax_add_event', [$this, 'add_event']); // Action AJAX pour ajouter un événement
        add_action('wp_ajax_get_contacts', [$this, 'get_contacts']); // Action AJAX pour récupérer les contacts
    }

    // Action pour ajouter un événement via Ajax
    public function add_event() {
        check_ajax_referer('ccp_nonce', 'nonce');

        // Vérification que tous les champs requis sont présents
        if (empty($_POST['title']) || empty($_POST['link_post']) || empty($_POST['event_date']) || empty($_POST['event_time'])) {
            wp_send_json_error('Les champs obligatoires sont manquants');
            return;
        }

        $date_time = $_POST['event_date'] . ' ' . $_POST['event_time']; // Combine date et heure

        $data = array(
            'link_post' => sanitize_text_field($_POST['link_post']),
            'title' => sanitize_text_field($_POST['title']),
            'date_time' => array($date_time), // Stocke sous forme de tableau
            'contact' => isset($_POST['contact']) ? $_POST['contact'] : [], // Assurez-vous que ce champ est un tableau
            'location' => sanitize_text_field($_POST['location']),
            'description' => sanitize_textarea_field($_POST['description']),
            'remember' => sanitize_text_field($_POST['remember']),
            'link' => esc_url($_POST['link']),
            'file' => sanitize_text_field($_POST['file']),
            'user_id' => get_current_user_id(),
        );

        $insert_id = $this->event_model->create_event($data);

        if ($insert_id) {
            wp_send_json_success('Événement ajouté avec succès');
        } else {
            wp_send_json_error('Erreur lors de l\'ajout de l\'événement');
        }
    }

    // Action pour récupérer les contacts
    public function get_contacts() {
        $contact_model = new ContactModel();
        $contacts = $contact_model->get_all_contacts();
        
        $contact_data = array_map(function($contact) {
            return [
                'id' => $contact->id,
                'contact_info' => $contact->contact_info
            ];
        }, $contacts);

        wp_send_json_success($contact_data); // Envoi des données des contacts sous forme JSON
    }

    // Affichage du formulaire pour ajouter un événement
    public function show_event_form() {
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
            #event_date:focus, #event_time:focus {
                outline: none;
                border-bottom: 1px solid #007bff;
            }
        </style>
        
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const now = new Date();
                flatpickr("#event_date", {
                    dateFormat: "Y-m-d",
                    defaultDate: now,
                });
                flatpickr("#event_time", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultDate: now,
                });

                // Gestion de la soumission du formulaire
                document.getElementById('add-event-form').addEventListener('submit', function(e) {
                    e.preventDefault(); // Empêche la soumission par défaut

                    const formData = new FormData(this);
                    formData.append('nonce', '<?php echo wp_create_nonce('ccp_nonce'); ?>'); // Ajoute le nonce

                    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=add_event', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('event-message').textContent = data.data; // Affiche le message de succès
                            this.reset(); // Réinitialise le formulaire
                        } else {
                            document.getElementById('event-message').textContent = data.data; // Affiche le message d'erreur
                        }
                    })
                    .catch(error => console.error('Erreur lors de l\'ajout de l\'événement:', error));
                });
            });

           let map;
    let marker;

    function showMap() {
        if (!map) {
            map = L.map('map').setView([48.8566, 2.3522], 13); // Coordonnées de Paris

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
        }
        document.getElementById('map').style.display = 'block';
        document.getElementById('address-search').style.display = 'block';
    }

    // Fonction pour rechercher une adresse avec Nominatim
    function searchAddress() {
        const query = document.getElementById('address-search').value;
        if (!query) {
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
            .then(response => response.json())
            .then(data => {
                const resultsContainer = document.getElementById('address-results');
                resultsContainer.innerHTML = ''; // Efface les résultats précédents

                if (data.length > 0) {
                    data.forEach(result => {
                        const resultItem = document.createElement('div');
                        resultItem.innerHTML = `<p style="cursor:pointer;">${result.display_name}</p>`;
                        
                        // Rendre chaque élément cliquable
                        resultItem.addEventListener('click', function() {
                            selectAddress(result.lat, result.lon, result.display_name);
                        });
                        
                        resultsContainer.appendChild(resultItem);
                    });
                } else {
                    resultsContainer.innerHTML = '<p>Aucune adresse trouvée.</p>';
                }
            })
            .catch(error => console.error('Erreur lors de la recherche d\'adresse:', error));
    }

    // Fonction pour sélectionner une adresse et la centrer sur la carte
    function selectAddress(lat, lon, displayName) {
        showMap();

        // Centrer la carte sur les coordonnées sélectionnées
        map.setView([lat, lon], 13);

        // Si un marqueur existe déjà, le supprimer
        if (marker) {
            map.removeLayer(marker);
        }

        // Ajouter un nouveau marqueur à l'emplacement sélectionné
        marker = L.marker([lat, lon]).addTo(map).bindPopup(displayName).openPopup();

        // Mettre à jour le champ de lieu avec l'adresse sélectionnée
        document.getElementById('location').value = displayName;
        document.getElementById('address-results').style.display = 'none';
        document.getElementById('address-search').value = '';
        
    }
        </script>

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        let selectedContacts = [];
        let contactsList = [];

        // Charger les contacts via AJAX
        function loadContacts() {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=get_contacts')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        contactsList = data.data; // Stocke les contacts récupérés
                    }
                })
                .catch(error => console.error('Erreur de chargement des contacts:', error));
        }

        // Fonction pour afficher/masquer le champ de recherche
        window.toggleSearchContainer = function () {
            const searchContainer = document.getElementById('search-container');
            if (searchContainer.style.display === 'none' || searchContainer.style.display === '') {
                searchContainer.style.display = 'block'; // Affiche le champ de recherche
            } else {
                searchContainer.style.display = 'none'; // Masque le champ de recherche
            }
        };

        // Rechercher les contacts lorsque l'utilisateur tape dans le champ de recherche
        document.getElementById('contact-search').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const resultsContainer = document.getElementById('contact-search-results');
            resultsContainer.innerHTML = ''; // Efface les résultats précédents

            // Filtrer les contacts en fonction de la recherche
            contactsList.forEach(contact => {
                if (contact.contact_info.toLowerCase().includes(query)) {
                    const resultItem = document.createElement('div');
                    resultItem.innerHTML = `
                        <span>${contact.contact_info}</span>
                        <button type="button" onclick="addContact('${contact.id}', '${contact.contact_info}')">+</button>
                    `;
                    resultsContainer.appendChild(resultItem);
                }
            });
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

        // Charger les contacts au démarrage
        loadContacts();
    });
</script>


        <form id="add-event-form">
            <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">
            <input type="text" id="title" name="title" required placeholder="Titre">

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="date" id="event_date" name="event_date" required>
                </div>
                <div class="col-md-4">
                    <input type="time" id="event_time" name="event_time" required>
                </div>
            </div>

             <div class="mb-12">
                <div class="input-group">
                    <input type="text" id="location" name="location" placeholder="Lieu" onclick="showMap()" readonly>
                </div>
            </div>
             
          <div class="mb-12">
                    <div class="input-group">
                        <input type="text" id="address-search" name="address" placeholder="Rechercher une adresse" oninput="searchAddress()" style="display: none;" >
                    </div>
                    <div id="address-results" ></div>
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
            <textarea id="description" name="description" placeholder="Ajouter une de"></textarea>
            <input type="text" id="remember" name="remember" placeholder="Alerte">
            <input type="text" id="link" name="link" placeholder="Ajouter un lien">
            <input type="file" id="file" name="file" placeholder="Ajouter un fichier">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="submit" value="Valider">
        </form>

        <div id="event-message"></div>

        <?php return ob_get_clean();
    }
}
