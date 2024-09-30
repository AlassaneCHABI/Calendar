
//console.log(php_vars);
// Function to create and show the modal
function openModal_add_even(dateStr) {
    console.log("Bouton cliqué");
   // Convertir la date en format lisible
    const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', locale: 'fr-FR' };
    const readableDate = new Date(dateStr).toLocaleDateString('fr-FR', options);

    // Formater la date au format requis (YYYY-MM-DD) pour stockage interne
    const formattedDate = new Date(dateStr).toISOString().split('T')[0];

    // Create the modal HTML structure
    const modalHTML = `
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 text-center position-relative">Ajouter un événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             
            <form id="add-event-form">

                <input type="hidden" id="lat" name="lat">
                <input type="hidden" id="lon" name="lon">
                
                <div class="form-group">
                <input type="text" id="title" name="title"  required placeholder="Titre"  class="form-control">
               </div>
               
                <div class="custom-container mb-3 " >
                <div class="row ">
                    <div class="col-md-7 " >
                        <input type="date" id="event_start_date" name="start_date" required style="font-size:13px">
                    </div>
                    <div class="col-md-5">
                        <input type="time" id="event_start_time" name="start_time" required >
                    </div>

                     <div class="col-md-7">
                        <input type="date" id="event_end_date" name="end_date" required >
                    </div>
                    <div class="col-md-5">
                        <input type="time" id="event_end_time" name="end_time" required >
                    </div>
                </div>
            </div>

              <div class="custom-container mb-3" >
                
                    <div class="">
                        <input type="text" class=" click-input" id="location"  name="location" placeholder="Lieu" onclick="showMap()" readonly>
                    </div>
    
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="address-search" name="address" placeholder="Rechercher une adresse" oninput="searchAddress()" style="display: none;" class="form-control search-field">
                    </div>
                    <div id="address-results"></div>
                </div>

                <div id="map" style="height: 300px; display: none;"></div>
                 </div>
               <div class="custom-container mb-3 " >
               <div class="form-group ">
                <input type="text" id="contact" class=" click-input" name="contact[]" readonly placeholder="Contact" onclick="toggleSearchContainer()" >
                    <div id="search-container" style="display: none;">
                        <input type="text" id="contact-search" placeholder="Rechercher un contact..." oninput="filterContacts()">
                        <ul id="contact-list"></ul>
                    </div>
                </div>

               <div class="separator" style="display:none" id="separator_display"></div>
                
                <div id="selected-contacts" style="font-size:12px"></div>      
                 <div id="more-contacts" style="display:none; cursor:pointer; font-size: 0.875rem;" onclick="showMoreContacts()">
                    <i class="bi bi-chevron-down"></i> <!-- Icône de chevron -->
                    <span id="more-contacts-text"></span> <!-- Texte dynamique -->
                </div>        
              </div>

                <input type="hidden" id="selectedColor" name="color" value="">
 
                <div class="form-group">
                <textarea id="description" name="description" placeholder="Ajouter une description" class="form-control"></textarea>
               </div>
               <input type="text" id="link_post" name="link_post" required placeholder="Lien du post" class="form-control">

                <select class="form-control" name="remember">
                                          <option value="Ajouter un rappel">Ajouter un rappel</option>
                                          <option value="Au début">Au début</option>
                                          <option value="5 minutes avant">5 minutes avant</option>
                                          <option value="15 minutes avant">15 minutes avant</option>
                                          <option value="1 heure avant">1 heure avant</option>
                                          <option value="2 heure avant">2 heure avant</option>
                                          <option value="1 jour avant">1 jour avant</option>
                                          <option value="2 jour avant">2 jour avant</option>
                                          <option value="Personnalisé">Personnalisé</option>
                            </select>
                <div class="form-group">
                <input type="text" id="link" name="link"  placeholder="Ajouter un lien"  class="form-control">
               </div>
               <div class="form-group">
               
                     <div class="d-flex justify-content-center">
                        <div  style="background:#f8bbd0b8 " class="color-bubble " onclick="selectBubble(this)" data-color="#f8bbd0b8"></div>
                        <div style="background:#93dbeb6b " class="color-bubble " onclick="selectBubble(this)" data-color="#93dbeb6b"></div>
                        <div  style="background:#e0fbbd " class="color-bubble " onclick="selectBubble(this)" data-color="#e0fbbd"></div>
                        <div style="background:#bcb3e191 " class="color-bubble " onclick="selectBubble(this)" data-color="#bcb3e191"></div>
                    </div>

                <input type="hidden" id="selectedColor" name="color" value="">
                <input type="file" id="file" name="file"  placeholder="Ajouter un fichier" class="form-control">
               </div>
                
                <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
                <input type="submit" value="Valider">
            </form>
        </div>
    </div>
</div>
`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    var myModal = new bootstrap.Modal(document.getElementById('addEventModal'), {
        keyboard: false
    });
    myModal.show();

      // Initialiser Flatpickr pour les champs de date avec locale en français
    flatpickr("#event_start_date", {
        altInput: true,
        altFormat: "l, d M Y", // Format lisible
        dateFormat: "Y-m-d",    // Format requis pour le stockage
        defaultDate: formattedDate, // Date par défaut
        locale: {
            firstDayOfWeek: 1, // Lundi comme premier jour de la semaine
            weekdays: {
                shorthand: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                longhand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr) {
            // Mettre à jour le champ avec la date au format lisible
            document.getElementById('event_start_date').value = new Date(selectedDates[0]).toLocaleDateString('fr-FR', options);
        }
    });

    flatpickr("#event_end_date", {
        altInput: true,
        altFormat: "l, d M Y", // Format lisible
        dateFormat: "Y-m-d",    // Format requis pour le stockage
        defaultDate: formattedDate, // Date par défaut
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                longhand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr) {
            // Mettre à jour le champ avec la date au format lisible
            document.getElementById('event_end_date').value = new Date(selectedDates[0]).toLocaleDateString('fr-FR', options);
        }
    });

// Récupérer l'heure actuelle pour les champs d'heure
    const now = new Date();
    const currentHours = String(now.getHours()).padStart(2, '0');
    const currentMinutes = String(now.getMinutes()).padStart(2, '0');
    const currentTime = `${currentHours}:${currentMinutes}`;

    // Préremplir les champs d'heure avec l'heure actuelle
    document.getElementById('event_start_time').value = currentTime;
    document.getElementById('event_end_time').value = currentTime;

    document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('addEventModal').remove();
    });

    // Capture form submission and send it via AJAX
    document.getElementById('add-event-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent form submission

        let formData = new FormData(this);
        formData.append('action', 'save_event');

        // Send the form data via AJAX
        fetch(php_vars.ajax_url, {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                events = JSON.parse(data.events) 
                renderCalendar();
                alert('Événement ajouté avec succès');
                
                var myModal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
                myModal.hide();
            } else {
                alert('Erreur : ' + data.data);
            }
        })
        .catch(error => console.error('Erreur lors de l\'ajout de l\'événement :', error));
    });
}

