<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details.
 *
 * @package    local_oidcserver
 * @category   local
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2010 onwards Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

// Privacy.
$string['privacy:metadata'] = 'The OIDC Server Local plugin ne stocke pas de données.';

$string['addclient'] = 'Enregistrer un nouveau client';
$string['addscope'] = 'Ajouter un scope';
$string['allowdeny'] = 'Autoriser / Interdire';
$string['allowdenyorder'] = 'Ordre d\'application';
$string['altredirecturis'] = 'URIs de redirection alternatives';
$string['altredirecturis_help'] = 'Ces URIs sont celles qui peuvent être explicitées dans les requêtes d\'authentification. Le serveur doit refuser l\'accès si l\'URI explicite n\'est pas dans la liste (liste à virgules).';
$string['authcodes'] = 'Codes d\'autorisation';
$string['clientname'] = 'Nom';
$string['clients'] = 'Clients';
$string['configenabled'] = 'Actif';
$string['configenabled_desc'] = 'Active le fonnctionnement des points de terminaison';
$string['configencryptionkey'] = 'Clef publique';
$string['configencryptionkey_desc'] = 'Cette clef publique peut être échangée avec les tiers pour qu\'ils cryptent des message à notre intention.';
$string['configencryptionalgorithm'] = 'Algorithme de cryptage des clefs';
$string['configencryptionalgorithm_desc'] = 'RSA ou HMAC sont supportés.';
$string['configfeatures'] = 'Configuration du serveur OIDC';
$string['configforceopeningcors'] = 'Forcer l\'ouverture CORS';
$string['configforceopeningcors_desc'] = 'Si actif, la politique Access-Control-Allow-Origin sera relâchée pour toute la plate-forme. Les sites tiers pourront intégrer librement les URLs de moodle.';
$string['configgetconsent'] = 'Demander le consentement sur la transmission de données';
$string['configgetconsent_desc'] = 'Si activé, une étape supplémentaire dans la procédure d\'authentification demande une fois le consentement sur le tranport de données.';
$string['configprivatekey'] = 'Clef privée';
$string['configprivatekey_desc'] = 'La clef privée de cryptage interne. Ne doit pas être communiquée aux tiers.';
$string['configclientskeysize'] = 'Taille de la clef des clients';
$string['configclientskeysize_desc'] = 'Taille de la clef générée (13 par défaut)';
$string['consenthead'] = 'Transmission de données personnelles';
$string['consenthelp'] = 'Ceci est un message informatif relatif à vos données personnelles. En acceptant l\'authentification, vous tranmettrez vos données d\'identité suivantes au service applicatif : ';
$string['consenthelptail'] = 'Vous pouvez refuser de transmettre certaines données, mais votre profil sur le service sera incomplet. Si vous refusez votre consentement sur l\'ensemble des données, votre profil ne pourra être créé.';
$string['defaultlocalinfo'] = 'Info locale associée par défaut';
$string['defaultinfo'] = 'Info locale associée par défaut';
$string['denyallow'] = 'Interdire / Autoriser';
$string['description'] = 'Description';
$string['editclient'] = 'Modifier un client';
$string['editscope'] = 'Modifier un scope';
$string['generate'] = ' Générer :&nbsp;&nbsp;';
$string['iconsent'] = 'J\'accepte';
$string['identifier'] = 'Identifiant';
$string['isconfidential'] = 'Est privé';
$string['manageoidcserver'] = 'Gérer les entités OIDC';
$string['oidcadmin'] = 'Administration du serveur OIDC';
$string['pluginname'] = 'Serveur Oauth/Openid';
$string['redirecturi'] = 'Uri de retour Oauth';
$string['redirecturi_help'] = 'L\'URI vers laquelle l\'utilisateur sera renvoyé pour traiter les échanges Oauth.';
$string['scopes'] = 'Scopes';
$string['secret'] = 'Secret';
$string['singlelogouturi'] = 'URI de déconnexion';
$string['singlelogouturi_help'] = 'Si elle est indiquée, l\'URI du client capable d\'assurer une déconnexion de la session de l\'utilisateur.';
$string['userallow'] = 'Utilisateurs autorisés';
$string['userdeny'] = 'Utilisateurs non autorisés';

$string['userallow_help'] = "
Filters the local users allowed to be authorized for this client. One expression per line. Passes if at least one rule is matched. Pass all if empty.\n
If the rule is a single rexexp (no prefix, or REGEXP: prefix), will apply to username.\n
If the rule has MOODLESCRIPT: prefix, will be evaluated by a moodlescript engine, in the system context.
";
$string['userdeny_help'] = "
Filters the local users allowed to be authorized for this client. One expression per line. Blocks if at least one rule is matched. Blocks none if empty.\n
If the rule is a single rexexp (no prefix, or REGEXP: prefix), will apply to username.\n
If the rule has MOODLESCRIPT: prefix, will be evaluated by a moodlescript engine, in the system context.
";

$string['communityversionclients'] = 'La version publique du plugin autorise la connexion jusqu\'à 10 Oauth/OIDC.';

include(__DIR__.'/pro_additional_strings.php');