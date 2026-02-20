<?php
namespace Text\Module\Modules;

use Discord\Parts\Embed\Embed;
use Skinny\Core\Configure;
use Skinny\Module\ModuleInterface;
use Skinny\Network\Wrapper;

class Text implements ModuleInterface
{
    /**
     * {@inheritDoc}
     *
     * @param \Skinny\Network\Wrapper $wrapper The Wrapper instance.
     * @param array $message The message array.
     *
     * @return void
     */
    public function onCommandMessage(Wrapper $wrapper, $message): void
    {
        //Handle the command.
        switch ($message['command']) {
            case 'ip':
            case 'ips':
            case 'serveur':
            case 'serveurs':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━  IP Serveurs  ━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("**\n **```yaml\n- Maps Cluster \n\n- Aberration       - aberration.ark-division.fr:27060\n- Extinction       - extinction.ark-division.fr:27061\n- Ragnarok         -   ragnarok.ark-division.fr:27062\n- The Center       -     center.ark-division.fr:27063\n- The Island       -     island.ark-division.fr:27064\n- Valguero         -   valguero.ark-division.fr:27065\n- Genesis          -    genesis.ark-division.fr:27066\n- CrystalIsles     -    crystal.ark-division.fr:27067\n- Scorched Earth   -   scorched.ark-division.fr:27068\n- Genesis2         -   genesis2.ark-division.fr:27069\n- Lost Island      - lostisland.ark-division.fr:27070\n- Fjordur          -    fjordur.ark-division.fr:27072```\n***[━━━> Cluster Steam Collection <━━━](https://tinyurl.com/Cluster-Division \"Collection complete des Mods sur steam pour vous abonner en 1 clic\")***\n\n")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);

                break;

            case 'regles':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("Merci de bien vouloir lire ces channels :")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("<#466527538974556160>\n<#564069972956151818>\n")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);

                break;

