jQuery(document).ready(function($) {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,weekGrid,dayGrid'
        },
        initialDate: new Date(),
        editable: true,
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: ccp_ajax_object.ajax_url, // URL Ajax localisé dans PHP
                method: 'POST',
                data: {
                    action: 'ccp_get_events' // Action pour récupérer les événements
                },
                success: function(response) {
                    if (response.success) {
                        successCallback(response.data); // Injecter les événements récupérés dans le calendrier
                        
                        // Appel de la fonction pour afficher les événements en dehors du calendrier
                        displayEventList(response.data);
                    } else {
                        failureCallback();
                    }
                },
                error: function() {
                    failureCallback();
                }
            });
        }
    });

    calendar.render(); // Initialiser et afficher le calendrier

    // Ajouter un gestionnaire d'événements pour le formulaire d'ajout d'événements
    $('#ccp-add-event-form').on('submit', function(e) {
        e.preventDefault();

        let eventData = {
            action: 'ccp_add_event',
            event_title: $('#event_title').val(),
            event_date: $('#event_date').val(),
            nonce: ccp_ajax_object.ccp_nonce
        };

        $.post(ccp_ajax_object.ajax_url, eventData, function(response) {
            if (response.success) {
                $('#ccp-message').html('<p>' + response.data + '</p>');
                $('#ccp-add-event-form')[0].reset();
                
                // Recharger les événements après ajout
                calendar.refetchEvents(); // Utiliser refetchEvents pour recharger les événements
            } else {
                $('#ccp-message').html('<p style="color: red;">' + response.data + '</p>');
            }
        });
    });

    // Fonction pour afficher les événements en dehors du calendrier
    function displayEventList(events) {
        var eventListEl = $('#event-list');
        eventListEl.empty(); // Vider la liste avant de la remplir

        if (events.length > 0) {
            events.forEach(function(event) {
                eventListEl.append('<p><strong>' + event.title + '</strong>: ' + event.start + '</p>');
            });
        } else {
            eventListEl.append('<p>Aucun événement disponible.</p>');
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('Document loaded'); // Déboguer pour s'assurer que le script est exécuté

    flatpickr("#date_time", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        mode: "multiple"
    });

    // Afficher le champ de recherche au clic
    document.getElementById('contact').addEventListener('click', function() {
        console.log('Contact field clicked'); // Déboguer pour s'assurer que le clic est détecté
        document.getElementById('search-container').style.display = 'block';
    });

    // Filtrer les contacts lors de la saisie dans le champ de recherche
    document.getElementById('contact-search').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let options = document.querySelectorAll('#contact-search-results option');
        options.forEach(function(option) {
            if (option.textContent.toLowerCase().includes(filter)) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });

    // Ajouter le contact sélectionné
    document.getElementById('contact-search-results').addEventListener('change', function() {
        let selectedContact = this.options[this.selectedIndex];
        if (selectedContact) {
            let contactList = document.getElementById('selected-contacts');
            let contact = document.createElement('div');
            contact.className = 'selected-contact';
            contact.textContent = selectedContact.textContent;
            contact.dataset.id = selectedContact.value;
            contactList.appendChild(contact);
        }
        document.getElementById('contact-search').value = '';
        document.getElementById('search-container').style.display = 'none';
    });
});



