<?php

class ContactController {

    private $contact_model;

    public function __construct() {
        $this->contact_model = new ContactModel();
    }

    // Action pour ajouter un contact via une requête Ajax
    public function add_contact() {
        check_ajax_referer('ccp_nonce', 'nonce');

        $nom = sanitize_text_field($_POST['nom']);
        $prenom = sanitize_text_field($_POST['prenom']);
        $telephone = sanitize_text_field($_POST['telephone']);

        $this->contact_model->create_contact($nom, $prenom, $telephone);

        wp_send_json_success('Contact ajouté avec succès');
    }

    // Action pour récupérer tous les contacts
    public function get_contacts() {
        $contacts = $this->contact_model->get_contacts();
        wp_send_json_success($contacts);
    }
}

// Enregistrement des actions Ajax
add_action('wp_ajax_add_contact', [new ContactController(), 'add_contact']);
add_action('wp_ajax_nopriv_add_contact', [new ContactController(), 'add_contact']);
add_action('wp_ajax_get_contacts', [new ContactController(), 'get_contacts']);
