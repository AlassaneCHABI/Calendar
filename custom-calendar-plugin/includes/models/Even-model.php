<?php

class EventModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . "events";
    }

    // Créer la table events
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Requête SQL pour créer la table des événements
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            link_post varchar(255) NOT NULL,
            title varchar(255) NOT NULL,
            date_time text NOT NULL,   /* Stockage JSON du tableau date_time */
            contact text NOT NULL,     /* Stockage JSON du tableau contact */
            location varchar(255),
            description text,
            remember tinyint(1) NOT NULL,
            link varchar(255),
            file varchar(255),
            user_id bigint(20) unsigned NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Fonction pour créer un événement
    public function create_event($data) {
        global $wpdb;

        // Insérer les données de l'événement dans la base de données
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'link_post' => $data['link_post'],
                'title' => $data['title'],
                'date_time' => maybe_serialize($data['date_time']),  // Sérialiser si le champ est un tableau
                'contact' => maybe_serialize($data['contact']),      // Sérialiser également les contacts
                'location' => $data['location'],
                'description' => $data['description'],
                'remember' => $data['remember'],
                'link' => $data['link'],
                'file' => $data['file'],
                'user_id' => $data['user_id'],
            ),
            array(
                '%s',  // link_post
                '%s',  // title
                '%s',  // date_time (sérialisé)
                '%s',  // contact (sérialisé)
                '%s',  // location
                '%s',  // description
                '%d',  // remember (booléen)
                '%s',  // link
                '%s',  // file
                '%d',  // user_id
            )
        );

        return $wpdb->insert_id;  // Renvoie l'ID de l'événement inséré
    }

    // Fonction pour récupérer tous les événements
    public function get_events() {
        global $wpdb;

        // Récupérer tous les événements de la base de données
        $results = $wpdb->get_results("SELECT * FROM $this->table_name");

        $formatted_events = array();

        // Formater les événements pour les retourner
        foreach ($results as $event) {
            $formatted_events[] = array(
                'link_post' => $event->link_post,
                'title' => $event->title,
                'date_time' => json_decode($event->date_time, true), // Décodage du JSON pour le champ date_time
                'contact' => json_decode($event->contact, true),     // Décodage du JSON pour les contacts
                'location' => $event->location,
                'description' => $event->description,
                'remember' => $event->remember,
                'link' => $event->link,
                'file' => $event->file,
                'user_id' => $event->user_id,
            );
        }

        return $formatted_events;  // Retourne les événements formatés
    }
}
