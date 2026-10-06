<?php
session_start();
require 'fonctions.php';

if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}
$erreurs = [];
$recherche = nettoyer($_GET['q']?? '');

// BONUS 1 : Suppression
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    if (isset($_SESSION['contacts'][$id])) {
        unset($_SESSION['contacts'][$id]);
        $_SESSION['contacts'] = array_values($_SESSION['contacts']);
    }
    header('Location: index.php');
    exit;
}

// Partie 2 : Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom']?? '');
    $email = nettoyer($_POST['email']?? '');
    $telephone = nettoyer($_POST['telephone']?? '');

    $erreurs = valider_contact($nom, $email, $telephone);

    // BONUS 2 : Doublons
    foreach ($_SESSION['contacts'] as $c) {
        if (strtolower($c['email']) === strtolower($email)) {
            $erreurs[] = "Cet email existe déjà!";
            break;
        }
    }

    if (empty($erreurs)) {
        $_SESSION['contacts'][] = compact('nom','email','telephone');
        header('Location: index.php');
        exit;
    }
}

$contacts_affiches = filtrer_contacts($_SESSION['contacts'], $recherche);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Carnet de contacts</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h1>Mon carnet de contacts</h1>
    <p class="text-muted">Total : <?= compter_contacts($_SESSION['contacts'])?> contact(s)</p>

    <?php if (!empty($erreurs)):?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach($erreurs as $e) echo "<li>$e</li>";?></ul></div>
    <?php endif;?>

    <!-- BONUS 3 : Recherche -->
    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Rechercher par nom..." value="<?= $recherche?>">
            <button class="btn btn-secondary">Rechercher</button>
        </div>
    </form>

    <form method="post" class="card p-3 mb-4">
        <h5>Ajouter un contact</h5>
        <input type="text" name="nom" class="form-control mb-2" placeholder="Nom (min 2 caractères)" required>
        <input type="text" name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="text" name="telephone" class="form-control mb-2" placeholder="Téléphone 10 chiffres" required>
        <button class="btn btn-primary">Ajouter</button>
    </form>

    <h2>Liste</h2>
    <?php if (empty($contacts_affiches)):?>
        <p>Aucun contact pour le moment.</p>
    <?php else:?>
        <table class="table table-striped table-bordered bg-white">
            <tr><th>N°</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Action</th></tr>
            <?php foreach($contacts_affiches as $i => $c):?>
            <tr>
                <td><?= $i+1?></td><td><?= $c['nom']?></td><td><?= $c['email']?></td><td><?= $c['telephone']?></td>
                <td><a href="?supprimer=<?= $i?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer?')">Supprimer</a></td>
            </tr>
            <?php endforeach;?>
        </table>
    <?php endif;?>
</div>
</body>
</html>