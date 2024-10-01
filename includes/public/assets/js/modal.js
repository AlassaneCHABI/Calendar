document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons de partage
    const shareButtons = document.querySelectorAll('button[data-share]');
    
    shareButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.stopPropagation(); 
            // Récupérer le lien de partage (attribut data-share)
            const shareLink = this.getAttribute('data-share');
            
            // Mettre à jour le lien dans le modal
            if(shareLink) {
                document.getElementById('copyLink').href = shareLink;
            }
            
            // Ouvrir le modal
            var modal = document.getElementById("myModal");
            modal.style.display = "block";

            const copyLinkButton = document.getElementById('copyLink');
    const copiedMessage = document.getElementById('copiedMessage');

    // Function to copy the link
    copyLinkButton.addEventListener('click', function (event) {
      event.preventDefault();

      // Use the Clipboard API to copy the link
      copyToClipboard(shareLink) 
        // Show "Link copied!" message
        copiedMessage.style.display = 'block';
        
        // Hide the message after 2 seconds
        setTimeout(() => {
          copiedMessage.style.display = 'none';
        }, 2000);
    });
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

function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      // Use the Clipboard API if available
      navigator.clipboard.writeText(text).then(() => {
        showCopiedMessage();
      }).catch((err) => {
        console.error('Error copying to clipboard: ', err);
      });
    } else {
      // Fallback method for unsupported browsers
      const textArea = document.createElement('textarea');
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      try {
        document.execCommand('copy');
      } catch (err) {
        console.error('Fallback: Could not copy text', err);
      }

      document.body.removeChild(textArea);
    }
  }