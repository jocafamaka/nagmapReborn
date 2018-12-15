<?php 	// File that defines language variables.
/*
 * ##################################################################
 * #             ALL CREDITS FOR MODIFICATIONS ARE HERE             #
 * ##################################################################
 *
 * KEEP THE PATTERN
 *
 * Original Credits: xvx45 (https://github.com/xvx45) in 31 October 2018
 *
 */

//Errors:
$var_cfg_error = ("n'est pas correctement configuré, vérifiez le fichier de configuration de NagMap Reborn et faites les corrections nécessaires! Valeur définie: ");

$moduleError = ("Un(e) module/extension PHP essentielle au fonctionnement de NagMap Reborn n'a pass été trouvé, veuillez installer le(l') module/extension en premier lieu. Nom du Module/Extension: ");

$file_not_find_error = ("n'existe pas! Merci de définir la variable dans le fichier de configuration de NagMap Reborn!\n");

$in_definition_error =("Commencer un nouvelle in_efinition avant de fermer la précédente! Ce n'est pas cool.");

$no_data_error = ("Il n'y a pas de données à afficher. Vous n'avez pas configuré correctement NagMap Reborn où il s'agit d'un bug logiciel.<br>Veuillez contacter joao_carlos.r@hotmail.com pour assistance.");

$reported = (" signalé.");

$errorFound = ("Une erreur a automatiquement été reportée.");

$reporterErrorPre =("Une erreur est survenue mais ne peut être reportée!");

$reporterError =("Cette version de NagMap Reborn n'est plus supportée. Merci d'utiliser <a href='https://github.com/jocafamaka/nagmapReborn/releases'>la dernière version disponible</a>.");

$reporterErrorOF =("Impossible de signaler un bogue car un ou plusieurs fichiers de projet majeurs ont été modifiés!");

$emptyUserPass = ("Le nom d'utilisateur et/ou le mot de passe n'ont pas été définis, définissez les dans le fichier de configuration.");

$updateError = ("Un problème est survenu lors de la mise à jour du statut des hôtes. Les statuts affichés peuvent être obsolètes. Consultez la console pour plus d'informations.");

$updateErrorServ = ("Ce type d'erreur est généralement lié au problème suivant: Le serveur est inaccessible ou il y à trop de rebonds, vérifiez votre serveur et sa connexion.");

$updateErrorStatus = ("Ce type d'erreur est généralement lié au problème suivant: Le fichier d'état est inaccessible ou n'existe pas. Vérifiez que le service de supervision s'exécute correctement.");

$updateErrorChanges = ("Ce type d'erreur est généralement lié au problème suivant: Des modifications de l'hôte, l'ajout, la suppression ou la modification de noms ont eu lieu. Dans ce cas, mettez à jour la page.");

$updateErrorSolved = ("Problème résolu, les statuts affichés le sont en temps réel.");

//Debug info:
$message = ("Message:");

$lineNum = ("Numéro de ligne:");

$error = ("Erreur");

$at = ("A:");

//Bubble info:
$alias = ("Alias");

$hostG = ("Groupes d\'hôtes");

$addr = ("Adresse");

$other = ("Autre");

$hostP = ("Parents");

$newVersion = ("Mise à jour disponible");

$newVersionText = ("<br>La version actuelle de NagMap Reborn est obsolète!<br><br>Téléchargez la dernière version sur GitHub:<br><br>");

$passAlertTitle = ("Authentification par défaut");

$passAlert = ("Actuellement, vous utilisez le mot de passe et le nom d'utilisateur par défaut, protégez vous, modifiez-les maintenant!");

$asFilter = ("Utiliser comme filtre");

//ChangesBar warnings:
$up = ("EN LIGNE");

$down = ("HORS LIGNE");

$warning = ("ATTENTION");

$unknown = ("INCONNU");

$critical = ("CRITIQUE");

$and = ("et");

$waiting = ("En attente");

