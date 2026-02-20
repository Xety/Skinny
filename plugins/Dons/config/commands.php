<?php
return [
    'Commands' => [
        'inventaire' => [
            'admin' => false,
            'params' => 0,
            'syntax' => 'Inventaire'
        ],
        'inventory' => [
            'admin' => false,
            'params' => 0,
            'syntax' => 'Inventory'
        ],
        'check' => [
            'admin' => false,
            'params' => 0,
            'syntax' => 'Check'
        ],
        'demande' => [
            'admin' => false,
            'params' => 0,
            'syntax' => 'Demande'
        ],
        'fait' => [
            'admin' => true,
            'params' => 2,
            'syntax' => 'Fait [Skin|Couleur] [@Membre]'
        ]
    ]
];
