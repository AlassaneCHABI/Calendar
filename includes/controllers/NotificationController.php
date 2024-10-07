<?php

class NotificationController {

    private $notification_model;

    public function __construct() {
        // Initialisation du modèle de notifications
        $this->notification_model = new NotificationModel();
        add_action('wp_ajax_make_read_all_seen', [$this, 'make_read_all_seen']);

    }

    // Méthode pour initialiser la table des notifications
    public function initialize_notifications_table() {
        // Appel à la méthode pour créer la table dans la base de données
        $this->notification_model->create_table();
    }

    // Méthode pour créer une notification
    public function create_notification($id_invitation, $id_user, $type, $message) {
        // Les données de la notification
        $data = array(
            'id_invitation' => $id_invitation,
            'type' => $type,
            'id_user' => $id_user,
            'message' => $message,
            'status' => 0 // Par défaut, la notification est non lue
        );

        // Appel au modèle pour insérer la notification
        return $this->notification_model->create_notification($data);
    }

    // Méthode pour marquer une notification comme lue
    public function mark_notification_as_read($id_notification) {
        // Appel au modèle pour mettre à jour la notification comme lue
        return $this->notification_model->mark_as_read($id_notification);
    }
    // Méthode pour marquer les notification comme lue
    public function make_read_all_seen() {
        // Appel au modèle pour mettre à jour la notification comme lue
        return $this->notification_model->mark_as_read_all();
    }

    // Méthode pour récupérer toutes les notifications d'un utilisateur
    public function get_notifications_for_user($id_user) {
        // Récupère toutes les notifications pour un utilisateur donné
        return $this->notification_model->get_notifications_by_user($id_user);
    }

    // Méthode pour récupérer les notifications non lues d'un utilisateur
    public function get_unread_notifications_for_user($id_user) {
        // Récupère toutes les notifications non lues pour un utilisateur donné
        return $this->notification_model->get_unread_notifications_by_user($id_user);
    }

    // Méthode pour envoyer une notification par e-mail à un utilisateur
    public function send_email_notification($id_invitation, $id_user, $email, $message) {
        // Utiliser le modèle pour envoyer l'e-mail de notification
        return $this->notification_model->send_email_notification($id_user, $email, $message);
    }

    // fonction pour prendre les notifications unread
    public function get_total_unread(){
        return $this->notification_model->get_total_unread();
    }

    // Méthode pour afficher les notifications dans le tableau de bord (exemple simplifié)
    public function display_user_notifications($id_user) {
        // Récupérer les notifications de l'utilisateur
        $notifications = $this->get_notifications_for_user($id_user);

        $echo = '
         <div class="modal fade " style="max-width : 100%" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="height: 450px;overflow:auto">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <span class="btn" onclick="hideModal_notification()" aria-hidden="true">&times;</span>
             
            </div>
            <div class="modal-body"><div id="modalEventContent">';
           

        if (empty($notifications)) {
             $echo .= "<p>Aucune notification trouvée.</p>";
        } else {
             $echo .= "<ul>";
            foreach ($notifications as $notification) {
                $date = $notification['invitation_status']==0 ? "RDV en attente" : "";
				$creator = $creator_name = get_the_author_meta('user_nicename', $notification['created_by']);
                if($notification['type'] == "Invitation") {
                       // Préparer les données pour l'événement
                        $event_id = $notification['id_event'];
                        $event_title = $notification['event_title'];
                        $start_time = $notification['start_time']; // Exemples statiques, à remplacer par les vraies valeurs si disponibles
                        $end_time = $notification['end_time'];
                        $status = ($notification['invitation_status'] == 0) ? 'Pending' : ($notification['invitation_status'] == 1 ? '<button class="btn" disabled style="background: #59df1f; color :white"> <i  class="bi bi-check"></i> </button>   ' : '<button class="btn" disabled style="background: red; color :white"> <i  class="bi bi-x"></i> </button> ') ;
                        $event_date = $this->formatDateToFrench(date('Y-m-d', strtotime($notification['start_date']))). " ".implode(':', array_slice(explode(':', $start_time), 0, 2))." à ".implode(':', array_slice(explode(':', $end_time), 0, 2)); // Formater la date

                        // Déterminer la classe pour la carte d'événement
                        $card_class = ($notification["invitation_status"] !=0) ? "bg-gray" : "bg-pink";
                        $button_icon_class =  'bi-check';
                        $send_icon_class =  'bi-x';
                         $echo .= "<li>
                         <div style='   font-size: x-small;
                         float: inline-start;'><b> $date </b>    </div>
                        <div onclick=\"openModal_show_even($event_id, '{$notification['start_date']}', this)\" style='width:100%' class='{$card_class} p-2 d-flex justify-content-between align-items-center mb-4'>
                       
                        <div class='event-info' style='text-align: left;'>
                                <p class='mb-1 time-range'> ".$event_date. "</p> 
                                <p class='mb-0 event-title time-range'>$event_title  </p>
                            </div>
                            <div class='event-icons d-flex align-items-center'> <i style='    font-size: small;
    color: midnightblue;'>$creator</i> </div>
							<div class='event-icons d-flex align-items-center'>
							";

                        if( $notification["invitation_status"] !=0 ) {

                             $echo .= "<i>$status</i>";

                        } else {

                              $echo .= "<button data-accept='true-$event_id' class='btn btn-light me-2' style='--bs-btn-hover-bg: green; --bs-btn-hover-color: white;' >
                                    <i class='bi $button_icon_class'></i>
                                </button>
                                <button  data-accept='false-$event_id'   style='--bs-btn-hover-bg: red; --bs-btn-hover-color: white;' class='btn btn-light'>
                                    <i class='bi $send_icon_class'></i>
                                </button>";
                        }
                       
                         $echo .= "
                            </div>
                        </div>
                    </li>";
                    

                } else {
                    // $status = ($notification['status'] == 0) ? 'Non lue' : 'Lue';

                     $echo .= "<li>
                     <div style='font-size: x-small;  float: inline-start;'><i> $date </i>  </div> 
                    <strong>{$notification['type']}:</strong> {$notification['message']}</li> <br>  ";

                }
               
            }
             $echo .= "</ul>";
        }

                 $echo .= "   </div>
                </div>
                
            </div>
            </div>
        </div>
        ";
		
		return $echo;
    }




   public function formatDateToFrench($dateString) {
        // Vérifier si la date est valide
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        
        // Si la date n'est pas valide, retourner un message d'erreur
        if (!$date) {
            return 'Date invalide';
        }
    
        // Formater la date
        $formattedDate = $date->format('l d M'); // 'l' pour le jour de la semaine, 'd' pour le jour, 'M' pour le mois en trois lettres
    
        // Traduire le jour de la semaine et le mois en français
        $daysOfWeek = [
            'Sunday' => 'Dimanche',
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
        ];
    
        $months = [
            'Jan' => 'Jan',
            'Feb' => 'Fév',
            'Mar' => 'Mar',
            'Apr' => 'Avr',
            'May' => 'Mai',
            'Jun' => 'Juin',
            'Jul' => 'Juil',
            'Aug' => 'Août',
            'Sep' => 'Sep',
            'Oct' => 'Oct',
            'Nov' => 'Nov',
            'Dec' => 'Déc',
        ];
    
        // Remplacer le jour de la semaine et le mois en anglais par ceux en français
        $formattedDate = str_replace(array_keys($daysOfWeek), array_values($daysOfWeek), $formattedDate);
        $formattedDate = str_replace(array_keys($months), array_values($months), $formattedDate);
    
        return $formattedDate; // Retourner le résultat formaté
    }
    
   
    
}
?>