// Function to show the modal with event details
function openModal_show_even_by_me(eventId) {
    console.log("Bouton cliqué avec l'ID de l'événement : " + eventId);
    
    // Requête AJAX pour récupérer les données de l'événement
    fetch(`${php_vars.ajax_url}?action=get_event_callback&event_id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = data.data.event; 
                const contacts = data.data.contacts;
                const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', locale: 'fr-FR' };
                const readableDate = new Date(event.start_date).toLocaleDateString('fr-FR', options);

                // Formater la date au format requis (YYYY-MM-DD) pour stockage interne
                const formattedDate = new Date(event.start_date).toISOString().split('T')[0];

                // Créer la structure HTML du modal
                const modalHTML = `
                <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center position-relative">Détails de l'événement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="add-event-form">
                                <input type="hidden" id="event_id" name="event_id" value="${event.id}">
                              
                               <div class="form-group">
                                <label for="title">Titre</label>
                                <input type="text" id="title" name="title" value="${event.title}" required placeholder="Titre" readonly class="form-control">
                            </div>

                        <div class="custom-container mb-3 " >
                            <div class="row ">
                                <div class="col-md-7 " >
                                    <input type="date" id="event_start_date" name="start_date" required style="font-size:13px">
                                </div>
                                <div class="col-md-5">
                                    <input type="time" id="event_start_time" name="start_time" value="${event.start_time}"  required >
                                </div>

                                 <div class="col-md-7">
                                    <input type="date" id="event_end_date" name="end_date" required >
                                </div>
                                <div class="col-md-5">
                                    <input type="time" id="event_end_time" name="end_time" value="${event.end_time}" required >
                                </div>
                            </div>
                        </div>
                            
                        <div class="custom-container mb-3" >
                        
                            <div class="">
                                <input type="text" class=" click-input" id="location"  name="location" placeholder="Lieu" onclick="showMap()" value="${event.location}"  readonly>
                            </div>
            
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="address-search" name="address" placeholder="Rechercher une adresse" oninput="searchAddress()" style="display: none;" class="form-control search-field">
                            </div>
                            <div id="address-results"></div>
                            <div id="map" style="height: 300px;"></div>
                        </div>
                        </div>

                            <div class="custom-container mb-3 " >
                           <div class="form-group ">
                            <input type="text" id="contact" class=" click-input" name="contact[]" readonly placeholder="Contact" onclick="toggleSearchContainer()" >
                                <div id="search-container" style="display: none;">
                                    <input type="text" id="contact-search" placeholder="Rechercher un contact..." oninput="filterContacts()">
                                    <ul id="contact-list"></ul>
                                </div>
                            </div>

                           <div class="separator" style="display:none" id="separator_display"></div>
                            
                            <div id="selected-contacts" style="font-size:12px"></div>      
                             <div id="more-contacts" style="display:none; cursor:pointer; font-size: 0.875rem;" onclick="showMoreContacts()">
                                <i class="bi bi-chevron-down"></i> <!-- Icône de chevron -->
                                <span id="more-contacts-text"></span> <!-- Texte dynamique -->
                            </div>        
                          </div>

                            <br>
                           <div class="form-group">
                                <label for="title">Description</label>
                               <textarea id="description" class="form-control" name="description" placeholder="Ajouter une description">${event.description}</textarea>
                            </div>
<div class="form-group">
                            <label for="link_post">Lien du post</label>
                            <input type="text" id="link_post" name="link_post" value="${event.link_post}" required placeholder="Lien du post" readonly class="form-control">
                           </div>
                            <div class="form-group">
                               <label>Alerte</label>
                              <select class="form-control" name="remember">
                                          <option value="${event.remember}">${event.remember}</option>
                                          <option value="Au début">Au début</option>
                                          <option value="5 minutes avant">5 minutes avant</option>
                                          <option value="15 minutes avant">15 minutes avant</option>
                                          <option value="1 heure avant">1 heure avant</option>
                                          <option value="2 heure avant">2 heure avant</option>
                                          <option value="1 jour avant">1 jour avant</option>
                                          <option value="2 jour avant">2 jour avant</option>
                                          <option value="Personnalisé">Personnalisé</option>
                            </select>
                            </div>

                            <div class="form-group">
                               <label>Lien</label>
                                <input type="text" id="link" name="link" placeholder="Ajouter un lien" value="${event.link}"  class="form-control">
                            </div>

                             <div class="form-group">
                               <label>Fichier</label>
                               <input type="file" id="file" name="file" placeholder="Ajouter un lien" class="form-control">
                            </div> 
                            
                                <div class="d-flex justify-content-center">
                                     <div style="background:#f8bbd0b8 "  class="color-bubble  ${event.color == '#f8bbd0b8' ? 'selected' : ''} " onclick="selectBubble(this)" data-color="#f8bbd0b8"></div>
                                    <div style="background:#93dbeb6b " class="color-bubble   ${event.color == '#93dbeb6b' ? 'selected' : ''} " onclick="selectBubble(this)" data-color="#93dbeb6b"></div>
                                    <div style="background:#e0fbbd " class="color-bubble   ${event.color == '#e0fbbd' ? 'selected' : ''} " onclick="selectBubble(this)" data-color="#e0fbbd"></div>
                                    <div  style="background:#bcb3e191 " class="color-bubble  ${event.color == '#bcb3e191' ? 'selected' : ''} " onclick="selectBubble(this)" data-color="#bcb3e191"></div>
                                </div>
                                <input type="hidden" id="selectedColor" name="color" value="${event.color}">

                                <input type="hidden" name="user_id" value="${event.user_id}">
                                <input type="submit" value="Valider">
                            </form>
                        </div>
                    </div>
                </div>`;

                // Ajouter le modal au corps
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                // Initialiser et afficher le modal
                var myModal = new bootstrap.Modal(document.getElementById('addEventModal'), {
                    keyboard: false
                });
                myModal.show();

                showMapWithLocation(event.location);

                 // Boucle sur les contacts et ajouter chaque contact sélectionné
                contacts.forEach(contactId => {
                    console.log("Ici nous somme");
                    console.log(contactId);
                        addContact(contactId); // Appel de la fonction pour ajouter le contact
                });

                   // Initialiser Flatpickr pour les champs de date avec locale en français
    flatpickr("#event_start_date", {
        altInput: true,
        altFormat: "l, d M Y", // Format lisible
        dateFormat: "Y-m-d",    // Format requis pour le stockage
        defaultDate: formattedDate, // Date par défaut
        locale: {
            firstDayOfWeek: 1, // Lundi comme premier jour de la semaine
            weekdays: {
                shorthand: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
               longhand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr) {
            // Mettre à jour le champ avec la date au format lisible
            document.getElementById('event_start_date').value = new Date(selectedDates[0]).toLocaleDateString('fr-FR', options);
        }
    });

    flatpickr("#event_end_date", {
        altInput: true,
        altFormat: "l, d M Y", // Format lisible
        dateFormat: "Y-m-d",    // Format requis pour le stockage
        defaultDate: formattedDate, // Date par défaut
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                longhand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr) {
            // Mettre à jour le champ avec la date au format lisible
            document.getElementById('event_end_date').value = new Date(selectedDates[0]).toLocaleDateString('fr-FR', options);
        }
    });

                // Nettoyer le modal du DOM après sa fermeture
                document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function () {
                    document.getElementById('addEventModal').remove();
                });

        // Capture form submission and send it via AJAX
            document.getElementById('add-event-form').addEventListener('submit', function(e) {
                e.preventDefault(); // Empêche l'envoi du formulaire

                let formData = new FormData(this);
                formData.append('action', 'update_event'); // Spécifiez l'action pour la mise à jour

                // Envoyer les données via AJAX
                fetch(php_vars.ajax_url, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Événement mis à jour avec succès');
                        var myModal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
                        myModal.hide();
                        // Rafraîchir les événements
                        events = JSON.parse(data.events);
                        renderCalendar();
                    } else {
                        alert('Erreur : ' + data.data);
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour de l\'événement :', error));
            });
            } else {
                alert('Erreur lors de la récupération de l\'événement : ' + data.data);
            }
        })
        .catch(error => console.error('Erreur lors de la récupération de l\'événement :', error));
}

// Function to show the modal with event details
function openModal_show_even(eventId) {
    console.log("Bouton cliqué avec l'ID de l'événement : " + eventId);

    // Requête AJAX pour récupérer les données de l'événement
    fetch(`${php_vars.ajax_url}?action=get_event_callback&event_id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = data.data.event; 
                const contacts = data.data.contacts;
                const user_status = data.data.user_status;
                const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', locale: 'fr-FR' };
                const readableDate = new Date(event.start_date).toLocaleDateString('fr-FR', options);
 
                // Formater la date au format requis (YYYY-MM-DD) pour stockage interne
                const formattedDate = new Date(event.start_date).toISOString().split('T')[0];
              
                // Créer la structure HTML du modal
                const modalHTML = `
                <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title w-100 text-center position-relative">Détails de l'invitation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="add-event-form">
                            <div class="form-group">
                                <label for="status">Statut</label>
                                <select class="form-control" name="status" id="status">
                                    <option ${user_status.status == '0' ? 'selected' :''} value="0">Pending</option>
                                    <option  ${user_status.status == '1' ? 'selected' :''} value="1">Accepted</option>
                                    <option  ${user_status.status == '2' ? 'selected' :''} value="2">Declined</option>
                                </select>
                            </div>

                            <input type="hidden" id="event_id" name="event_id" value="${event.id}">
                             <div class="form-group">
                            <label for="link_post">Lien du post</label>
                            <input type="text" id="link_post" name="link_post" value="${event.link_post}" required placeholder="Lien du post" readonly class="form-control">
                           </div>
                               <div class="form-group">
                                <label for="title">Titre</label>
                                <input type="text" id="title" name="title" value="${event.title}" required placeholder="Titre" readonly class="form-control">
                            </div>

                        <div class="custom-container mb-3 " >
                            <div class="row ">
                                <div class="col-md-7 " >
                                    <input type="date" id="event_start_date" name="start_date" readonly required style="font-size:13px">
                                </div>
                                <div class="col-md-5">
                                    <input type="time" id="event_start_time" name="start_time" value="${event.start_time}" readonly  required >
                                </div>

                                 <div class="col-md-7">
                                    <input type="date" id="event_end_date" name="end_date" readonly required >
                                </div>
                                <div class="col-md-5">
                                    <input type="time" id="event_end_time" name="end_time" value="${event.end_time}" readonly required >
                                </div>
                            </div>
                        </div>
                            
                      <div class="custom-container mb-3" >
                        
                            <div class="">
                                <input type="text" class=" click-input" id="location"  name="location" placeholder="Lieu" value="${event.location}"  readonly>
                            </div>
            
                        <div class="mb-3">
                            <div id="address-results"></div>
                            <div id="map" style="height: 300px;"></div>
                        </div>
                        </div>

                        <div class="custom-container mb-3 " >
                           <div class="form-group ">
                            <input type="text" id="contact" class=" click-input" name="contact[]" readonly placeholder="Contact" >
                                
                            </div>
                            
                            <div id="selected-contacts" style="font-size:12px"></div>      
                             <div id="more-contacts" style="display:none; cursor:pointer; font-size: 0.875rem;" onclick="showMoreContacts()">
                                <i class="bi bi-chevron-down"></i> <!-- Icône de chevron -->
                                <span id="more-contacts-text"></span> <!-- Texte dynamique -->
                            </div>        
                          </div>

                                <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" placeholder="Ajouter une description" readonly class="form-control">${event.description}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="remember">Alerte</label>
                                <input type="text" id="remember" name="remember" value="${event.remember}" placeholder="Alerte" readonly class="form-control">
                            </div>
                                 <div class="form-group">
                                <label for="link">Lien</label>
                                <input type="text" id="link" name="link" value="${event.link}" placeholder="Ajouter un lien" readonly class="form-control">
                            </div>
                                <div class="form-group">
                                <label for="file">Fichier</label>
                                <input type="file" id="file" name="file" placeholder="Ajouter un fichier" readonly class="form-control">
                            </div>
                                <input type="hidden" name="user_id" value="${event.user_id}">
                                <input type="submit" value="Valider">
                            </form>
                        </div>
                    </div>
                </div>`;

                // Ajouter le modal au corps
                document.body.insertAdjacentHTML('beforeend', modalHTML);

                // Initialiser et afficher le modal
                var myModal = new bootstrap.Modal(document.getElementById('addEventModal'), {
                    keyboard: false
                });
                myModal.show();

                showMapWithLocation(event.location);

                 // Boucle sur les contacts et ajouter chaque contact sélectionné
                contacts.forEach(contactId => {
                    console.log("Ici nous somme");
                    console.log(contactId);
                        addContact1(contactId); // Appel de la fonction pour ajouter le contact
                });

                   // Initialiser Flatpickr pour les champs de date avec locale en français
    flatpickr("#event_start_date", {
        altInput: true,
        altFormat: "l, d M Y", // Format lisible
        dateFormat: "Y-m-d",    // Format requis pour le stockage
        defaultDate: formattedDate, // Date par défaut
        locale: {
            firstDayOfWeek: 1, // Lundi comme premier jour de la semaine
            weekdays: {
                shorthand: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                longhand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr) {
            // Mettre à jour le champ avec la date au format lisible
            document.getElementById('event_start_date').value = new Date(selectedDates[0]).toLocaleDateString('fr-FR', options);
        },
        clickOpens: false,
    });

    flatpickr("#event_end_date", {
        altInput: true,
        altFormat: "l, d M Y", // Format lisible
        dateFormat: "Y-m-d",    // Format requis pour le stockage
        defaultDate: formattedDate, // Date par défaut
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                 longhand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr) {
            // Mettre à jour le champ avec la date au format lisible
            document.getElementById('event_end_date').value = new Date(selectedDates[0]).toLocaleDateString('fr-FR', options);
        },
        clickOpens: false,
    });

                // Nettoyer le modal du DOM après sa fermeture
                document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function () {
                    document.getElementById('addEventModal').remove();
                });

        // Capture form submission and send it via AJAX
            document.getElementById('add-event-form').addEventListener('submit', function(e) {
                e.preventDefault(); // Empêche l'envoi du formulaire

                let formData = new FormData(this);
                formData.append('action', 'update_status'); // Spécifiez l'action pour la mise à jour

                // Envoyer les données via AJAX
                fetch(php_vars.ajax_url, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Statut mis à jour avec succès');
                        var myModal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
                        myModal.hide();
                        // Rafraîchir les événements
                        events = JSON.parse(data.events);
                        renderCalendar();
                    } else {
                        alert('Erreur : ' + data.data);
                    }
                })
                .catch(error => console.error('Erreur lors de la mise à jour de l\'événement :', error));
            });
            } else {
                alert('Erreur lors de la récupération de l\'événement : ' + data.data);
            }
        })
        .catch(error => console.error('Erreur lors de la récupération de l\'événement :', error));
}

