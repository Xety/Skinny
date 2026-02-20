<?php
return [
    'Commands' => [
        // Commande pour générer un code de liaison
        'pallink' => [
            'params' => 0,
            'description' => 'Génère un code pour lier ton compte Discord à Palworld',
            'syntax' => 'pallink',
            'admin' => false,
            'developer' => false,
            'module' => 'Palworld'
        ],
        // Commande pour vérifier le statut de liaison
        'palstatus' => [
            'params' => 0,
            'description' => 'Vérifie si ton compte Discord est lié à Palworld',
            'syntax' => 'palstatus',
            'admin' => false,
            'developer' => false,
            'module' => 'Palworld'
        ],
        // Commande pour délier un compte
        'palunlink' => [
            'params' => 0,
            'description' => 'Supprime la liaison de ton compte Palworld',
            'syntax' => 'palunlink',
            'admin' => false,
            'developer' => false,
            'module' => 'Palworld'
        ],
    ]
];
