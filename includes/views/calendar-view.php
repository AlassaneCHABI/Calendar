<?php

class CalendarView {

    public static function render_calendar() {
        ob_start();
        ?>
        <div id="calendar"></div>

        <!-- Conteneur pour la liste des événements en dehors du calendrier -->
        <div id="event-list"></div>
        <?php
        return ob_get_clean();
    }

    public static function render_event_form() {
        ob_start();
        ?>
        <form id="ccp-add-event-form">
            <label for="event_title">Titre de l'événement</label>
            <input type="text" id="event_title" name="event_title" required>

            <label for="event_date">Date de l'événement</label>
            <input type="date" id="event_date" name="event_date" required>

            <input type="submit" value="Ajouter l'événement">
        </form>

        <div id="ccp-message"></div>
        <?php
        return ob_get_clean();
    }
}