$timePrefix = ('Depuis ');

$timeSuffix = ('');

$filter = ("Filtrer");

$clear = ("Effacer");

//Debug page
$debugTitle = ("Informations de débogage");

$updating = ("Mise à jour");

$mainPage = ("Accueil");

$project = ("Projet sur GitHub");

$btop = ("Haut de la page");

$starting = ("Démarrage, patientez.");

$stopped = ("Stoppé");

$downData = ("Téléchargement des données");

$ignHosts = ("Hôtes ignorés (statique)");

$statusFile = ("Informations sur le fichier d'état (dynamique)");

$hostName = ("Nom d'hôte");

$reasons = ("Raison(s)");

$tServ = ("Le service ");

$tHost = ("L'hôte ");

$cs = ("Etat actuel");

$lhs = ("Dernier état hard");

$lsc = ("Dernier changement d'état");

$lhsc = ("Dernier changement d'état hard");

$ltup = ("Dernière fois en ligne");

$ltd = ("Dernière fois hors ligne");

$ltun = ("Dernière fois inaccessible");

$lto = ("Dernière fois ok");

$ltw = ("Dernière fois en avertissement");

$ltunk = ("Dernière fois inconnue");

$ltc = ("Dernière fois critique");

$isUp = ("est en ligne");

$isDown = ("est hors ligne");

$inWar = ("est en avertissement");

$incrit = ("est critique");

$isunk = ("a un statut inconnu");

$controlInfo = ("Arrêter/Démarrer la mise à jour des info.");

$appStatus = ("Statut actuel de l'application");

$noLatLng = ("Il n'y a pas de définition de LatLng dans les paramètres");

$noHostN = ("N'a pas de HostName");

$noStatus = ("N'existe pas dans le fichier d'état");

$outFilterHg = ("Ce n'est pas dans le HostGroup filtré.");

$help = ("Aide");

$close = ("Fermer");

$primary = (" (Primaire)");

$debugHelp = ("TCette page contient des informations utiles pour toute demande de support.!<br><br>

Les caractéristiques des pages sont les suivantes:<br><br>

<strong>1 - Les hôtes qui ont été ignorés.</strong><br>
     - Affiche tous les hôtes ignorés.<br>
     - Informe le nom d\'hôte.<br>
     - L\'alias de l\'hôte.<br>
     - Les raisons pour lesquelles cet hôte a été ignorées.<br>
     - Les raisons peuvent être très utiles pour définir s\'il s\'agis d\'une erreur de configuration ou d\'un bug applicatif.<br><br>

<strong>2 - Informations importantes sur chaque hôte dans le fichier d\'état.</strong><br>
     - La couleur de la \'Card\' indique le statut de l\'hôte ou du service en question.<br>
     - Affiche des informations sur le statut interne.<br>
         - Vert: ok; Jaune: attention; Orange: critique; Gris: inconnu.<br>
     - Affiche les valeurs de temps pour plusieurs paramètres.<br>
     - Affiche l\'heure au format Epoch et l\'heure en heures et minutes.<br><br>

<strong>3 - Au bas de la page se trouve le contrôleur pour mettre à jour les informations de la page..</strong><br>
     - Il est possible d\'arrêter la mise à jour à tout moment, utile pour capturer des événements rapides.<br>
     - Il y a aussi un bouton de téléchargement qui télécharge un fichier avec les informations sur la page en ce moment.<br>
     - Le bouton de téléchargement est désactivé pendant les mises à jour des informations de page.<br>
<br>
<strong>Chaque fois que vous sollicitez une assistance</strong> accéder à la page de débogage, téléchargez le fichier et joignez-la à votre demande, cette procédure peut faciliter le dépannage.<br><br>

Vous pouvez obtenir de l\'aide en me contactant par e-mail: <strong>joao_carlos.r@hotmail.com</strong>");

//Auth

$authFail = ("Authentification échouée! Réessayer ultérieurement.");
?>
