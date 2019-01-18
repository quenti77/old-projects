<?php
session_start();


// Charge notre fichier qui nous connecte à la bdd
require __DIR__.'/inc/db.php';

// Si la personne est connecté alors il doit partir
if (!empty($_SESSION['user'])) {
    header('location: /index.php');
    exit;
}

// Si le formulaire est envoyer ...
if (!empty($_POST)) {
    // Pour chaque champs on créé sa variable correspondante ou null si elle n'existe pas
    $nickname = !empty($_POST['nickname']) ? $_POST['nickname'] : null;
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    // On créé un tableau qui contient toute nos erreurs
    $errors = [];

    // Je ne fais que la vérification pour savoir si les champs sont vide
    // mais il faut aussi vérifier la taille du pseudo, si l'email est bon etc.
    if (empty($nickname) || empty($password)) {
        $errors['empty'] = 'Vous devez remplir tous les champs !';
    }

    if (empty($errors)) {
        // On prépare la requête avec des tag pour nos valeurs
        $insertUser = $db->prepare('SELECT id, nickname, password, email FROM users WHERE nickname = :nickname');

        // On remplace les tags par nos valeurs ce qui permet de contrer des injections SQL possibles
        $insertUser->bindValue(':nickname', $nickname, PDO::PARAM_STR);
        $insertUser->execute();
        $user = $insertUser->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'content' => 'Vous êtes maintenant connecté !'
            ];

            unset($user['password']);
            $_SESSION['user'] = $user;

            header('location: /index.php');
            exit;
        } else {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'content' => 'Login ou mot de passe invalide !'
            ];

            header('location: /login.php');
            exit;
        }
    } else {
        // Tu peux mettre ce que tu veux
        $_SESSION['flash'] = [
            'type' => 'danger',
            'content' => 'Vous avez des erreurs dans le formulaire'
        ];

        header('location: /login.php');
        exit;
    }
}

require __DIR__.'/inc/header.php';
?>
<h1>Connexion</h1>
<h2><em><a href="index.php">Revenir à l'accueil</a></em></h2>

<form action="login.php" method="post">
    <label for="nickname">Votre pseudo :</label>
    <input type="text" name="nickname" id="nickname" placeholder="Votre pseudo ..."><br>
    <label for="password">Votre mot de passe :</label>
    <input type="password" name="password" id="password" placeholder="Votre mot de passe ..."><br>
    <button type="submit">Se connecter</button>
</form>
<?php
require __DIR__.'/inc/footer.php';
?>

