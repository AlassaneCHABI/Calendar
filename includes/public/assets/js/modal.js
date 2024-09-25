document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons de partage
    const shareButtons = document.querySelectorAll('button[data-share]');
    
    shareButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.stopPropagation(); 
            // Récupérer le lien de partage (attribut data-share)
            const shareLink = this.getAttribute('data-share');
            
            // Mettre à jour le lien dans le modal
            document.getElementById('event-share-link').href = shareLink;
            document.getElementById('event-share-link').textContent = shareLink;

            // Ouvrir le modal
            var modal = document.getElementById("myModal");
            modal.style.display = "block";
        });
    });

// Get the modal
var modal = document.getElementById("myModal");

// Get the <span> element that closes the modal
var closeSpan = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
closeSpan.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
});