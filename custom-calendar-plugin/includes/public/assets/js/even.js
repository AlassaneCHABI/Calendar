// Function to create and show the modal
function openModal_add_even() {
    console.log("Bouton cliqué");
    // Create the modal HTML structure
    const modalHTML = `
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title w-100 text-center position-relative"  >Ajouter un événement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
            <form id="add-event-form">
            <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">
            <input type="text" id="title" name="title" required placeholder="Titre">
            
            <div class="row mb-3">
            
                <div class="col-md-8">
                    <input type="date" id="event_date" name="start_date" required>
                </div>
                
                <div class="col-md-4">

                    <input type="time" id="event_time" name="start_time" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="date" id="event_date" name="end_date" required>
                </div>
                <div class="col-md-4">
                    <input type="time" id="event_time" name="end_time" required>
                </div>
            </div>

             <div class="mb-12">
                <div class="input-group">
                    <input type="text" id="location" name="location" placeholder="Lieu" onclick="showMap()" readonly>
                </div>
            </div>
             
          <div class="mb-12">
                    <div class="input-group">
                        <input type="text" id="address-search" name="address" placeholder="Rechercher une adresse" oninput="searchAddress()" style="display: none;" >
                    </div>
                    <div id="address-results" ></div>
                </div>
            <div id="map" style="height: 300px; display: none;"></div>
             
        <div id="contact-container">
                <input type="text" id="contact" name="contact[]" readonly placeholder="Contact" onclick="toggleSearchContainer()">
                <div id="search-container" style="display: none;">
                    <input type="text" id="contact-search" placeholder="Rechercher un contact...">
                    <div id="contact-search-results"></div>
                </div>
            </div>

            <div id="selected-contacts"></div>
            <textarea id="description" name="description" placeholder="Ajouter une description"></textarea>
            <input type="text" id="remember" name="remember" placeholder="Alerte">
            <input type="text" id="link" name="link" placeholder="Ajouter un lien">
            <input type="file" id="file" name="file" placeholder="Ajouter un fichier">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="submit" value="Valider">
        </form>

      </div>
    </div>`;

    // Append the modal to the body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Initialize and show the modal
    var myModal = new bootstrap.Modal(document.getElementById('addEventModal'), {
        keyboard: false
    });
    myModal.show();

    // Clean up the modal from the DOM after it is closed
    document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function (event) {
        document.getElementById('addEventModal').remove();
    });

    // Add event listener for the "Enregistrer" button
    document.getElementById('submit-form').addEventListener('click', function() {
        document.getElementById('add-event-form').submit();
    });
}

// Function to show even the modal
function openModal_show_even_by_me() {
    console.log("Bouton cliqué");
    // Create the modal HTML structure
    const modalHTML = `
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title w-100 text-center position-relative"  >Détails de votre événement</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
            <form id="add-event-form">
            
            <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">
            <input type="text" id="title" name="title" required placeholder="Titre">

            <div class="row mb-3">
            
                <div class="col-md-8">
                    <input type="date" id="event_date" name="start_date" required>
                </div>
                
                <div class="col-md-4">

                    <input type="time" id="event_time" name="start_time" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="date" id="event_date" name="end_date" required>
                </div>
                <div class="col-md-4">
                    <input type="time" id="event_time" name="end_time" required>
                </div>
            </div>
             <div class="mb-12">
                <div class="input-group">
                    <input type="text" id="location" name="location" placeholder="Lieu" onclick="showMap()" readonly>
                </div>
            </div>
             
          <div class="mb-12">
                    <div class="input-group">
                        <input type="text" id="address-search" name="address" placeholder="Rechercher une adresse" oninput="searchAddress()" style="display: none;" >
                    </div>
                    <div id="address-results" ></div>
                </div>
            <div id="map" style="height: 300px; display: none;"></div>
             
        <div id="contact-container">
                <input type="text" id="contact" name="contact[]" readonly placeholder="Contact" onclick="toggleSearchContainer()">
                <div id="search-container" style="display: none;">
                    <input type="text" id="contact-search" placeholder="Rechercher un contact...">
                    <div id="contact-search-results"></div>
                </div>
            </div>

            <div id="selected-contacts"></div>
            <textarea id="description" name="description" placeholder="Ajouter une description"></textarea>
            <input type="text" id="remember" name="remember" placeholder="Alerte">
            <input type="text" id="link" name="link" placeholder="Ajouter un lien">
            <input type="file" id="file" name="file" placeholder="Ajouter un fichier">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="submit" value="Valider">
        </form>

      </div>
    </div>`;

    // Append the modal to the body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Initialize and show the modal
    var myModal = new bootstrap.Modal(document.getElementById('addEventModal'), {
        keyboard: false
    });
    myModal.show();

    // Clean up the modal from the DOM after it is closed
    document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function (event) {
        document.getElementById('addEventModal').remove();
    });

    // Add event listener for the "Enregistrer" button
    document.getElementById('submit-form').addEventListener('click', function() {
        document.getElementById('add-event-form').submit();
    });
}

