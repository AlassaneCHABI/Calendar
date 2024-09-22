<?php

class CalendarController {

    private $model;

    public function __construct() {
        $this->model = new CalendarModel();
        add_shortcode('custom_calendar', array($this, 'display_calendar'));
        add_shortcode('ccp_event_form', array($this, 'event_form'));
        add_action('wp_ajax_ccp_add_event', array($this, 'add_event'));
        add_action('wp_ajax_nopriv_ccp_add_event', array($this, 'add_event'));
        add_action('wp_ajax_ccp_get_events', array($this, 'get_events'));
        add_action('wp_ajax_nopriv_ccp_get_events', array($this, 'get_events'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function display_calendar() {
        return CalendarView::render_calendar();
    }

    public function event_form() {
        return CalendarView::render_event_form();
    }

    public function add_event() {
        check_ajax_referer('ccp_nonce', 'nonce');
        $title = $_POST['event_title'];
        $date = $_POST['event_date'];
        $result = $this->model->create_event($title, $date);
        if ($result) {
            wp_send_json_success('Événement ajouté avec succès');
        } else {
            wp_send_json_error('Erreur lors de l\'ajout de l\'événement');
        }
    }

    public function get_events() {
        $events = $this->model->get_events();
        wp_send_json_success($events);
    }

  public function enqueue_scripts() {
    wp_enqueue_style('fullcalendar-css', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css');
    wp_enqueue_script('fullcalendar-js', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js', array('jquery'), null, true);
    
    // Assure-toi que le chemin est correct
    wp_enqueue_style('ccp-calendar-style', plugins_url('public/assets/css/calendar-style.css', dirname(__FILE__)));
    
    // Localiser le script pour ajouter des variables JavaScript
    wp_localize_script('ccp-calendar-script', 'ccp_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ccp_nonce' => wp_create_nonce('ccp_nonce')
    ));
}


}
