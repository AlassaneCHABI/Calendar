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
     
        ob_start(); 
       $events = $this->get_events();
        ?>
        <script>
        let events = <?php echo $events; ?> // Passe les données à JavaScript
        </script>
        
        <div class="row">
            <div class="col-md-6 wrapper-side">
                <header>
                    <div class="row" style="direction: rtl">
                        <i class="bi bi-plus-square" onclick="openModal_add_even()"></i>
                    </div> 
                    <!-- <div>
                        <input type="text" class="form-control" placeholder="search">
                    </div> -->
                    <hr>
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
            $user_id = get_current_user_id();

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
                    'created_by'     => $user_id,
                    'created_at'  => current_time('mysql')
                )
            );

            // Check if insertion was successful
            if ($wpdb->insert_id) {
                wp_send_json([
                    'success' => 'Événement ajouté avec succès',
                    'events' => $this->get_events()
                ]);
              //  wp_send_json_success('');
            } else {
                wp_send_json_error('Erreur lors de l\'ajout de l\'événement');
            }
        } else {
            wp_send_json_error('Le titre est obligatoire');
        }
    }



public function get_events() {
 

    $id_user = get_current_user_id();
    
  return json_encode($this->get_events_with_invitations($id_user)); // Encode les événements et les retourne
}

function get_events_with_invitations($user_id) {
    global $wpdb;
    $table_name_inv = $wpdb->prefix . 'invitations';
    $table_name_ev = $wpdb->prefix . 'events';


   
    // Requête SQL pour récupérer les événements et les invitations
    $results = $wpdb->get_results("
        SELECT 
            e.id AS event_id,
            e.title AS event_title,
            e.start_time AS start_time,
            e.end_time AS end_time,
            e.created_by AS event_creator,
            e.link AS link,
            COALESCE(i.id_guest, 0) AS invited_user,
            COUNT(i.id_guest) AS n_invited,
            i.status AS invitation_status,
            e.start_date AS event_date,
            (CASE 
                WHEN e.created_by = $user_id THEN 1
                ELSE 0
            END) AS by_me
        FROM 
            $table_name_ev e
        LEFT JOIN 
            $table_name_inv i ON e.id = i.id_event
        WHERE 
            e.created_by = $user_id OR i.id_guest = $user_id
        GROUP BY 
            e.id
    ");

    // Formater les résultats
    $events_by_date = [];
    foreach ($results as $row) {
        $date = $row->event_date;

        if (!isset($events_by_date[$date])) {
            $events_by_date[$date] = ['date' => $date, 'events' => []];
        }

        $events_by_date[$date]['events'][] = [
            'title' => $row->event_title,
            'startTime' => $row->start_time,
            'endTime' => $row->end_time,
            'link' => $row->link,
            'byMe' => $row->by_me == 1,
            'status' => $row->invitation_status,
            'n_invited' => $row->n_invited,
        ];
    }

    return array_values($events_by_date);
}

 


}
