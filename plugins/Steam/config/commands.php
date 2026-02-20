<?php
return [
    'Commands' => [
        'steamban' => [
            'admin' => true,
            'params' => 3,
            'syntax' => 'SteamBan [SteamID] [Durée (Heures) (0 Perm)] [Raison]'
        ],
        'steamcheck' => [
            'admin' => true,
            'params' => 1,
            'syntax' => 'SteamCheck [SteamID]'
        ]
    ]
];
