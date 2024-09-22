<?php

class InvitationModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . "invitations"; // Nom de la table
    }

    // Créer la table invitations
    public function create_table() {
        global $wpdb;
        $table_name = $this->table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            id_event mediumint(9) NOT NULL,
            id_guest bigint(20) NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Fonction pour créer une invitation
    public function create_invitation($data) {
        global $wpdb;

        // Insérer les données de l'invitation dans la base de données
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'id_event' => $data['id_event'],
                'id_guest' => $data['id_guest'],
                'status' => $data['status'],
            ),
            array(
                '%d',  // id_event
                '%d',  // id_guest
                '%d',  // status (booléen)
            )
        );

        return $wpdb->insert_id;  // Renvoie l'ID de l'invitation insérée
    }

    // Fonction pour récupérer toutes les invitations
    public function get_invitations() {
        global $wpdb;

        // Récupérer toutes les invitations de la base de données
        $results = $wpdb->get_results("SELECT * FROM $this->table_name");

        $formatted_invitations = array();

        // Formater les invitations pour les retourner
        foreach ($results as $invitation) {
            $formatted_invitations[] = array(
                'id' => $invitation->id,
                'id_event' => $invitation->id_event,
                'id_guest' => $invitation->id_guest,
                'status' => $invitation->status,
            );
        }

        return $formatted_invitations;  // Retourne les invitations formatées
    }
}
