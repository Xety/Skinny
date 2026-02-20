<?php
use Skinny\Core\Env;

return [
    /**
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => Env::getBool('APP_DEBUG', true),

    /**
     *  Option used by the dev bot.
     */
    'dev' => Env::getBool('APP_DEV', true),

    /**
     * Configure basic information about the application.
     *
     * - namespace - The namespace to find app classes under.
     */
    'App' => [
        'namespace' => 'Skinny',
        'paths' => [
            'plugins' => [ROOT . DS . 'plugins' . DS]
        ],
        'site' => Env::getString('APP_SITE', 'https://discuss.ark-division.fr'),
    ],

    /**
     * Configure the Bot.
     *
     * - token - The Discord authentication token.
     */
    'Bot' => [
        'token' => Env::getRequired('BOT_TOKEN'),
        'game' => Env::getString('BOT_GAME', 'https://ark-division.fr'),
        'status' => Env::getString('BOT_STATUS', 'online'),
        'username' => Env::getString('BOT_USERNAME', 'Skinny'),
        'avatar' => Env::getString('BOT_AVATAR', 'https://i.imgur.com/f9L2InC.png')
    ],

    /**
     * API
     *
     * - url - The url used to request the API.
     * - token - The API authentication token.
     */
    'API' => [
        'url' => Env::getRequired('API_URL'),
        'token' => Env::getRequired('API_TOKEN'),
    ],

    /**
     * Configure basic information about the Discord.
     *
     * - guild : The guild ID.
     * - developers : The list of bot's developers. Either by User ID or by Role ID. (IDs only)
     * - admins : The list of bot's administrators. Either by User ID or by Role ID. (IDs only)
     * - member :
     *      - roles : The roles applied to an user that just donated € to ARK Division.
     * - chatChannels : Restrict the bot to only listen to certain text channels. (IDs only)
     * - voiceChannel : Join a voice channel on startup. (IDs only)
     */
    'Discord' => [
        'guild' => Env::getString('DISCORD_GUILD_ID', '386615163165605889'), // ARK Division
        'developers' => [
            '92596320333742080', // ZoRo
            '313384725618098177', // Nicosllife
            '149850524294840321', //Yaya
        ],
        'admins' => [
            '386617196258000896', // Admin
            '511657672987377685' // Ambassadeur
        ],
        'member' => [
            'roles' => [
                '386617500516876289', // Membres
                '431910257367973898' // DJ'
            ],
        ],
        'roles' => [
            'valider' => '564064249303924777', // Validé,
        ],
        'channels' => [
            'welcome' => '537259506904727552',
            'logs-bot' => '607226558645796864',
            'annonces' => '386898828109938688',
            'click-here-dev' => '653094981707497473',
            'admins' => [
                '631999661112033280' // test-cmd-bot
            ],
            'arklog' => [
                '693371861307752468', // arklog
                '631999661112033280' // test-cmd-bot
            ],
            // Channels Palworld (webhooks de chat in-game)
            'palworld' => [
                '1465675674697338900' // chat-ingame
            ],
            // Channels avec modération automatique
            'moderated' => [
                'support-admin' => ['735592989551886407'], // support-admin
                'reglement' => ['564069972956151818'], // reglement-discord
            ],
            'blacklisted' => [
                '654359922791809054', // chat-serveurs
                '663518505538158633', // Aberration
                '663518644818411595', // The Center
                '717723485195075604', // Crystal Isles
                '663518734710734849', // Extinction
                '986693058823335936', // Fjordur
                '701843656675426364', // Genesis
                '863169410326986784', // Genesis2
                '663518665060384768', // The Island
                '920363412343640076', // LostIsland
                '663518687952633877', // Ragnarock
                '663518770681348113', // Scorched Earth
                '663518799370256394', // Valguero
                '724322424941969438', // Epic Game Store
                '724322704798253157', // PlayStation
                '724324775610286121', // Windows Store
            ]
        ],
        'click-here' => [
            'dev' => '653094981707497473',
            'parent-dev' => '634190444326158338'
        ],
        'ticket' => [
            'channels' => [
                '631999661112033280', // test-cmd-bot
                '735592989551886407' // support-admin
            ],
            'category' => '651956310761406464', // support
            'prefix' => 'ticket-dev-',
            'admins' => [
                '386617196258000896', // Admin
                '511657672987377685' // Ambassadeur
            ],
            'ticket_limit' => 1, // Ticket limit per user
            'reactions' => [
                'open' => '📩', // Reaction used to open a ticket
                'close' => '🔒' // Reaction used to close a ticket
            ],
            'ticket_embed' => [
                'message_text' => "Bonjour %s ! \nUn <@&652646216806432772> sera bientôt là.",
                'message_text_dev' => "(VERSION DEV) Bonjour %s ! \nUn Admin sera bientôt là.",
                'embed_text' => "- **Veuillez expliquer au mieux votre problème** avec des coordonnées, screenshots *(si possible)*, nom de tribu etc, afin qu'un admin puisse traiter votre problème le plus rapidement possible.\n- **Evitez les messages** du genre \"J'ai un problème pouvez-vous m'aider svp ?\" sans quoi votre ticket sera automatiquement fermé.\n\n**- Remboursables : stuff et selles sur BP, items communs.\n- Non-remboursables : créatures, BPs, items avancés (forge industrielle, mutator, fusil capé sans BP...).\n\n** **Merci d'être patient**, les Admins ne sont pas toujours disponible et **ne sont pas** à votre disposition."
            ],
            'message_dm' => "Bonjour,\nVotre Ticket N°`%s` vient d'être fermé pour l'une des raisons suivantes:\n- Votre problème a été résolu.\n- Votre demande est considérée comme inappropriée pour l'usage des tickets, merci d'utiliser le channel #support pour avoir de l'aide de la communauté."
        ],

        //Set the expiration delay used for members.
        'expire' => '+6 months',

        //Discord API configuration.
        'api' => [
            'webhook' => Env::getString('DISCORD_WEBHOOK_URL', ''),
            'base_url' => "https://discordapp.com/api/v10",
        ],
        'commands' => [
            'channel_id' => '738230751748685844',
            'message_id_everyone' => '820128464391634964',
            'message_id_membre' => '820130126665875476',
        ]
    ],

    /**
     *  Configure the donations part.
     *
     *  - interval_between_asking_color - The interval between two asked colors to avoid spam.
     *  - interval_between_asking_skin - The interval between two asked skins to avoid spam.
     */
    'Dons' => [
        'interval_between_asking_color' => Env::getString('DONS_INTERVAL_COLOR', '48'),
        'interval_between_asking_skin' => Env::getString('DONS_INTERVAL_SKIN', '48')
    ],

    /**
     * Configure Module manager.
     *
     * - priority - All modules that need to be loaded before others.
     */
    'Modules' => [
        'priority' => []
    ],

    /**
     * Configure basic information about the the commands.
     *
     * - prefix - Prefix used with command.
     */
    'Command' => [
        'prefix' => Env::getString('COMMAND_PREFIX', '!')
    ]
];
