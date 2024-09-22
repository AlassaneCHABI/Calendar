# Calendar

### Fonction JS très utiles
* populateEventList => affiche tous les evenements et les actions 

* renderCalendar => affiche le calendrier

### Structure des tables :

Table event :
    
    id : Identifiant unique de l'événement.
    title : Titre de l'événement.
    created_by : Référence à l'utilisateur qui a créé l'événement (correspond à id_user).

Table invitation :
    
    id : Identifiant unique de l'invitation.
    id_event : Référence à l'événement (id de la table event).
    id_guest : Référence à l'utilisateur invité (correspond à id_user).
    status : Statut de l'invitation (par exemple, "acceptée", "refusée").





Pour produire une sortie sous forme de tableau d'objets JavaScript comme celui que vous souhaitez, vous devrez d'abord effectuer une requête SQL qui récupère les événements, ainsi que leur statut et les informations sur les invités. Ensuite, vous pouvez structurer cette requête pour que le résultat soit transformé en tableau d'objets avec des propriétés comme `byMe`, `status`, et `n_invited` (nombre d'invités).

Voici un exemple de requête SQL et de transformation des résultats en JavaScript.

### 1. **Requête SQL pour récupérer les événements et invitations**

Vous devez d'abord récupérer les événements, leurs créateurs, et les invités, puis déterminer si l'utilisateur en question est le créateur (`byMe`) ou un invité (`byMe = false`). Vous pouvez modifier cette requête en fonction de votre base de données. Supposons que l'utilisateur connecté a un `id_user` donné (par exemple, `123`).

```sql
SELECT 
    e.id AS event_id,
    e.title AS event_title,
    e.created_by AS event_creator,
    u1.id_user AS invited_user,
    COUNT(i.id_guest) AS n_invited,  -- Nombre d'invités
    i.status AS invitation_status,
    (CASE 
        WHEN e.created_by = 123 THEN 1
        ELSE 0
    END) AS by_me -- Determine si c'est l'utilisateur connecté qui a créé l'événement
FROM 
    event e
LEFT JOIN 
    invitation i ON e.id = i.id_event
LEFT JOIN 
    user u1 ON i.id_guest = u1.id_user
WHERE 
    e.created_by = 123  -- Pour les événements créés par l'utilisateur connecté
OR 
    i.id_guest = 123    -- Pour les événements où l'utilisateur est invité
GROUP BY 
    e.id, i.status;
```

### 2. **Transformation en JavaScript**

Une fois que vous avez récupéré ces données de votre base de données, vous pouvez transformer le résultat en tableau d'objets JavaScript dans le format souhaité. Voici un exemple de script PHP (ou tout autre langage côté serveur) qui pourrait traiter les résultats et les formater en JSON pour JavaScript.

```php
<?php
// Exemple de données simulées à partir de la requête SQL
$result = [
    ['event_id' => 1, 'event_title' => 'Réunion d\'équipe', 'event_creator' => 123, 'n_invited' => 0, 'invitation_status' => 1, 'by_me' => 1, 'date' => '2024-09-20'],
    ['event_id' => 2, 'event_title' => 'Déjeuner avec client', 'event_creator' => 123, 'n_invited' => 0, 'invitation_status' => 1, 'by_me' => 1, 'date' => '2024-09-20'],
    ['event_id' => 3, 'event_title' => 'Séminaire', 'event_creator' => 456, 'n_invited' => 3, 'invitation_status' => 1, 'by_me' => 0, 'date' => '2024-09-20'],
];

// Initialiser un tableau pour stocker les événements
$events_by_date = [];

// Parcourir les résultats de la requête
foreach ($result as $row) {
    // Extraire la date de l'événement
    $date = $row['date'];
    
    // Si la date n'est pas encore dans le tableau, l'ajouter
    if (!isset($events_by_date[$date])) {
        $events_by_date[$date] = ['date' => $date, 'events' => []];
    }
    
    // Ajouter l'événement à la bonne date
    $events_by_date[$date]['events'][] = [
        'title' => $row['event_title'],
        'byMe' => $row['by_me'] == 1 ? true : false,
        'status' => $row['invitation_status'],
        'n_invited' => $row['n_invited'],
    ];
}

// Convertir le tableau PHP en JSON pour JavaScript
echo json_encode(array_values($events_by_date), JSON_PRETTY_PRINT);
```

### 3. **Sortie JavaScript**

Le résultat de ce script serait un tableau JavaScript qui ressemble à ceci :

```js
const events = [
    { 
        date: '2024-09-20',
        events: [
            { title: "Réunion d'équipe", byMe: true, status: 1, n_invited: 0 }, 
            { title: "Déjeuner avec client", byMe: true, status: 1, n_invited: 0 }, 
            { title: "Séminaire", byMe: false, status: 1, n_invited: 3 }
        ]
    }
];
```

### Explication du processus :
1. **Requête SQL** : 
   - Récupère les événements créés par l'utilisateur (`created_by = 123`) ou ceux auxquels l'utilisateur a été invité (`id_guest = 123`).
   - Utilise `COUNT(i.id_guest)` pour compter le nombre d'invités à chaque événement.
   - Utilise un `CASE` pour déterminer si l'utilisateur connecté est le créateur de l'événement (`by_me`).

2. **PHP / Transformation côté serveur** :
   - Regroupe les événements par date.
   - Pour chaque événement, il ajoute un objet avec les propriétés `title`, `byMe` (indiquant si l'utilisateur est le créateur), `status`, et `n_invited` (nombre d'invités).

3. **Sortie JSON** :
   - La structure de sortie est un tableau d'objets JavaScript avec des dates, contenant des sous-tableaux d'événements pour chaque jour.

### Résumé :
Avec cette approche, vous pouvez générer un tableau structuré pour les événements et leurs invités, avec des propriétés comme `byMe` pour indiquer si l'utilisateur connecté est le créateur de l'événement ou un invité. Le nombre d'invités (`n_invited`) est également inclus, ainsi que le statut de l'invitation (`status`).
