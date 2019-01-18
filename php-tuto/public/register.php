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
    $email = !empty($_POST['email']) ? $_POST['email'] : null;

    // On créé un tableau qui contient toute nos erreurs
    $errors = [];

    // Je ne fais que la vérification pour savoir si les champs sont vide
    // mais il faut aussi vérifier la taille du pseudo, si l'email est bon etc.
    if (empty($nickname) || empty($password) || empty($email)) {
        $errors['empty'] = 'Vous devez remplir tous les champs !';
    }

    if (empty($errors)) {
        // On prépare la requête avec des tag pour nos valeurs
        $insertUser = $db->prepare('INSERT INTO users (nickname, password, email) VALUES (:nickname, :password, :email)');

        // On remplace les tags par nos valeurs ce qui permet de contrer des injections SQL possibles
        $insertUser->bindValue(':nickname', $nickname, PDO::PARAM_STR);
        $insertUser->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $insertUser->bindValue(':email', $email, PDO::PARAM_STR);
        $insertUser->execute();
        
        $_SESSION['flash'] = [
            'type' => 'success',
            'content' => 'Vous êtes maintenant inscrit !'
        ];

        header('location: /index.php');
        exit;
    } else {
        // Tu peux mettre ce que tu veux
        $_SESSION['flash'] = [
            'type' => 'danger',
            'content' => 'Vous avez des erreurs dans le formulaire'
        ];

        header('location: /register.php');
        exit;
    }
}

require __DIR__.'/inc/header.php';
?>
<h1>Inscription</h1>
<h2><em><a href="index.php">Revenir à l'accueil</a></em></h2>

<form action="register.php" method="post">
    <label for="nickname">Votre pseudo :</label>
    <input type="text" name="nickname" id="nickname" placeholder="Votre pseudo ..."><br>
    <label for="password">Votre mot de passe :</label>
    <input type="password" name="password" id="password" placeholder="Votre mot de passe ..."><br>
    <label for="email">Votre adresse mail :</label>
    <input type="email" name="email" id="email" placeholder="Votre adresse mail ..."><br>
    <button type="submit">S'enregistrer</button>
</form>
<?php
require __DIR__.'/inc/footer.php';
?>

