<?php

class AccueilController {

    //private $event_model;

    public function __construct() {
        //$this->event_model = new EventModel();
    }

    public function show_calendar() {
        ob_start(); ?>

     
    <div class="row">

       <div class="col-md-6 wrapper-side">
         <header>
          <div class="row" style ="direction : rtl">   <i class="bi bi-plus-square" onclick="openModal_add_even()"></i></div> 
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


    
}