// Function to show even the modal
function openModal_show_even() {
    console.log("Bouton cliqué");
    // Create the modal HTML structure
    const modalHTML = `
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title w-100 text-center position-relative"  >Détails de l'invitation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
            <form id="add-event-form">
            <label>Statut</label>
            <select class="form-control" name="isFee">
                          <option value="0">Pending</option>
                          <option value="1">Accepted</option>
                          <option value="1">Declined</option>
            </select>
            <input type="text" id="link_post" name="link_post" required placeholder="Lien du post">
            <input type="text" id="title" name="title" required placeholder="Titre">

            <div class="row mb-3">
            
                <div class="col-md-8">
                    <input type="date" id="event_date" name="start_date" required>
                </div>
                
                <div class="col-md-4">

                    <input type="time" id="event_time" name="start_time" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="date" id="event_date" name="end_date" required>
                </div>
                <div class="col-md-4">
                    <input type="time" id="event_time" name="end_time" required>
                </div>
            </div>

             <div class="mb-12">
                <div class="input-group">
                    <input type="text" id="location" name="location" placeholder="Lieu" onclick="showMap()" readonly>
                </div>
            </div>
             
          <div class="mb-12">
                    <div class="input-group">
                        <input type="text" id="address-search" name="address" placeholder="Rechercher une adresse" oninput="searchAddress()" style="display: none;" >
                    </div>
                    <div id="address-results" ></div>
                </div>
            <div id="map" style="height: 300px; display: none;"></div>
             
        <div id="contact-container">
                <input type="text" id="contact" name="contact[]" readonly placeholder="Contact" onclick="toggleSearchContainer()">
                <div id="search-container" style="display: none;">
                    <input type="text" id="contact-search" placeholder="Rechercher un contact...">
                    <div id="contact-search-results"></div>
                </div>
            </div>

            <div id="selected-contacts"></div>
            <textarea id="description" name="description" placeholder="Ajouter une description"></textarea>
            <input type="text" id="remember" name="remember" placeholder="Alerte">
            <input type="text" id="link" name="link" placeholder="Ajouter un lien">
            <input type="file" id="file" name="file" placeholder="Ajouter un fichier">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="submit" value="Valider">
        </form>

      </div>
    </div>`;

    // Append the modal to the body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Initialize and show the modal
    var myModal = new bootstrap.Modal(document.getElementById('addEventModal'), {
        keyboard: false
    });
    myModal.show();

    // Clean up the modal from the DOM after it is closed
    document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function (event) {
        document.getElementById('addEventModal').remove();
    });

    // Add event listener for the "Enregistrer" button
    document.getElementById('submit-form').addEventListener('click', function() {
        document.getElementById('add-event-form').submit();
    });
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
