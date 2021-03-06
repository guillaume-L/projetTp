<?php

//utilisation de ajax dans le if
if (isset($_POST['eventUpdate'])) {
    include_once '../configuration.php';
    include_once '../class/database.php';
    include_once '../models/events.php';
    $event = new events();
    $event->id = $_POST['eventUpdate'];
    echo json_encode(array('events' => $event->getEventsById()));
    //utilisation sans ajax dans le else
} else {

    /**
     * fonction qui formate la date au format jours/mois/année et qui la restitue au format année-mois-jours et qui verifie que c'est bien une date
     * en cas d'erreur elle revoie false
     * @param string $testDate
     * @return mixed
     */
    function isValidateDate($testDate) {
        $d = DateTime::createFromFormat('d-m-Y', $testDate);
        return ($d && $d->format('d-m-Y') == $testDate) ? date_format(date_create_from_format('d-m-Y', $testDate), 'Y-m-d') : false;
    }

//instanciation de l objet event
    $event = new events();
    $event->idUsers = $_SESSION['idUser'];
//verifis que l utilisateur à appuyé sur le bouton supprimer et que les données attendues soient bien numérique
    if (isset($_GET['event']) && is_numeric($_GET['event'])) {
        //recupere dans les proprietés id et idUser les informations passées en GET       
        $event->id = $_GET['event'];
        if (!$event->removeEvents()) {
            $errorMessage = 'La suppression a échouée';
        }
    }
//Déclaration de la variable $hasEvents et initialisation a TRUE
    $hasEvents = TRUE;
//stockage de la fonction getEvents dans la variable $eventsList
    $eventsList = $event->getEventsByUserId();
    $firstEventId = 0;
//si $eventsList n est pas egale a TRUE
    if (!$eventsList) {
        //alors $eventsList = FALSE (voir memberArea.php)
        $hasEvents = FALSE;
    } else {
        $firstEventId = $eventsList[0];
        $firstEventId = $firstEventId->id;
    }
//vérification du format de la date de debut
    if (!empty($_POST['startDate'])) {
        $startDate = isValidateDate($_POST['startDate']);
        if (!$startDate) {
            $errorList['startDate'] = 'mauvais format date';
        } else {
            $event->startDate = $startDate;
        }
    }
//vérification du format de la date de fin
    if (!empty($_POST['endDate'])) {
        $endDate = isValidateDate($_POST['endDate']);
        if (!$endDate) {
            $errorList['endDate'] = 'mauvais format date';
        } else {
            $event->endDate = $endDate;
        }
    }

//ajouts d'évènements dans la BDD
    $message ='';
    if (isset($_POST['create'])) {

        if (!empty($_POST['name']) && !empty($_POST['startDate']) && !empty($_POST['startTime']) && !empty($_POST['endDate']) && !empty($_POST['description']) && !empty($_POST['location']) && !empty($_POST['contribution'])) {
            $event->name = strip_tags($_POST['name']);
            $event->startDate = strip_tags($_POST['startDate']);
            $event->startTime = strip_tags($_POST['startTime']);
            $event->endDate = strip_tags($_POST['endDate']);
            $event->description = strip_tags($_POST['description']);
            $event->location = strip_tags($_POST['location']);
            $event->contribution = strip_tags($_POST['contribution']);
            if($event->addEvents()){
                $message='Un évènement à été ajouté';
            }else{
                $message ='Echec d\'ajout de l\'évènement';
            }
                
        } else {
            $message='Veuillez remplir tous les champs';
        }
    }

//modification d'évènements dans la table
    if (isset($_POST['update'])) {
        if (!empty($_POST['name'])) {
            $event->name = strip_tags($_POST['name']);
        }
        if (!empty($_POST['startDate'])) {
            $event->startDate = strip_tags($_POST['startDate']);
        }
        if (!empty($_POST['startTime'])) {
            $event->startTime = strip_tags($_POST['startTime']);
        }
        if (!empty($_POST['endDate'])) {
            $event->endDate = strip_tags($_POST['endDate']);
        }
        if (!empty($_POST['description'])) {
            $event->description = strip_tags($_POST['description']);
        }
        if (!empty($_POST['location'])) {
            $event->location = strip_tags($_POST['location']);
        }
        if (!empty($_POST['contribution'])) {
            $event->contribution = strip_tags($_POST['contribution']);
        }
        if (!empty($_POST['events'])) {
            $event->id = strip_tags($_POST['events']);
        }
        $event->editEvents();
    }
}