<?php
return [
    'Commands' => [
        'info' => [
            'admin' => true,
            'params' => 1,
            'syntax' => 'Info [Membre]'
        ],
        'tribu' => [
            'admin' => true,
            'params' => 0,
            'syntax' => 'tribu'
        ],
        'aideadmin' => [
            'admin' => true,
            'params' => 0,
            'syntax' => 'AideAdmin'
        ],
        'say' => [
            'admin' => true,
            'params' => 1,
            'syntax' => 'Say [Texte]'
        ]
    ]
];
