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
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            link_post varchar(255) NOT NULL,
            title varchar(255) NOT NULL,
            date_time text NOT NULL,   /* On utilisera JSON pour stocker le tableau */
            contact text NOT NULL,     /* Stocké sous forme de JSON */
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
        return $wpdb->insert(
            $this->table_name,
            array(
                'link_post' => sanitize_text_field($data['link_post']),
                'title' => sanitize_text_field($data['title']),
                'date_time' => json_encode($data['date_time']), // Encodage en JSON du tableau
                'contact' => json_encode($data['contact']),     // Encodage en JSON du tableau
                'location' => sanitize_text_field($data['location']),
                'description' => sanitize_textarea_field($data['description']),
                'remember' => intval($data['remember']),
                'link' => sanitize_text_field($data['link']),
                'file' => sanitize_text_field($data['file']),
                'user_id' => intval($data['user_id']),
            )
        );
    }

    // Fonction pour récupérer tous les événements
    public function get_events() {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM $this->table_name");
        $formatted_events = array();

        foreach ($results as $event) {
            $formatted_events[] = array(
                'link_post' => $event->link_post,
                'title' => $event->title,
                'date_time' => json_decode($event->date_time, true), // Décodage du JSON
                'contact' => json_decode($event->contact, true),     // Décodage du JSON
                'location' => $event->location,
                'description' => $event->description,
                'remember' => $event->remember,
                'link' => $event->link,
                'file' => $event->file,
                'user_id' => $event->user_id,
            );
        }

        return $formatted_events;
    }
}