let map;
let marker;
 function showMap() {
            if (!map) {
                map = L.map('map').setView([48.8566, 2.3522], 13); // Coordonnées de Paris

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);
            }

            document.getElementById('map').style.display = 'block';
            document.getElementById('address-search').style.display = 'block';
        }

         // Fonction pour rechercher une adresse avec Nominatim
    function searchAddress() {
        const query = document.getElementById('address-search').value;
        if (!query) {
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
            .then(response => response.json())
            .then(data => {
                const resultsContainer = document.getElementById('address-results');
                resultsContainer.innerHTML = ''; // Efface les résultats précédents

                if (data.length > 0) {
                    data.forEach(result => {
                        const resultItem = document.createElement('div');
                        resultItem.innerHTML = `<p style="cursor:pointer;">${result.display_name}</p>`;
                        
                        // Rendre chaque élément cliquable
                        resultItem.addEventListener('click', function() {
                            selectAddress(result.lat, result.lon, result.display_name);
                        });
                        
                        resultsContainer.appendChild(resultItem);
                    });
                } else {
                    resultsContainer.innerHTML = '<p>Aucune adresse trouvée.</p>';
                }
            })
            .catch(error => console.error('Erreur lors de la recherche d\'adresse:', error));
    }

    // Fonction pour sélectionner une adresse et la centrer sur la carte
    function selectAddress(lat, lon, displayName) {
        showMap();

        // Centrer la carte sur les coordonnées sélectionnées
        map.setView([lat, lon], 13);

        // Si un marqueur existe déjà, le supprimer
        if (marker) {
            map.removeLayer(marker);
        }

        // Ajouter un nouveau marqueur à l'emplacement sélectionné
        marker = L.marker([lat, lon]).addTo(map).bindPopup(displayName).openPopup();

        // Mettre à jour le champ de lieu avec l'adresse sélectionnée
        document.getElementById('location').value = displayName;
        document.getElementById('address-results').style.display = 'none';
        document.getElementById('address-search').value = '';
    }

function showMapWithLocation(location) {
    if (!map) {
        map = L.map('map').setView([48.8566, 2.3522], 13); // Coordonnées par défaut (Paris)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    // Rechercher l'adresse avec Nominatim
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${location}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = data[0].lat;
                const lon = data[0].lon;
                const displayName = data[0].display_name;

                // Centrer la carte sur les coordonnées de l'adresse
                map.setView([lat, lon], 13);

                // Supprimer l'ancien marqueur s'il existe
                if (marker) {
                    map.removeLayer(marker);
                }

                // Ajouter un nouveau marqueur
                marker = L.marker([lat, lon]).addTo(map).bindPopup(displayName).openPopup();
            } else {
                console.log('Aucune adresse trouvée');
            }
        })
        .catch(error => console.error('Erreur lors de la recherche de l\'adresse :', error));
}