            case 'infodon':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("Un don donne accès aux avantages suivants :")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("\nAu statut **<@&386617500516876289>** en vert sur Discord, qui permet de participer aux votes concernant les grandes décisions de nos serveurs, accéder au logiciel <#693371861307752468> qui vous permet de surveiller vos bases depuis n'importe où, ainsi que le statut **<@&431910257367973898>** donnant des droits prioritaires sur les bots musique durant une période de 6 mois.\n\nDe **modifier et personnaliser son channel de tribu** sur Discord.\nUne couleur dino toutes les **tranches de 10€**\nUn skin toutes les **tranches de 15€**\nUn skin des 3 statues : Manticore, Dragon et Gorille de taille moyenne toutes les **tranches de 20€**\n\n> Les couleurs ici : <https://ark.gamepedia.com/Color_IDs>\n> Les skins ici : <https://ark.gamepedia.com/Skins>\n\n> Pour faire un don : **[clique ici](https://ark-division.fr/faire-un-don/ \"Le lien de donation du site ARK Division.\")**")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);

                break;

            case 'discord':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:diamonds:  ━━━━  Est interdit sur le Discord DIVISION   ━━━━  :diamonds:***")
                    ->setColor(hexdec("FF0000"))
                    ->setDescription("\n```diff\n- Les recrutements sur d'autres serveurs que ARK Division\n- De flooder les channels\n- De faire de la publicité pour quoi que ce soit sans notre accord\n- De communiquer sur un autre serveur que ARK Division et sur un support autre que PC, nous ne sommes pas le discord officiel de wildcard, mais uniquement de nos serveurs privés PVE DIVISION
                    ```")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'aide':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━  Aide  ━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("**\n**Veuillez trouver dans le channel <#738230751748685844> toutes le commandes disponibles pour le bot.")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'mod':
            case 'mods':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━━  Liste des Mods du Cluster   ━━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("** **\n:small_orange_diamond: **[Awesome Spyglass](https://tinyurl.com/so35p5s \"Affiche les stats des dinos\")**\n:small_orange_diamond: **[Death Inventory Keeper](https://tinyurl.com/deathkeeper \"Permet de récupérer le contenu de son corps après être mort\")**\n:small_orange_diamond: **[Dino Tracker](https://tinyurl.com/dinotraker \"Permet de retrouver ses dinos égarés\")**\n:small_orange_diamond: **[Hulks Armor](https://tinyurl.com/armorhulks \"Mod ADMIN\")**\n:small_orange_diamond: **[Kraken's Better Dinos](https://tinyurl.com/krakenbetter \"Modifie les dinos\")**\n:small_orange_diamond: **[RP Decors Division](https://tinyurl.com/yashi-factory \"Mod de décoration par YASHI FACTORY\")**\n:small_orange_diamond: **[Simple Spawners](https://tinyurl.com/SimpleSpawner \"Permet de faire spawn des dinos HL\")**\n:small_orange_diamond: **[Structures Plus](https://tinyurl.com/StructuresPlus \"Mod de construction\")**\n:small_orange_diamond: **[Swim Clear Scuba Mask](https://tinyurl.com/scubamask \"Pour voir clair sous l’eau\")**\n:small_orange_diamond: **[TCs Auto Rewards](https://tinyurl.com/TCsVault \"Coffre aux trésors\")**\n:small_orange_diamond: **[TributTransfer](https://tinyurl.com/TributTransfer \"Autorise le transfert d'éléments et d'apex\")**\n:small_orange_diamond: **[Dino Storage V2](https://tinyurl.com/dinostoragev2 \"Dino storage manager\")**\n:small_orange_diamond: **[DS Cryopods Revomer](https://tinyurl.com/DSCryopodsRemover \"DS Cryopods Remover\")**\n:small_orange_diamond: **[ArkShopUI](https://tinyurl.com/ArkShopUI \"Permet l'affichage du ArkShop avec F1\")**\n:small_orange_diamond: **[Shop Points](https://tinyurl.com/ShopPoints \"Permet d'avoir des shop point via des items\")**\n\n\n***[━━━> Steam Collection <━━━](https://tinyurl.com/Mods-Division \"Collection complete des Mods sur steam pour vous abonner en 1 clic\")***")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter("Ce bot a été créé par @ZoRo pour ARK Division France\nMessage de @yaya070 pour ARK Division France");

                $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'rate':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━  Liste des configurations du Cluster  ━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription(" \n\n:small_orange_diamond: Expérience  **` X2.5 `**\n:small_orange_diamond: Taming **` X3 `**\n:small_orange_diamond: Consommation nourriture **` X1 `**\n:small_orange_diamond: Récolte des ressources **` X2.5 `**\n:small_orange_diamond: Niveau de difficulté **` 5 `**\n:small_orange_diamond: Niveau maximum des dinos sauvages **` 150 `**\n:small_orange_diamond: Limite de dinos par tribu **` 200 `**\n:small_orange_diamond: Qualité des loots (air drop et caves) **` X4 `**\n:small_orange_diamond: Gamma **` actif `** (taper gamma + chiffre dans la console ex: gamma 3)\n\n**Configuration serveurs pour *ARK Smart Breeding* :**\n\n:small_orange_diamond: Taming Speed Multiplier : **` 3 `**\n:small_orange_diamond: Mating Interval Multiplier : **` 0.5 `** (Genesis 2 : **` 0.75 `** )\n:small_orange_diamond: Egg Hatch Speed Multiplier : **` 9 `**\n:small_orange_diamond: Baby Mature Speed Multiplier : **` 7 `**")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'admin':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━  Descriptif des Administrateurs  ━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("**\n<@&652646216806432772>** : Admins ayant accès à l'administration en jeu.\n<@388710363861876737> - <@344756221070409729> - <@272217639823343617>\n\n**<@&652647274681335860>** : Admins ayant la gestion des serveurs.\n<@152432753801953280> - <@313384725618098177> - <@92596320333742080>\n\n**<@&652647814811222016>** : Admins ayant la gestion du site internet.\n<@320547035604582411> - <@92596320333742080>\n\n**<@&652645511949713439>** : Admins ayant la gestion du discord.\n<@313384725618098177> - <@92596320333742080>\n\n**<@&511657672987377685>** : Modérateurs pouvant vous aider mais qui ne disposent pas d'autorisation d'administration ingame.\n-\n")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'repro':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("***Voici notre guide sur la reproduction***\n> Dans ARK en général\n> Et avec le mutator du mod S+\n\n:small_orange_diamond:**[ARK La reproduction optimisée](https://tinyurl.com/ARKreproAMELIO \"Guide sur la reproduction.\")**:small_orange_diamond:\n\nMerci aux joueurs **Bartman** et **John Doe** pour la création de ce document")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'depop':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle('***:small_blue_diamond:  ━━━  Voici les Timers de Depop des Fondations  ━━━ :small_blue_diamond:***')
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("\n```yaml\n- Fonda isolée  - 12 Heures\n- Tatch         -  4 Jours\n- Bois          -  8 Jours\n- Argile        -  8 Jours\n- Pierre        - 12 Jours\n- Metal         - 16 Jours\n- Glass         - 16 Jours\n- Tek           - 20 Jours```")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'bug':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***  :small_blue_diamond:        Liste des problèmes récurrents et leurs solutions        :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("```css\n[ The UE4-ShooterGame Game has crashed and will close ]\n```\n- Si dans le texte de la fenêtre se trouvent les mots **\"Missing Map Report\"**, c'est que le jeu n'arrive pas à charger la Map lorsque tu essayes de t'y connecter. Vérifie que la Map soit bien téléchargée, sinon, désinstalle la, puis réinstalle la. *(tape -dlc pour plus d'infos)*\n- Si tu n'y trouves pas cette ligne, la raison peut être aléatoire et relancer le jeu suffira probablement à résoudre le souci. Si le problème est récurrent, essaye d'effectuer une réparation des fichiers du jeu ou une réinstallation du jeu.\n- Sinon le problème provient surement de ton PC, il faut approfondir sur le net pour trouver diverses solutions.\n\n```css\n[ Outgoing reliable buffer overflow ]\n```\n- Tu as trop d'engrammes appris, prends une potion d'oubli.\n- Tu as trop d'objets dans ton inventaire lors du transfert.\n\n```css\n[ Délai de connexion dépassé ]\n```\n- Temps de chargement trop long dû à la présence du jeu sur un HDD.\n- Mods en cours de téléchargement et/ou d'installation.\n\n```css\n[ Je ne vois pas les serveurs ! ]\n```\n- Vérifie que les adresses IP sont bien ajoutées dans tes favoris de steam (tape -favoris pour un tuto).\n - Vérifie le filtre des serveurs en jeu, règle le sur **FAVORIS**.\n- Vérifie que l'option \"afficher les serveurs avec mots de passe\" est décochée.\n - Vérifie que le jeu est à jour en relançant Steam plusieurs fois.\n\n```css\n[ Mon BattleEye me demande d'installer en boucle et refuse de lancer le jeu ]\n```\n- Va dans le dossier *.../SteamGame\steamapps\common\ARK\ShooterGame\Binaries\Win64\BattlEye* et ouvre le fichier ***BELauncher.ini***\n- Édite la valeur de *PrivacyBox* par *PrivacyBox=**0***\n- Faq BattleEye sur les problèmes : <https://www.battleye.com/support/faq/>")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);

                break;

            case 'lien':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━  Les liens DIVISION   ━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("*** ***\n>>> <:division:693196707592274030>   -  ***[ARK Division France](https://ark-division.fr/ \"Notre Site Internet\")***\n\n<:facebook:691311917465206836>   -  ***[FaceBook Division](https://www.facebook.com/arkdivision/ \"Le FaceBook Division\")***\n\n<:twitter:691311977485828196>   -  ***[Twitter Division](https://twitter.com/Ark_Division \"Le Twitter Division\")***\n\n<:YouTube:695201856854556722>   -  ***[Notre chaine Youtube](https://www.youtube.com/channel/UCoWnySfrqo-qFTh6n8nsCSA \"La chaine Division\")***\n\n<:discord:695209188212604928> - ***[https://discord.gg/tcud7UG](https://discord.gg/tcud7UG \"Lien d'invitation pour notre serveur Discord\")***\n\n<:paypal:691311957135065170>   -  ***[Nous soutenir via PayPal](https://donation.ark-division.fr/ \"Faire un don via Paypal\")***\n\n<:uTip:691312052849082388>   -  ***[Nous soutenir via uTip](https://utip.io/ArkDivision \"Faire un don via uTip\")***\n\n<:vote:722545454646296667>   -  ***[Votez DIVISION](https://tinyurl.com/vote-serveur \"Voter pour les serveurs Division\")***\n")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'favoris':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━  Ajout des Serveurs Division en favoris   ━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("Pour ajouter un serveur Division en favoris, veuillez cliquer sur l'image ci-dessous et suivre les étapes indiquées :")
                    ->setImage('https://i.imgur.com/cyornHs.jpg')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'new':
            case 'tuto':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("** :small_blue_diamond:  ━  Nouveau sur ARK ? Besoin d'un tutoriel sur ARK ?  ━  :small_blue_diamond:**")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("** **\n```yaml\n- Voici la liste de nos tutos :\n```\n:small_orange_diamond: **[Bien débuter sur ARK](https://ark-division.fr/reussir-ark-survival-10-etapes-cles/ \"Tuto pour bien débuter sur ARK en 10 étapes\")**\n\n:small_orange_diamond: **[Les astuces du S+](https://ark-division.fr/mod-structure-plus-s/ \"Tout savoir sur le mod Structure Plus\")**\n\n:small_orange_diamond: **[Tous sur la repro](https://tinyurl.com/ARKreproAMELIO \"Tuto sur la repro avec et sans le mutator\")**\n\n:small_orange_diamond: **[Mettre une IP en favoris](https://i.imgur.com/cyornHs.jpg \"Ajouter une IP dans les favoris steam\")**\n\n")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'cluster':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("** :small_blue_diamond:  ━━━━━━  Info Cluster  ━━━━━━  :small_blue_diamond:**")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("** **\n```yaml\nLes serveurs Division sont en -cluster-```\n__**C'est à dire :**__\n```yaml\nToutes les maps que nous proposons sont reliées entre elles, tu peux donc y voyager avec ton personnage, ton stuff, tes items, tes ressources et tes dinos\nC'est ce que l'on appelle le -TRANSFERT-```\n```yaml\nIl y a cependant quelques restrictions au niveau des -éléments- et des -apex- (griffes, serres, venin etc...) qui eux sont transférables via un mod```\n***Pour te transférer tu as le choix entre :***\n```css\n🔸 Les obelisques\n🔸 Les beacons\n🔸 Le transmitter TEK```")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter("Ce bot a été créé par @ZoRo pour ARK Division France.");

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'kibble':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━  Kibbles sur ARK   ━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("Voici la liste des Kibbles disponibles sur les serveurs ARK Division :")
                    ->setImage('https://i.imgur.com/tN9KQaC.jpg')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'arcbar':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━━  Les ARc Bars  ━━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("** **\n*Les **ARc Bars** vous permettent d'acheter des packs d'items divers et variés dans le **TCs Vault***\n\n__**Vous pouvez en looter dans :**__\n```css\n🔸 Les Artefacts sur toutes les Maps du cluster\n🔸 Les Boss une fois vaincus\n🔸 Rarement dans les Beacons blancs```\n__**Combien :**__\n```yaml\nSur les Boss\n    🔸The Island Broodmother    10    20    30\n    🔸The Island Megapithecus   20    40    60\n    🔸The Island Dragon         40    70   100\n    🔸The Center                50    80   110\n    🔸Ragnarok                  80   130   180\n    🔸Valguero                  50   100   150\n    🔸Scorched Earth            40    55    70\n    🔸Crystal Isles             40    55    70\n\nDans les Artefacts\n    🔸3 sur Aberration & Scorched\n    🔸2 sur Island, Ragnarok, Extinction & Valguero\n    🔸1 sur Center & Crystal Isles```  \n__**Comment les utiliser :**__\n```yaml\nTout simplement en les consommant depuis votre inventaire (sur n'importe quelle map, portefeuille global au cluster)```\n***Consulter tout les packs  sur le site Division  ***:   **https://ark-division.fr/packs-tcs/**")
                    ->setImage('https://i.imgur.com/m18sQGA.png')
                    ->setFooter("Ce bot a été créé par @ZoRo pour ARK Division France. \nMessage de @yaya070 pour ARK Division France.");

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'dlc':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━━  Installation des DLC de ARK   ━━  :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("Voici comment installer/désinstaller les DLC de ARK (Cocher/Décocher) :")
                    ->setImage('https://i.imgur.com/AmOVJ58.png?1')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;

            case 'versionmod':
            case 'bugsteam':
                $embed = new Embed($wrapper->Discord);

                $embed
                    ->setTitle("***:small_blue_diamond:  ━  Liste des problèmes en rapport avec Steam  ━ :small_blue_diamond:***")
                    ->setColor(hexdec("1DFCEA"))
                    ->setDescription("```css\n[ Verifiez que le serveur que vous tentez de rejoindre utilise la dernière version des Mods ]\n```\n- Sur Steam: aller dans **Steam/Paramètres/Téléchargement** puis \"Effacer le cache de téléchargement\"\n- Redémarrer Steam, voir même le PC.\n- Si aucune MAJ ne se lance, allez dans **Ark/Workshop/Parcourir/Articles abonnés**, à \"**Objets abonnés**\" à droite de l'écran, séléctionner \"**Date de mise à jour**\" puis **Se désabonner** puis **Se réabonner** au dernier mod mis à jour")
                    ->setImage('https://i.imgur.com/uTHSH6Q.png')
                    ->setFooter('Ce bot a été créé par @ZoRo pour ARK Division France');

                    $wrapper->Message->channel->sendEmbed($embed);
                break;
        }
    }
}
