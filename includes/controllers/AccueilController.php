<?php

class AccueilController {

    public function __construct() {
            // Register the AJAX action hooks
        add_action('wp_ajax_save_event', [$this, 'save_event']);
        add_action('wp_ajax_nopriv_save_event', [$this, 'save_event']); // For non-logged-in users

        // Register the action for enqueuing scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_action('wp_ajax_update_event', [$this, 'update_event']);
        add_action('wp_ajax_nopriv_update_event', [$this, 'update_event']);

        // Ajouter l'action AJAX pour récupérer l'événement
        add_action('wp_ajax_get_event_callback', [$this, 'get_event_callback']);
        add_action('wp_ajax_nopriv_get_event_callback', [$this, 'get_event_callback']);

        // Ajouter l'action AJAX pour mettre à jour le status de l'évernement
        add_action('wp_ajax_update_status', [$this, 'update_status']);
        add_action('wp_ajax_nopriv_update_status', [$this, 'update_status']);


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
        let eventss = <?php echo $events; ?> // Passe les données à JavaScript
        </script>
        
     
     
    
                   
        
        <div class="row plug-cal" >   
            <div class="" style="direction: rtl;margin-right: unset;padding-right: unset;">
            <i class="bi bi-plus-square" onclick="openModal_add_even(new Date())"></i>
          </div> 
       
            <div class="col-md-6 wrapper-side">
                
                <div class="wrapper">    
                    <div class="icons">
                        <span id="prev" class="material-symbols-rounded">chevron_left</span>
                        <p class="current-date"></p>
                        <span id="next" class="material-symbols-rounded">chevron_right</span>
                    </div>  
                    <div class="calendar">
                        <ul class="weeks">
                            
                            <li>L</li>
                            <li>M</li>
                            <li>M</li>
                            <li>J</li>
                            <li>V</li>
                            <li>S</li>
                            <li>D</li>
                            
                        </ul>
                        <ul class="days"></ul>
                    </div>
                </div> 
            </div>
            <div class="col-md-6" id="event-list"></div>
   

            <div id="myModal" class="modal  text-center p-3 border rounded mt-4">
                <!-- Modal content -->
                <div class="modal-content">
                    <span class="close">&times;</span>  
                    <div class="modals modalls">
                         
                        <!-- Send with Icons Section -->
                        <button>Share by Message</button>
                        <a href="#" id="copyLink" class="copy-link">Copy link</a>
                        <span class="copied-message" id="copiedMessage">Link copied!</span>

                        <div class="iconss">
                        <a href="#"><img src="https://img.icons8.com/ios-glyphs/30/email.png" alt="Email"></a>
                        <a href="#"><img src="https://img.icons8.com/ios-glyphs/30/facebook-messenger.png" alt="Messenger"></a>
                        <a href="#"><img src="https://img.icons8.com/ios-glyphs/30/instagram-new.png" alt="Instagram"></a>
                        <a href="#"><img src="https://img.icons8.com/ios-glyphs/30/linkedin.png" alt="LinkedIn"></a>
                        <a href="#"><img src="https://img.icons8.com/ios-glyphs/30/whatsapp.png" alt="WhatsApp"></a>
                        <a href="#"><img src="https://img.icons8.com/ios-glyphs/30/twitter.png" alt="Twitter"></a>
                    </div>
                </div> 
            </div>

            


         
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


/*public function save_event() {
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
        $color = sanitize_text_field($_POST['color']);
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
                'color'        => $color,
                'created_by'     => $user_id,
                'created_at'  => current_time('mysql')
            )
        );

        // Check if insertion was successful
        if ($wpdb->insert_id) {
            // Debugging: Check the contents of the contact array
            if (isset($_POST['contact']) && !empty($_POST['contact'])) {
                $contact_ids = array_map('intval', $_POST['contact']); // Sanitize and ensure they are integers

                // Log the contact IDs for debugging
                error_log('Contact IDs: ' . print_r($contact_ids, true));

                // Insert each contact ID into the invitation table
                foreach ($contact_ids as $contact_id) {
                    // Ensure the contact_id is valid before inserting
                    if ($contact_id > 0) { // Or any other validation logic you prefer
                        $wpdb->insert(
                            $wpdb->prefix . 'invitations', // Your invitation table name
                            array(
                                'id_event' => $wpdb->insert_id,
                                'id_guest' => $contact_id,
                                'status'   => 'pending', // Or any default status you want
                            )
                        );
                    }
                }
            }

            wp_send_json([
                'success' => 'Événement ajouté avec succès',
                'events' => $this->get_events()
            ]);
        } else {
            wp_send_json_error('Erreur lors de l\'ajout de l\'événement');
        }
    } else {
        wp_send_json_error('Le titre est obligatoire');
    }
}
*/

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
        $color = sanitize_text_field($_POST['color']);
        $user_id = get_current_user_id();
        
        // Handle file upload if a file is present
        $file_url = '';
        if (!empty($_FILES['file']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $uploadedfile = $_FILES['file'];
            $upload_overrides = array('test_form' => false);

            // Handle file upload
            $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
            if ($movefile && !isset($movefile['error'])) {
                $file_url = $movefile['url']; // Get the URL of the uploaded file
            } else {
                wp_send_json_error('Erreur lors de l\'upload du fichier : ' . $movefile['error']);
                return;
            }
        }

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
                'color'       => $color,
                'file_url'    => $file_url, // Store file URL in the database
                'created_by'  => $user_id,
                'created_at'  => current_time('mysql')
            )
        );

