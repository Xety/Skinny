<?php
return [
    'language' => [
        // General
        'member_not_allowed' => 'Vous n\'avez jamais été membre, faite une donation pour le devenir ! Pour voir les avantages à devenir membre, utilisez la commande `-infodon`',
        'member_not_allowed_admin' => 'Cet utilisateur n\'a jamais été membre ! Pour voir les avantages à devenir membre, utilisez la commande `-infodon`',
        'your_inventory' => "\n>  Voici l'inventaire de votre compte :\n```yml\nCouleurs: %d couleurs\nSkins: %d skins\nRecompenses: %d récompenses\n```",
        'your_inventory_admin' => "\n>  Voici l'inventaire de %s :\n```yml\nCouleurs: %d couleurs\nSkins: %d skins\nRecompenses: %d récompenses\n```",
        'inventory_not_allowed' => 'Vous n\'êtes pas authorisé à utiliser cette commande pour un autre membre !',

        // Couleurs
        'no_more_color' => 'Vous n\'avez plus de couleur disponible.',
        'color_done_message' => 'Une couleur a bien été appliqué au membre <@%s> ! Il reste **%d** couleur(s) disponible(s) pour ce membre.',
        'color_done_error' => 'Ce membre n\'a plus de couleur disponible.',
        'asked_color_admin_message' => '%s **DEMANDE DE COULEUR**, <@%d> voudrait **une couleur** et vite !',
        'asked_color_reponse_message' => 'Votre demande a été prise en compte, ne quittez pas je cherche un admin... :smirk:',
        'interval_between_asking_color' => 'Vous avez fait une demande de couleur dans les dernières %s heures, merci de patienter avant de refaire une autre demande !',

        // Skins
        'no_more_skin' => 'Vous n\'avez plus de skin disponible.',
        'skin_done_message' => 'Un skin a bien été appliqué au membre <@%s> ! Il reste **%d** skin(s) disponible(s) pour ce membre.',
        'skin_done_error' => 'Ce membre n\'a plus de skin disponible.',
        'asked_skin_admin_message' => '%s **DEMANDE DE SKIN**, <@%d> voudrait **un skin** et vite !',
        'asked_skin_reponse_message' => 'Votre demande a été prise en compte, ne quittez pas je cherche un admin... :smirk:',
        'interval_between_asking_skin' => 'Vous avez fait une demande de skin dans les dernières %s heures, merci de patienter avant de refaire une autre demande !',
    ]
];
