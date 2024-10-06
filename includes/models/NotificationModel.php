<?php

class NotificationModel {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . "notifications"; // Nom de la table des notifications
    }

    // Créer la table notifications
    public function create_table() {
        global $wpdb;
        $table_name = $this->table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            id_invitation mediumint(9) NOT NULL,
            type varchar(255) NOT NULL,
            id_user int(11) NOT NULL,  
            message text NOT NULL,
            status tinyint(1) NOT NULL DEFAULT 0, 
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Fonction pour créer une notification
    public function create_notification($data) {
        global $wpdb;

        // Insérer les données de la notification dans la base de données
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'id_invitation' => $data['id_invitation'],
                'type' => $data['type'],
                'id_user' => $data['id_user'],  // Utilisateur qui reçoit la notification
                'message' => $data['message'],
                'status' => 0    // Statut: 0 = non lue, 1 = lue
            ),
            array(
                '%d',  // id_invitation
                '%s',  // type de notification
                '%d',  // id_user
                '%s',  // message
                '%d',  // status
            )
        );

        return $wpdb->insert_id;  // Renvoie l'ID de la notification insérée
    }

    // Fonction pour récupérer toutes les notifications pour un utilisateur donné
    public function get_notifications_by_user($id_user) {
        global $wpdb;

        // Joindre les notifications avec la table des événements pour récupérer le titre et la date de l'événement
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT n.*, e.title AS event_title, e.id as id_event,e.start_date, e.end_date, e.start_time, e.end_time,i.status as invitation_status
             FROM {$this->table_name} n
             JOIN {$wpdb->prefix}invitations i ON n.id_invitation = i.id
             JOIN {$wpdb->prefix}events e ON i.id_event = e.id
             WHERE n.id_user = %d order by n.created_at desc", $id_user
        ));
        
 
        $formatted_notifications = array();

        // Formater les notifications pour les retourner avec les données de l'événement
        foreach ($results as $notification) {
            $formatted_notifications[] = array(
                'id' => $notification->id,
                'id_invitation' => $notification->id_invitation,
                'type' => $notification->type,
                'id_user' => $notification->id_user,
                'message' => $notification->message,
                'id_event' => $notification->id_event,
                'status' => $notification->status,
                'date' => $notification->created_at,
                'invitation_status' => $notification->invitation_status,
                'event_title' => $notification->event_title,  // Titre de l'événement
                'start_date' => $notification->start_date,    // start_date de l'événement
                'end_date' => $notification->end_date,    // end_date de l'événement
                'start_time' => $notification->start_time,    // start_time de l'événement
                'end_time' => $notification->end_time,    // end_time de l'événement
            );
        }

        return $formatted_notifications;  // Retourne les notifications formatées
    }


    // fonction pour prendre les notifications unread
    public function get_total_unread(){
        global $wpdb;
        $total = $wpdb->get_var(
            $wpdb->prepare("SELECT count('*') as total FROM $this->table_name WHERE id_user = %d AND status=0", get_current_user_id())
        );
        return $total;

    }
    // Fonction pour marquer une notification comme lue
    public function mark_as_read($id_notification) {
        global $wpdb;

        // Mettre à jour le statut de la notification à "1" (lue)
        $result = $wpdb->update(
            $this->table_name,
            array('status' => 1),  // Statut "1" pour lue
            array('id' => $id_notification),
            array('%d'),
            array('%d')
        );

        return $result;
    }

// Fonction pour marquer toutes les notifications comme lues si leur statut est à 0
public function mark_as_read_all() {
    global $wpdb;

    // Mettre à jour le statut de la notification à "1" (lue) uniquement si le statut est à 0 (non lue)
    $result = $wpdb->update(
        $this->table_name,
        array('status' => 1),    // Valeurs à mettre à jour (status = 1)
        array('status' => 0),    // Condition pour ne mettre à jour que les notifications dont le statut est à 0
        array('%d'),             // Type des données mises à jour
        array('%d')              // Type des données pour la condition
    );

    wp_send_json_success(array(
        'result' => $result,
    ));    
}


    // Fonction pour envoyer une notification par e-mail
    public function send_email_notification($id_user, $email, $message) {
        $subject = "Nouvelle Notification";

        // Utilisation de la fonction WordPress pour envoyer un e-mail
        wp_mail($email, $subject, $message);
    }

    // Fonction pour récupérer toutes les notifications non lues d'un utilisateur
    public function get_unread_notifications_by_user($id_user) {
        global $wpdb;

        // Récupérer toutes les notifications non lues pour un utilisateur donné
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $this->table_name WHERE id_user = %d AND status = 0 order by status", $id_user
        ));

        $unread_notifications = array();

        // Formater les notifications non lues
        foreach ($results as $notification) {
            $unread_notifications[] = array(
                'id' => $notification->id,
                'id_invitation' => $notification->id_invitation,
                'type' => $notification->type,
                'id_user' => $notification->id_user,
                'message' => $notification->message,
                'status' => $notification->status,
            );
        }

        return $unread_notifications;  // Retourne les notifications non lues
    }
}
?>