function toggleSearchContainer() {
    const searchContainer = document.getElementById('search-container');

    // Toggle the visibility of the search container
    searchContainer.style.display = (searchContainer.style.display === 'none') ? 'block' : 'none';
}

function filterContacts() {
    const searchInput = document.getElementById('contact-search').value.toLowerCase();
    const contactList = document.getElementById('contact-list');
    const users = php_vars.users;

    // Clear previous results
    contactList.innerHTML = '';

    // Filter users based on the input
    const filteredUsers = users.filter(user => user.nom.toLowerCase().includes(searchInput));

    if (filteredUsers.length > 0) {
        filteredUsers.forEach(user => {
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center mb-2'; // Flexbox layout

            // Colonne pour l'icône de l'utilisateur
            const userIconCol = document.createElement('div');
            const userIcon = document.createElement('i');
            userIcon.className = 'bi bi-person-circle'; // Classe pour l'icône
            userIconCol.appendChild(userIcon);
            userIconCol.style.flex = '0'; // Prendre un espace égal

            // Colonne pour le nom
            const nameCol = document.createElement('div');
            nameCol.textContent = `${user.nom} ${user.prenom}`;
            nameCol.style.flex = '3'; // Prendre plus d'espace
            nameCol.style.textAlign = 'center'; // Centrer le texte

            // Colonne pour l'icône d'ajout
            const addIconCol = document.createElement('div');
            const addIcon = document.createElement('i');
            addIcon.className = 'bi bi-plus-circle'; // Classe pour l'icône d'ajout
            addIcon.style.cursor = 'pointer';
            addIcon.addEventListener('click', function() {
                addSelectedContact(user);
            });
            addIconCol.appendChild(addIcon);
            addIconCol.style.flex = '1'; // Prendre un espace égal

            row.appendChild(userIconCol);
            row.appendChild(nameCol);
            row.appendChild(addIconCol);
            contactList.appendChild(row);
        });
    } else {
        const noResultsItem = document.createElement('div');
        noResultsItem.textContent = 'Aucun contact trouvé';
        noResultsItem.style.color = 'red';
        contactList.appendChild(noResultsItem);
    }
}