        // Check if insertion was successful
        if ($wpdb->insert_id) {
            if (isset($_POST['contact']) && !empty($_POST['contact'])) {
                $contact_ids = array_map('intval', $_POST['contact']);
                foreach ($contact_ids as $contact_id) {
                    if ($contact_id > 0) {
                        $wpdb->insert(
                            $wpdb->prefix . 'invitations',
                            array(
                                'id_event' => $wpdb->insert_id,
                                'id_guest' => $contact_id,
                                'status'   => 'pending',
                            )
                        );
                    }
                }
            }

            wp_send_json([
                'success' => 'Événement ajouté avec succès',
                'events' => $this->get_events()
            ]);
        } else {
            wp_send_json_error('Erreur lors de l\'ajout de l\'événement');
        }
    } else {
        wp_send_json_error('Le titre est obligatoire');
    }
}




function get_event_callback() {
    global $wpdb;
    $event_id = intval($_GET['event_id']);
    $user_id = get_current_user_id();

    // Récupérer les détails de l'événement
    $event = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_events WHERE id = %d", $event_id));

    // Récupérer les contacts associés avec les détails des utilisateurs
    $invitations = $wpdb->get_results($wpdb->prepare("
        SELECT i.id_guest, u.display_name AS user_name , u.user_nicename As user_last_name, i.status AS status
        FROM wp_invitations AS i
        JOIN wp_users AS u ON i.id_guest = u.ID
        WHERE i.id_event = %d
    ", $event_id));

    // prendre le satus dr l'utilisateur actuel
    $user_status = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_invitations WHERE id_event = %d AND id_guest = %d", $event_id,$user_id));


    // Formater les contacts pour ne garder que les noms
    $contacts = array_map(function($invitation) {
        return array(
            'id' => $invitation->id_guest, 
            'nom' => $invitation->user_last_name ,
            'prenom' => $invitation->user_name,
            'status' => $invitation->status
        );
    }, $invitations);

    // Retourner les données
    wp_send_json_success(array(
        'event' => $event,
        'contacts' => $contacts,
        'user_status' => $user_status->status
    ));
}


public function update_event() {
    // Vérifier si les champs requis sont définis
    if (isset($_POST['event_id']) && !empty($_POST['event_id']) && isset($_POST['title']) && !empty($_POST['title'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'events'; // Remplacez par le nom de votre table

        // Sanitize form data
        $event_id = intval($_POST['event_id']);
        
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
        $color = sanitize_text_field($_POST['color']);
        $user_id = get_current_user_id();
        
        // Mettre à jour l'événement dans la base de données
        $updated = $wpdb->update(
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
                'color'        => $color,
                'created_by'     => $user_id,
                'created_at'  => current_time('mysql')
            ),
            array('id' => $event_id) // Condition pour identifier l'événement à mettre à jour
        );

        // Vérifier si la mise à jour a réussi
        if ($updated !== false) {
            // Mise à jour des contacts associés
            if (isset($_POST['contact']) && !empty($_POST['contact'])) {
                $contact_ids = array_map('intval', $_POST['contact']);

                // Supprimer les anciennes invitations liées à cet événement
                $wpdb->delete($wpdb->prefix . 'invitations', array('id_event' => $event_id));

                // Ajouter les nouveaux contacts dans la table des invitations
                foreach ($contact_ids as $contact_id) {
                    if ($contact_id > 0) {
                        $wpdb->insert(
                            $wpdb->prefix . 'invitations', 
                            array(
                                'id_event' => $event_id,
                                'id_guest' => $contact_id,
                                'status'   => 'pending'
                            )
                        );
                    }
                }
            }

            wp_send_json([
                'success' => 'Événement ajouté avec succès',
                'events' => $this->get_events()
            ]);
        } else {
            wp_send_json_error('Erreur lors de la mise à jour de l\'événement');
        }
    } else {
        wp_send_json_error('Le titre et l\'ID de l\'événement sont obligatoires');
    }
}

public function update_status() {
    // Vérifier si les champs requis sont définis
    if (isset($_POST['status']) && isset($_POST['event_id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'invitations'; // Remplacez par le nom de votre table

        // Sanitize form data
        $event_id = intval($_POST['event_id']);
        $status = sanitize_text_field($_POST['status']);
        $user_id = get_current_user_id(); // Récupérer l'ID de l'utilisateur connecté

        // Mettre à jour l'invitation dans la base de données
        $updated = $wpdb->update(
            $table_name,
            array(
                'status' => $status 
            ),
            array(
                'id_event' => $event_id,   
                'id_guest' => $user_id     
            )
        );

        // Vérifier si la mise à jour a réussi
        if ($updated !== false) {
            wp_send_json([
                'success' => 'Statut mis à jour avec succès',
                'events' => $this->get_events() 
            ]);
        } else {
            wp_send_json_error('Erreur lors de la mise à jour du statut');
        }
    } else {
        wp_send_json_error('Le statut et l\'ID de l\'événement sont obligatoires');
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
            e.color AS color,
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
            'id' => $row->event_id,
            'title' => $row->event_title,
            'startTime' => $row->start_time,
            'endTime' => $row->end_time,
            'link' => $row->link,
            'color' => $row->color,
            'byMe' => $row->by_me == 1,
            'status' => $row->invitation_status,
            'n_invited' => $row->n_invited,
        ];
    }

    return array_values($events_by_date);
}

 public function get_all_users_for_contacts() {
        $users = get_users(); // Récupère tous les utilisateurs

        $user_list = [];
        foreach ($users as $user) {
            if($user->ID != get_current_user_id())
            $user_list[] = [
                'id' => $user->ID,
                'nom' => $user->user_nicename,
                'prenom' => $user->display_name
            ];
        }

        return $user_list;
    }


public function enqueue_scripts() {
        wp_enqueue_script('your-script-handle');

        $users = $this->get_all_users_for_contacts();
        wp_localize_script('your-script-handle', 'php_vars', ['ajax_url' => admin_url('admin-ajax.php'), 'users' => $users]);
    }  
    


}
