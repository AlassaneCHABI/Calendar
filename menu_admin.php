<?php
/**
 * Plugin Name: Mon Plugin Personnalisé
 * Description: Un plugin simple qui ajoute un menu personnalisé au tableau de bord WordPress.
 * Version: 1.0
 * Author: Votre Nom
 */

// Fonction pour ajouter le menu au tableau de bord
function ajouter_menu_calendar() {
    // Ajouter un élément de menu principal
    add_menu_page(
        'Titre du Menu',        // Titre de la page
        'Calendar Pro',    // Texte du menu
        'manage_options',       // Capacité (seuls les administrateurs peuvent y accéder)
        'mon-plugin-slug',      // Slug (identifiant) de la page
        'afficher_page_menu',   // Fonction qui affiche le contenu de la page
        'dashicons-calendar', // Icône (utiliser les icônes Dashicons ou une URL d'image)
        6                        // Position dans le menu (6 place après "Articles")
    );

     
    // add_submenu_page(
    //     'mon-plugin-slug',      // Slug du parent (l'élément de menu principal)
    //     'Sous-menu',            // Titre de la page du sous-menu
    //     'Sous-menu',            // Texte du sous-menu
    //     'manage_options',       // Capacité requise
    //     'mon-sousmenu-slug',    // Slug du sous-menu
    //     'afficher_page_sousmenu' // Fonction qui affiche le contenu du sous-menu
    // );
}

// Fonction qui affiche le contenu de la page principale
function afficher_page_menu() {
    ?>
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation d'utilisation de Calendar Pro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            color: #555;
            border-bottom: 2px solid #00aaff;
            padding-bottom: 5px;
        }

        p {
            line-height: 1.6;
            color: #666;
        }

        code {
            background-color: #f0f0f0;
            padding: 2px 6px;
            border-radius: 4px;
            color: #d63384;
            font-weight: bold;
        }

        pre {
            background-color: #272822;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
 

        .note {
            background-color: #fffae6;
            border-left: 4px solid #ffcc00;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            color: #555;
        }

        ul {
            margin: 20px 0;
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 10px;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Documentation d'utilisation du plugin "Calendar Pro"</h1>
        
        <h2>Introduction</h2>
        <p>Ce plugin vous permet d'intégrer un calendrier pour gérer des évenements en utilisant un <strong>shortcode</strong>. Suivez ce guide pour apprendre à utiliser le plugin efficacement.</p>

        <h2>Installation</h2>
        <p>Après avoir téléchargé et activé le plugin dans votre tableau de bord WordPress, vous pouvez immédiatement commencer à l'utiliser.</p>

        <h2>Utilisation du shortcode</h2>

        <ol>
            <li><b>Show Calendar</b>
  
            
        <p>Pour utiliser le plugin, ajoutez simplement le shortcode suivant dans vos  pages :</p>
        
        <pre><code>[show_calendar]</code></pre>
        
         </li>
            <li><b>Bouton Calendar</b>
       
        <pre><code>[bouton_calendar texte="Calendrier" url=""]   </code></pre>

        <h3>Attributs disponibles</h3>
        <ul>
            <li><strong>texte</strong> (facultatif) : titre que portera votre bouton</li>
            <li><strong>url</strong> (facultatif) :la page du calendrier. Une page par défaut est faite</li>
        </ul>
</li> </ol>
        <h3>Exemple d'utilisation </h3>

        <ol>
            <li> <b>Show Calendar</b>
                <p>Vous le mettez n'importe où dans une page</p>

                 <pre><code>[show_calendar]</code></pre>
            </li>
            <li> <b>Bouton Calendar</b>
                <p>
                    <ul>
                        <li>Disponser d'une page par exemple la page /programme</li>
                        <li>Ajouter ensuite le lien de la page dans le shortcode comme suite :

                        <pre><code>[bouton_calendar texte="Calendrier" url="/programme"] </code></pre>
                        </li>
                    </ul>
                </p>
 
            </li>
        </ol>
        

        <h2>Notes supplémentaires</h2>
        <div class="note">
            <p><strong>Note :</strong> Assurez-vous que votre thème WordPress supporte bien les shortcodes dans les zones où vous les utilisez. En cas de problème, vérifiez si le shortcode est désactivé dans certains blocs de page ou utilisez un éditeur de texte classique.</p>
        </div>

        <h2>FAQ</h2>
        <ul>
            <li><strong>Comment personnaliser le style du plugin ?</strong> Vous pouvez ajouter des styles personnalisés en modifiant le fichier CSS de votre thème ou en utilisant un plugin de customisation CSS.</li>
            <li><strong>Le plugin ne fonctionne pas correctement, que faire ?</strong> Assurez-vous que votre site est à jour (WordPress et tous les plugins). Si le problème persiste, contactez le support technique.</li>
        </ul>

        <footer>
            <p>© 2024 Calendar Pro. Tous droits réservés.</p>
        </footer>
    </div>

</body>
</html>

    <?php
}

// Fonction qui affiche le contenu du sous-menu
function afficher_page_sousmenu() {
    ?>
    <!-- <div class="wrap">
        <h1>Page de Sous-menu</h1>
        <p>Ceci est le contenu du sous-menu.</p>
    </div> -->
    <?php
}

// Accrocher la fonction à l'admin_menu
add_action('admin_menu', 'ajouter_menu_calendar');