function addSelectedContact(user) {
    const selectedContactsContainer = document.getElementById('selected-contacts');

    // Vérifier si le contact est déjà sélectionné
    const existingContact = Array.from(selectedContactsContainer.children).some(contactDiv => {
        const hiddenInput = contactDiv.querySelector('input[type="hidden"]');
        return hiddenInput && hiddenInput.value === user.id; // Comparer les IDs
    });

    if (existingContact) {
        alert(`${user.nom} ${user.prenom} est déjà sélectionné.`);
        return; // Sortir de la fonction si le contact est déjà ajouté
    }

    // Effacer le champ de recherche et cacher le conteneur de recherche
    document.getElementById('contact-search').value = '';
    document.getElementById('search-container').value = '';
    document.getElementById('contact-list').innerHTML = ''; // Effacer les résultats
    // Afficher le contact sélectionné
    const contactDiv = renderSelectedContact(user);
    selectedContactsContainer.appendChild(contactDiv);

    // Debugging: Afficher les contacts actuellement sélectionnés
    console.log('Contacts sélectionnés:', Array.from(selectedContactsContainer.children).map(contactDiv => contactDiv.querySelector('input[type="hidden"]').value));
}

 let selectedContacts = [];
    const MAX_DISPLAY = 2;

function renderSelectedContact(user) {
    selectedContacts.push(user);
    updateSelectedContactsDisplay();
}

