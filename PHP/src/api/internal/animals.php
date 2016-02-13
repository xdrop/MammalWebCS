<?php

include('../../core.php');

if(isset($_GET['action'])){
    if($_GET['action'] === 'list'){
        $listNamesQuery = new SpeciesNameQuery();
        echo json_encode($listNamesQuery->fetch());
    }
}