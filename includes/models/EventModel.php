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
    $table_name = $wpdb->prefix . 'events';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        link_post varchar(255) NOT NULL,
        title varchar(255) NOT NULL,
        start_date date NOT NULL,
        start_time time NOT NULL,
        end_date date NOT NULL,
        end_time time NOT NULL,
        location varchar(255) NOT NULL,
        description text,
        remember varchar(255), 
        link varchar(255),
        link_share varchar(255),
        color varchar(255),
        file_url varchar(255),
        created_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


}