function updateSelectedContactsDisplay() {
    const selectedContactsContainer = document.getElementById('selected-contacts');
    const moreContactsText = document.getElementById('more-contacts');
    const moreContactsSpan = document.getElementById('more-contacts-text');
    
    selectedContactsContainer.innerHTML = ''; // Vider le conteneur avant d'ajouter les contacts

    // Limiter l'affichage à MAX_DISPLAY contacts
    const contactsToDisplay = selectedContacts.slice(0, MAX_DISPLAY);
    contactsToDisplay.forEach((user, index) => {
        const contactDiv = createContactDiv(user, index);
        selectedContactsContainer.appendChild(contactDiv);
    });

    // Si le nombre de contacts dépasse la limite, afficher l'icône et le texte
    if (selectedContacts.length > MAX_DISPLAY) {
        moreContactsText.style.display = 'flex';
        moreContactsSpan.textContent = `+${selectedContacts.length - MAX_DISPLAY} contacts supplémentaires`;
    } else {
        moreContactsText.style.display = 'none';
    }

    // Afficher le séparateur s'il y a des contacts sélectionnés
    document.getElementById('separator_display').style.display = selectedContacts.length > 0 ? 'block' : 'none';
}

function createContactDiv(user, index) {
    const contactDiv = document.createElement('div');
    contactDiv.className = 'd-flex justify-content-between align-items-center mb-2';

    const userIconCol = document.createElement('div');
    const userIcon = document.createElement('i');
    userIcon.className = 'bi bi-person-circle';
    userIconCol.appendChild(userIcon);
    userIconCol.style.flex = '0';

    const nameCol = document.createElement('div');
    nameCol.innerHTML = `${user.nom} ${user.prenom} <span class="status-pending">En cours</span>`;
    nameCol.style.flex = '7';
    nameCol.style.textAlign = 'center';

    const deleteCol = document.createElement('div');
    const deleteIcon = document.createElement('i');
    deleteIcon.className = 'bi bi-trash';
    deleteIcon.style.cursor = 'pointer';

    // Ajouter un gestionnaire d'événements pour supprimer un contact spécifique
    deleteIcon.addEventListener('click', function() {
        // Retirer l'élément en fonction de son index dans selectedContacts
        selectedContacts.splice(index, 1);
        updateSelectedContactsDisplay(); // Mettre à jour l'affichage après suppression
    });

    deleteCol.appendChild(deleteIcon);
    deleteCol.style.flex = '1';

    contactDiv.appendChild(userIconCol);
    contactDiv.appendChild(nameCol);
    contactDiv.appendChild(deleteCol);

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'contact[]';
    hiddenInput.value = user.id;
    contactDiv.appendChild(hiddenInput);

    return contactDiv;
}

