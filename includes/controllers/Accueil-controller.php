<?php

class AccueilController {


    public function __construct() {
    }

    /**
     * Fonction de shortcode [show_calendar] permettant d'afficher le calendrier
     * @param  
     */
    public function show_calendar() {
        ob_start(); ?>     
    <div class="row">

       <div class="col-md-6 wrapper-side">
         <header>
          <div class="row" style ="direction : rtl">   <i class="bi bi-plus-square"></i></div> 
          <div class="" > <input type="text" class="form-control" placeholder="search"></div>
                
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
              
                <ul class="days">  </ul>
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


    
}
