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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#date_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                mode: "multiple" // Permet de sélectionner plusieurs dates et heures
            });
        });

         // Afficher le champ de recherche au clic
            document.getElementById('contact').addEventListener('click', function() {
                document.getElementById('search-container').style.display = 'block';
            });

            // Filtrer les contacts lors de la saisie dans le champ de recherche
            document.getElementById('contact-search').addEventListener('input', function() {
                let filter = this.value.toLowerCase();
                let options = document.querySelectorAll('#contact-search-results option');
                options.forEach(function(option) {
                    if (option.textContent.toLowerCase().includes(filter)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });

             // Ajouter le contact sélectionné
            document.getElementById('contact-search-results').addEventListener('change', function() {
                let selectedContact = this.options[this.selectedIndex];
                if (selectedContact) {
                    let contactList = document.getElementById('selected-contacts');
                    let contact = document.createElement('div');
                    contact.className = 'selected-contact';
                    contact.textContent = selectedContact.textContent;
                    contact.dataset.id = selectedContact.value;
                    contactList.appendChild(contact);
                }
                document.getElementById('contact-search').value = '';
                document.getElementById('search-container').style.display = 'none';
            });
    
    </script>
     
    <form id="add-event-form" >
        <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">

        <input type="text" id="title" name="title" required placeholder="Titre">
        
        <input type="date" id="date_time" name="date_time[]" required placeholder="Date et heure"> <!-- Tableau -->

        <input type="text" id="contact" name="contact[]" readonly placeholder="Contact">
         <div id="search-container">
                <input type="text" id="contact-search" placeholder="Rechercher un contact...">
                <select id="contact-search-results" size="5">
                    <?php
                    $contact_model = new ContactModel();
                    $contacts = $contact_model->get_all_contacts();
                    foreach ($contacts as $contact): ?>
                        <option value="<?php echo esc_attr($contact->id); ?>"><?php echo esc_html($contact->contact_info); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="selected-contacts"></div> <!-- Affiche les contacts sélectionnés -->


        <input type="text" id="location" name="location" placeholder="Lieu">

        <textarea id="description" name="description" placeholder="Description"></textarea>

        <input type="text" id="remember" name="remember" placeholder="Rappel">

        <input type="text" id="link" name="link" placeholder="Ajouter un lien">

        <input type="file" id="file" name="file" placeholder="Ajouter un Fichier">

        <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">

        <input type="submit" value="Valider">
    </form>
    <div id="event-message"></div> <!-- Message de confirmation -->
    <?php return ob_get_clean();
}


    
}