function showMoreContacts() {
    const selectedContactsContainer = document.getElementById('selected-contacts');
    selectedContactsContainer.innerHTML = ''; // Vider le conteneur

    // Afficher tous les contacts sélectionnés
    selectedContacts.forEach((user, index) => {
        const contactDiv = createContactDiv(user, index);
        selectedContactsContainer.appendChild(contactDiv);
    });

    // Masquer le texte "x contacts supplémentaires" après avoir tout affiché
    document.getElementById('more-contacts').style.display = 'none';
}

/*function renderSelectedContact(user) {
    const selectedContactsContainer = document.getElementById('selected-contacts');
    const contactDiv = document.createElement('div');
    contactDiv.className = 'd-flex justify-content-between align-items-center mb-2'; // Flexbox layout

    // Colonne pour l'icône de l'utilisateur
    const userIconCol = document.createElement('div');
    const userIcon = document.createElement('i');
    userIcon.className = 'bi bi-person-circle'; // Classe pour l'icône
    userIconCol.appendChild(userIcon);
    userIconCol.style.flex = '0'; // Prendre un espace égal

    // Colonne pour le nom et le prénom
    const nameCol = document.createElement('div');
    nameCol.innerHTML = `${user.nom} ${user.prenom} <span class="status-pending">En cours</span>`;
    //nameCol.textContent = `${user.nom} ${user.prenom} <span class="status-pending">En cours</span>`; // Afficher le prénom et le nom
    nameCol.style.flex = '7'; // Prendre plus d'espace
    nameCol.style.textAlign = 'center'; // Centrer le texte

    // Colonne pour l'icône de suppression
    const deleteCol = document.createElement('div');
    const deleteIcon = document.createElement('i');
    deleteIcon.className = 'bi bi-trash'; // Classe pour l'icône de suppression
    deleteIcon.style.cursor = 'pointer'; // Pour pointer comme un curseur

    // Ajouter un gestionnaire d'événements pour supprimer le contact
    deleteIcon.addEventListener('click', function() {
        selectedContactsContainer.removeChild(contactDiv); // Retirer le contact
        console.log(`${user.nom} ${user.prenom} a été supprimé.`);
    });

    deleteCol.appendChild(deleteIcon);
    deleteCol.style.flex = '1'; // Prendre un espace égal

    // Ajouter les colonnes au contact
    contactDiv.appendChild(userIconCol);
    contactDiv.appendChild(nameCol);
    contactDiv.appendChild(deleteCol); // Ajouter la colonne de suppression

    // Ajouter un champ input caché pour stocker l'ID de l'utilisateur
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'contact[]';
    hiddenInput.value = user.id;
    contactDiv.appendChild(hiddenInput); // Ajouter le champ caché ici
    document.getElementById('separator_display').style.display = 'block';
    
    return contactDiv; // Retourner le div pour l'ajouter au conteneur
}*/

function addContact(user) {
    const selectedContactsContainer = document.getElementById('selected-contacts');

    // Vérifier si le contact est déjà sélectionné
    const existingContact = Array.from(selectedContactsContainer.children).some(contactDiv => {
        const hiddenInput = contactDiv.querySelector('input[type="hidden"]');
        return hiddenInput && hiddenInput.value === user.id; // Comparer les IDs
    });

    /*if (existingContact) {
        alert(`${user.nom} ${user.prenom} est déjà sélectionné.`);
        return; // Sortir de la fonction si le contact est déjà ajouté
    }*/

    // Afficher le contact sélectionné
    const contactDiv = renderContact(user);
    selectedContactsContainer.appendChild(contactDiv);

    // Effacer le champ de recherche et cacher le conteneur de recherche
    document.getElementById('contact-search').value = '';
    document.getElementById('search-container').style.display = 'none';
    document.getElementById('contact-list').innerHTML = ''; // Effacer les résultats

    // Debugging: Afficher les contacts actuellement sélectionnés
    console.log('Contacts sélectionnés:', Array.from(selectedContactsContainer.children).map(contactDiv => contactDiv.querySelector('input[type="hidden"]').value));
}

