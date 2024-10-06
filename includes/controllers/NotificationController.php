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

        echo '
         <div class="modal fade " style="max-width : 100%" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="height: 450px;">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <span class="btn" onclick="hideModal_notification()" aria-hidden="true">&times;</span>
             
            </div>
            <div class="modal-body"><div id="modalEventContent">';
           

        if (empty($notifications)) {
            echo "<p>Aucune notification trouvée.</p>";
        } else {
            echo "<ul>";
            foreach ($notifications as $notification) {
                if($notification['type'] == "Invitation") {
                       // Préparer les données pour l'événement
                        $event_id = $notification['id_invitation'];
                        $event_title = $notification['event_title'];
                        $start_time = $notification['start_time']; // Exemples statiques, à remplacer par les vraies valeurs si disponibles
                        $end_time = $notification['end_time'];
                        $status = $notification['status']; // Statut de la notification
                        $event_date = date('Y-m-d', strtotime($notification['start_date'])). " ".implode(':', array_slice(explode(':', $start_time), 0, 2))."-".date('Y-m-d', strtotime($notification['end_date']))." ".implode(':', array_slice(explode(':', $end_time), 0, 2)); // Formater la date

                        // Déterminer la classe pour la carte d'événement
                        $card_class = ($notification["invitation_status"] !=0) ? "bg-gray" : "bg-pink";
                        $button_icon_class =  'bi-check';
                        $send_icon_class =  'bi-x';
                        echo "<li>
                        <div onclick=\"openModal_show_even($event_id, '{$notification['start_date']}', this)\" style='width:100%' class='{$card_class} p-2 d-flex justify-content-between align-items-center mb-4'>
                            <div class='event-info'>
                                <p class='mb-1 time-range'> ".$event_date. "</p>
                                <p class='mb-0 event-title'>$event_title</p>
                            </div>
                            <div class='event-icons d-flex align-items-center'>";

                        if($notification["invitation_status"] !=0) {
                            echo "<i>Traitée</i>";
                        } else {
                             echo   " <button class='btn btn-light me-2'>
                                    <i class='bi $button_icon_class'></i>
                                </button>
                                <button data-share='#' class='btn btn-light'>
                                    <i class='bi $send_icon_class'></i>
                                </button>";
                        }
                       
                        echo "
                            </div>
                        </div>
                    </li>";
                    

                } else {
                    $status = ($notification['status'] == 0) ? 'Non lue' : 'Lue';

                    echo "<li><strong>{$notification['type']}:</strong> {$notification['message']} <em>({$status})</em></li>";

                }
               
            }
            echo "</ul>";
        }

                echo "   </div>
                </div>
                
            </div>
            </div>
        </div>
        ";
    }
}
?>
