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
     	
        

		// Gestion invitation
		if (strpos($_SERVER['REQUEST_URI'], '/event/') !== false)  {  

				if(get_current_user_id()) {

					$this->set_guest($_GET['title']);

				} else {
					wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
				}

		}
		// end invitation
      

        ob_start(); 
       $events = $this->get_events();
       global $notification_controller;
		
		echo( '<span id="notif_content">'. $notification_controller->display_user_notifications(get_current_user_id()).'</span>');


        ?>
       
                   
        
        <div class="row plug-cal" >   
            <div class="" style="text-align: end;margin-right: unset;padding-right: unset;">
            <i onclick="openModal_notification()" class="bi bi-info-circle" style="font-size: x-large;"></i>
            <span onclick="openModal_notification()" class="" id="e_notif" style="
            right: 14px;
            position: relative;
            bottom: 10px;
            border-radius: 50px;
            padding: 0px 5px 5px 5px;
            height: 18px;
            display: inline-block;
            font-size: small;
            z-index: 122;
            background: darkgray;
            color: #ff0018;
            font-weight: 800;">
         <?php echo $notification_controller->get_total_unread();  ?>
        </span>
            <i class="bi bi-plus-square" onclick="openModal_add_even(new Date())" style="font-size: x-large;"></i>
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
                            <a href="mailto:?subject=Regardez%20ce%20contenu&body=Voici%20le%20lien:%20[LIEN_A_PARTAGER]" id="Email">
                                <img src="https://img.icons8.com/ios-glyphs/30/email.png" alt="Email">
                            </a>
                            <a href="https://m.me/?link=[LIEN_A_PARTAGER]" id="Messenger">
                                <img src="https://img.icons8.com/ios-glyphs/30/facebook-messenger.png" alt="Messenger">
                            </a>
                            <a href="https://www.instagram.com/sharer/sharer.php?u=[LIEN_A_PARTAGER]" id="Instagram">
                                <img src="https://img.icons8.com/ios-glyphs/30/instagram-new.png" alt="Instagram">
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=[LIEN_A_PARTAGER]&title=[LIEN_A_PARTAGER]&summary=INVITATION&source=/" id="LinkedIn">
                                <img src="https://img.icons8.com/ios-glyphs/30/linkedin.png" alt="LinkedIn">
                            </a>
                            <a href="https://wa.me/?text=[LIEN_A_PARTAGER]" id="WhatsApp">
                                <img src="https://img.icons8.com/ios-glyphs/30/whatsapp.png" alt="WhatsApp">
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=[LIEN_A_PARTAGER]&text=[LIEN_A_PARTAGER]" id="Twitter">
                                <img src="https://img.icons8.com/ios-glyphs/30/twitter.png" alt="Twitter">
                            </a>                    
                        </div>
                </div> 
            </div>

            


         
    </div>
    <script>
        let eventss = <?php echo $events; ?> // Passe les données à JavaScript
        </script>
        <?php 

		return ob_get_clean();
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
        $link_share = sanitize_text_field($_POST['link']);
        $color = sanitize_text_field($_POST['color']);
        $user_id = get_current_user_id();

        // Generate link if not provided
 		$link = "https://calendar.cbeny.com/event/?title=".sanitize_title($title);
        // $link .= '-' . time();

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
                'link_share'  => $link_share,
                'color'       => $color,
                'file_url'    => $file_url, // Store file URL in the database
                'created_by'  => $user_id,
                'created_at'  => current_time('mysql')
            )
        );

        // Check if insertion was successful
        if ($wpdb->insert_id) {
            $event_id = $wpdb->insert_id;
            
            if (isset($_POST['contact']) && !empty($_POST['contact'])) {
                $contact_ids = array_map('intval', $_POST['contact']);
                foreach ($contact_ids as $contact_id) {
                    if ($contact_id > 0) {
                        // Insert invitation
                        $wpdb->insert(
                            $wpdb->prefix . 'invitations',
                            array(
                                'id_event' => $event_id,
                                'id_guest' => $contact_id,
                                'status'   => 'pending',
                            )
                        );

                        // Notification
                        // Créer une nouvelle notification pour un utilisateur
                        global $notification_controller;
                        $notification_id = $notification_controller->create_notification($wpdb->insert_id, $contact_id, 'Invitation', 'Vous avez été invité à un événement.'.$title);


                        // Get guest email (assuming you have a table with guest info)
                        $guest_email = $wpdb->get_var(
                            $wpdb->prepare("SELECT user_email FROM {$wpdb->prefix}users WHERE ID = %d", $contact_id)
                        );

                        // Prepare and send email
                        if ($guest_email) {
                            $subject = 'Invitation à ' . $title;
                            $message = 'Bonjour,

Nous avons le plaisir de vous inviter à notre événement "' . $title . '" qui se tiendra le ' . $start_date . ' à ' . $location . '.

Voici les détails de l\'événement :
- Date : ' . $start_date . ' de ' . $start_time . ' à ' . $end_time . '
- Lieu : ' . $location . '
- Description : ' . $description . '

Veuillez confirmer votre présence en suivant ce lien : ' . $link . '

Nous espérons vous voir lors de cet événement.

Cordialement,
L\'équipe ' . get_bloginfo('name');

                            // Send the email
                            wp_mail($guest_email, $subject, $message);
                        }
                    }
                }
            }

            wp_send_json([
                'success' => 'Événement ajouté avec succès et invitations envoyées',
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


	public function set_guest($title) {
	
		global $wpdb;
    $event = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}events WHERE title LIKE %s", "%".$title."%"));
 $if_exist = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}invitations WHERE id_guest = %d",
        get_current_user_id()
    ));
		
		if ($event && $if_exist==0) {
        $wpdb->insert(
            $wpdb->prefix . 'invitations', 
            array(
                'id_event' => $event->id,
                'id_guest' => get_current_user_id(),
                'status'   => 'pending'
            )
        );

        // Get guest email (assuming you have a table with guest info)
        $guest_email = $wpdb->get_var(
            $wpdb->prepare("SELECT user_email FROM {$wpdb->prefix}users WHERE ID = %d", get_current_user_id())
        );

        // Prepare and send email
        if ($guest_email) {
            $subject = 'Invitation à ' . $event->title;
            $message = 'Bonjour,

Nous avons le plaisir de vous inviter à notre événement "' . $event->title . '" qui se tiendra le ' . $event->start_date . ' à ' . $event->location . '.

Voici les détails de l\'événement :
- Date : ' . $event->start_date . ' de ' . $event->start_time . ' à ' . $event->end_time . '
- Lieu : ' . $event->location . '
- Description : ' . $event->description . '

Veuillez confirmer votre présence en suivant ce lien : ' . $event->link . '

Nous espérons vous voir lors de cet événement.

Cordialement,
L\'équipe ' . get_bloginfo('name');

            // Send the email
            wp_mail($guest_email, $subject, $message);
        }
    
        
    }
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

                        // Get guest email (assuming you have a table with guest info)
                        $guest_email = $wpdb->get_var(
                            $wpdb->prepare("SELECT user_email FROM {$wpdb->prefix}users WHERE ID = %d", $contact_id)
                        );

                        // Prepare and send email
                        if ($guest_email) {
                            $subject = 'Invitation à ' . $title;
                            $message = 'Bonjour,

Nous avons le plaisir de vous inviter à notre événement "' . $title . '" qui se tiendra le ' . $start_date . ' à ' . $location . '.

Voici les détails de l\'événement :
- Date : ' . $start_date . ' de ' . $start_time . ' à ' . $end_time . '
- Lieu : ' . $location . '
- Description : ' . $description . '

Veuillez confirmer votre présence en suivant ce lien : ' . $link . '

Nous espérons vous voir lors de cet événement.

Cordialement,
L\'équipe ' . get_bloginfo('name');

                            // Send the email
                            wp_mail($guest_email, $subject, $message);
                        }
                    
                        
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

        $id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE id_event = %d AND id_guest = %d",
            $event_id,
            $user_id
        ));
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
            // Récupérer l'événement et le créateur de l'événement
            $event = $wpdb->get_row(
                $wpdb->prepare("SELECT id, title, created_by FROM {$wpdb->prefix}events WHERE id = %d", $event_id)
            );

            if ($event) {
                $creator_id = $event->created_by;
                $event_title = $event->title;

                // notification
                $creator_name = get_the_author_meta('user_nicename', $user_id);
                global $notification_controller;
                $notification_id = $notification_controller->create_notification($id, $creator_id, 'Réponse', $creator_name.' a répondu à votre invitation sur l\'évenement '.$event->title);
                // Récupérer l'email du créateur
                $creator_email = get_the_author_meta('user_email', $creator_id);

                if ($creator_email) {
                    // Préparer et envoyer un email au créateur
                    $subject = 'Mise à jour du statut de votre événement : ' . $event_title;
                    $message = 'Bonjour,

Le statut de l\'invitation pour votre événement "' . $event_title . '" a été mis à jour. Voici les détails :

- Statut mis à jour : ' . ($status == 1 ? 'Accepted' : 'Declined') . '
- Utilisateur concerné : ' . wp_get_current_user()->display_name . '

Vous pouvez consulter votre événement pour plus de détails.

Cordialement,
L\'équipe ' . get_bloginfo('name');
                    
                    


                    // Envoyer l'email
                    wp_mail($creator_email, $subject, $message);
                }
            }

			global $notification_controller;
            wp_send_json([
                'success' => 'Statut mis à jour avec succès et email envoyé au créateur',
                'events' => $this->get_events(),
				'notifications' =>  $notification_controller->display_user_notifications(get_current_user_id())
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
   $table_name_users = $wpdb->prefix . 'users';
   
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
			u.user_nicename as creator,
            e.start_date AS event_date,
            (CASE 
                WHEN e.created_by = $user_id THEN 1
                ELSE 0
            END) AS by_me
        FROM 
            $table_name_ev e
        LEFT JOIN 
            $table_name_inv i ON e.id = i.id_event
		LEFT JOIN 
            $table_name_users u ON u.ID = e.created_by
        WHERE 
           ( e.created_by = $user_id OR i.id_guest = $user_id)
        GROUP BY 
            e.id
    ");

    // Formater les résultats
    $events_by_date = [];
    foreach ($results as $row) {
        $date = $row->event_date;

        

       if($row->invitation_status !=2){
if (!isset($events_by_date[$date])) {
            $events_by_date[$date] = ['date' => $date, 'events' => []];
        }
       $events_by_date[$date]['events'][] = [
            'id' => $row->event_id,
            'title' => $row->event_title,
            'startTime' => $row->start_time,
            'endTime' => $row->end_time,
            'link' => $row->link,
			'creator' => $row->creator,
            'color' => $row->color,
            'byMe' => $row->by_me == 1,
            'status' => $row->invitation_status,
            'n_invited' => $row->n_invited,
        ]; }
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