function renderContact(user) {
    const selectedContactsContainer = document.getElementById('selected-contacts');
    const contactDiv = document.createElement('div');
    contactDiv.className = 'd-flex justify-content-between align-items-center mb-2'; // Flexbox layout

    // Colonne pour l'icône de l'utilisateur
    const userIconCol = document.createElement('div');
    const userIcon = document.createElement('i');
    userIcon.className = 'bi bi-person-circle'; // Classe pour l'icône
    userIconCol.appendChild(userIcon);
    userIconCol.style.flex = '0'; // Prendre un espace égal

    // Colonne pour le nom, prénom et statut
    const nameCol = document.createElement('div');
    // Afficher le prénom et le statut
    const statusInfo = getStatusText(parseInt(user.status, 10)); // Obtenir le texte et la classe du statut
   nameCol.innerHTML = `${user.nom} ${user.prenom} <span class="${statusInfo.class}">${statusInfo.text}</span>`;
    nameCol.style.flex = '7'; // Prendre plus d'espace
    nameCol.style.textAlign = 'center'; // Centrer le texte

    // Colonne pour l'icône de suppression
    const deleteCol = document.createElement('div');
    const deleteIcon = document.createElement('i');
    deleteIcon.className = 'bi bi-trash'; // Classe pour l'icône de suppression
    deleteIcon.style.cursor = 'pointer'; // Pour pointer comme un curseur

    // Ajouter un gestionnaire d'événements pour supprimer le contact
    deleteIcon.addEventListener('click', function() {
        selectedContactsContainer.removeChild(contactDiv); // Retirer le contact
        console.log(`${user.nom} ${user.prenom} a été supprimé.`);
    });

    deleteCol.appendChild(deleteIcon);
    deleteCol.style.flex = '1'; // Prendre un espace égal

    // Ajouter les colonnes au contact
    contactDiv.appendChild(userIconCol);
    contactDiv.appendChild(nameCol);
    contactDiv.appendChild(deleteCol); // Ajouter la colonne de suppression

    // Ajouter un champ input caché pour stocker l'ID de l'utilisateur
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'contact[]';
    hiddenInput.value = user.id;
    contactDiv.appendChild(hiddenInput); // Ajouter le champ caché ici

    return contactDiv; // Retourner le div pour l'ajouter au conteneur
}

function addContact1(user) {
    const selectedContactsContainer = document.getElementById('selected-contacts');

    // Vérifier si le contact est déjà sélectionné
    const existingContact = Array.from(selectedContactsContainer.children).some(contactDiv => {
        const hiddenInput = contactDiv.querySelector('input[type="hidden"]');
        return hiddenInput && hiddenInput.value === user.id; // Comparer les IDs
    });

    if (existingContact) {
        alert(`${user.nom} ${user.prenom} est déjà sélectionné.`);
        return; // Sortir de la fonction si le contact est déjà ajouté
    }

    // Afficher le contact sélectionné
    const contactDiv = renderContact1(user);
    selectedContactsContainer.appendChild(contactDiv);

    // Debugging: Afficher les contacts actuellement sélectionnés
    console.log('Contacts sélectionnés:', Array.from(selectedContactsContainer.children).map(contactDiv => contactDiv.querySelector('input[type="hidden"]').value));
}

function renderContact1(user) {
    const selectedContactsContainer = document.getElementById('selected-contacts');
    const contactDiv = document.createElement('div');
    contactDiv.className = 'd-flex justify-content-between align-items-center mb-2'; // Flexbox layout

    // Colonne pour l'icône de l'utilisateur
    const userIconCol = document.createElement('div');
    const userIcon = document.createElement('i');
    userIcon.className = 'bi bi-person-circle'; // Classe pour l'icône
    userIconCol.appendChild(userIcon);
    userIconCol.style.flex = '1'; // Prendre un espace égal

    // Colonne pour le nom, prénom
    const nameCol = document.createElement('div');
    // Afficher le prénom et le statut
    const statusInfo = getStatusText(parseInt(user.status, 10)); // Obtenir le texte et la classe du statut
   nameCol.innerHTML = `${user.nom} ${user.prenom}`;
    nameCol.style.flex = '10'; // Prendre plus d'espace
    nameCol.style.textAlign = 'center'; // Centrer le texte

    // Ajouter les colonnes au contact
    contactDiv.appendChild(userIconCol);
    contactDiv.appendChild(nameCol);
    
    // Ajouter un champ input caché pour stocker l'ID de l'utilisateur
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'contact[]';
    hiddenInput.value = user.id;
    contactDiv.appendChild(hiddenInput); // Ajouter le champ caché ici

    return contactDiv; // Retourner le div pour l'ajouter au conteneur
}

// Fonction pour obtenir le texte du statut
function getStatusText(status) {
    console.log('status :', status); // Débogage pour voir la valeur du statut
    switch (status) {
        case 0:
            return { text: 'Pending', class: 'status-pending' };
        case 1:
            return { text: 'Accepted', class: 'status-accepted' };
        case 2:
            return { text: 'Declined', class: 'status-declined' };
        default:
            return { text: 'Unknown', class: '' }; // Aucune classe pour "Unknown"
    }
}
