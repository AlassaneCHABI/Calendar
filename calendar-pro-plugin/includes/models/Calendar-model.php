<?php

class CalendarModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . "calendar_events";
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            event_title text NOT NULL,
            event_date date NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_event($title, $date) {
        global $wpdb;
        return $wpdb->insert(
            $this->table_name,
            array(
                'event_title' => sanitize_text_field($title),
                'event_date' => sanitize_text_field($date),
            )
        );
    }

    public function get_events() {
        global $wpdb;
        $events = $wpdb->get_results("SELECT * FROM $this->table_name");
        $formatted_events = array();
        foreach ($events as $event) {
            $formatted_events[] = array(
                'title' => $event->event_title,
                'start' => $event->event_date
            );
        }
        return $formatted_events;
    }
}
