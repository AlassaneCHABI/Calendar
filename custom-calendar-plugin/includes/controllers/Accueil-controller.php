<?php

class AccueilController {

    public function __construct() {
        // Register the AJAX action hooks
        add_action('wp_ajax_save_event', [$this, 'save_event']);
        add_action('wp_ajax_nopriv_save_event', [$this, 'save_event']); // For non-logged-in users
    }

    /**
     * Fonction de shortcode [show_calendar] permettant d'afficher le calendrier
     * @param  
     */

    public function show_calendar() {
      $events = $this->get_events();
        ob_start(); ?>
         <script>
        const events = <?php echo $events; ?>; // Passe les données à JavaScript
        </script>
        <div class="row">
            <div class="col-md-6 wrapper-side">
                <header>
                    <div class="row" style="direction: rtl">
                        <i class="bi bi-plus-square" onclick="openModal_add_even()"></i>
                    </div> 
                    <div>
                        <input type="text" class="form-control" placeholder="search">
                    </div>
                    <div class="icons">
                        <span id="prev" class="material-symbols-rounded">chevron_left</span>
                        <p class="current-date"></p>
                        <span id="next" class="material-symbols-rounded">chevron_right</span>
                    </div>
                </header>
                <div class="wrapper">      
                    <div class="calendar">
                        <ul class="weeks">
                            <li>S</li>
                            <li>L</li>
                            <li>M</li>
                            <li>M</li>
                            <li>J</li>
                            <li>V</li>
                            <li>S</li>
                        </ul>
                        <ul class="days"></ul>
                    </div>
                </div> 
            </div>
            <div class="col-md-6" id="event-list"></div>
        </div>
        
        <?php return ob_get_clean();
    }


    
/**
 * Créer un shortcode qui affiche un bouton avec une icône de calendrier
 */ 
function afficher_bouton_calendrier_shortcode($atts) {
  // Définir les attributs par défaut
  $atts = shortcode_atts(
      array(
          'texte' => 'Voir le calendrier',  // Texte par défaut du bouton
          'url' => '#',                    // Lien par défaut du bouton
      ),
      $atts,
      'bouton_calendrier'                 // Nom du shortcode
  );
  
  // Générer le HTML du bouton avec l'icône Dashicon du calendrier
  $html = '<a href="' . esc_url($atts['url']) . '" class="bouton-calendrier" style="display: inline-block; background-color: #0073aa; color: #fff; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-size: 16px;">';
  $html .= '<span class="dashicons dashicons-calendar" style="margin-right: 8px;"></span>';
  $html .= esc_html($atts['texte']);
  $html .= '</a>';

  return $html;
}


    public function save_event() {
        // Check if required fields are set
        if (isset($_POST['title']) && !empty($_POST['title'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'events'; // Replace with your table name

            // Sanitize form data
            $title = sanitize_text_field($_POST['title']);
            $link_post = sanitize_text_field($_POST['link_post']);
            $start_date = sanitize_text_field($_POST['start_date']);
            $start_time = sanitize_text_field($_POST['start_time']);
            $end_date = sanitize_text_field($_POST['end_date']);
            $end_time = sanitize_text_field($_POST['end_time']);
            $location = sanitize_text_field($_POST['location']);
            $description = sanitize_textarea_field($_POST['description']);
            $remember = sanitize_text_field($_POST['remember']);
            $link = sanitize_text_field($_POST['link']);
            $user_id = intval($_POST['user_id']);

            // Insert into database
            $wpdb->insert(
                $table_name,
                array(
                    'link_post'   => $link_post,
                    'title'       => $title,
                    'start_date'  => $start_date,
                    'start_time'  => $start_time,
                    'end_date'    => $end_date,
                    'end_time'    => $end_time,
                    'location'    => $location,
                    'description' => $description,
                    'remember'    => $remember,
                    'link'        => $link,
                    'user_id'     => $user_id,
                    'created_at'  => current_time('mysql')
                )
            );

            // Check if insertion was successful
            if ($wpdb->insert_id) {
                wp_send_json_success('Événement ajouté avec succès');
            } else {
                wp_send_json_error('Erreur lors de l\'ajout de l\'événement');
            }
        } else {
            wp_send_json_error('Le titre est obligatoire');
        }
    }



public function get_events() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'events';

    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    $events = [];

    foreach ($results as $event) {
        $date = $event['start_date']; // Utilise la date de début de l'événement
        if (!isset($events[$date])) {
            $events[$date] = ['date' => $date, 'events' => []];
        }

        $events[$date]['events'][] = [
            'title' => $event['title'],
            'startTime' => $event['start_time'],
            'endTime' => $event['end_time'],
            'bgcolor' => "#65435a", // Ajuste si nécessaire
            'date_debut' => $event['start_date'],
            'date_fin' => $event['end_date'],
            'byMe' => $event['user_id'] === get_current_user_id(),
            //'status' => $event['status'],
            'n_invited' => 0 // Ajuste selon tes besoins
        ];
    }

    return json_encode(array_values($events)); // Encode les événements et les retourne
}



}
