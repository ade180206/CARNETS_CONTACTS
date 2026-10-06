<?php
function nettoyer($valeur) {
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

function valider_contact($nom, $email, $telephone) {
    $erreurs = [];
    if (strlen($nom) < 2) {
        $erreurs[] = "Le nom doit contenir au moins 2 caractères.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }
    if (!ctype_digit($telephone) || strlen($telephone)!== 10) {
        $erreurs[] = "Le téléphone doit contenir exactement 10 chiffres.";
    }
    return $erreurs;
}

function compter_contacts($contacts) {
    return count($contacts);
}

// BONUS : filtrer par nom
function filtrer_contacts($contacts, $recherche) {
    if (empty($recherche)) return $contacts;
    $recherche = strtolower($recherche);
    return array_filter($contacts, function($c) use ($recherche) {
        return strpos(strtolower($c['nom']), $recherche)!== false;
    });
}