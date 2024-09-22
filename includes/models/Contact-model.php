<?php

class ContactModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . "contacts"; // Nom de la table pour les contacts
    }

    // Fonction pour créer la table contacts
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            nom varchar(255) NOT NULL,
            prenom varchar(255) NOT NULL,
            telephone varchar(20) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Fonction pour créer un contact
    public function create_contact($nom, $prenom, $telephone) {
        global $wpdb;
        return $wpdb->insert(
            $this->table_name,
            array(
                'nom' => sanitize_text_field($nom),
                'prenom' => sanitize_text_field($prenom),
                'telephone' => sanitize_text_field($telephone),
            )
        );
    }

    // Fonction pour récupérer tous les contacts
    public function get_all_contacts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'contacts'; // Assurez-vous que le nom de la table est correct
        $results = $wpdb->get_results("SELECT id, CONCAT(nom, ' ', prenom, ' - ', telephone) AS contact_info FROM $table_name");
        return $results;
    }
}
