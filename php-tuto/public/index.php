<?php
session_start();

var_dump(password_hash('Chiyome---dc', PASSWORD_BCRYPT));
echo sha1('toto');

/**
 * Ce fichier affichera les commentaires directement pour l'exemple
 * pour ne pas avoir à tout refaire mais c'est à adapter bien sûre ^^
 */

// Charge notre fichier qui nous connecte à la bdd
require __DIR__.'/inc/db.php';

// On effectue notre requête qui prends tout les commentaires
// trié par ordre décroissant via la date d'ajout (posted_at)
// On y ajoute les informations de notre utilisateur qui à envoyer
// le commentaire en question.
$query = $db->query('SELECT COUNT(*) AS nb FROM comments');
$data = $query->fetch();
$nbComment = $data['nb'];

$page = (!empty($_GET['p'])) ? intval($_GET['p']) : 1;
$maxByPage = 2;
$maxPage = ceil($nbComment / $maxByPage);

if ($page < 1) {
    $page = 1;
} elseif ($page > $maxPage) {
    $page = $maxPage;
}

$offset = ($page - 1) * $maxByPage;

$comments = $db->prepare('
    SELECT
        comments.id as comment_id, title, content, posted_at,
        users.id as user_id, nickname, email
    FROM comments
    LEFT JOIN users ON users.id = comments.user_id
    ORDER BY posted_at DESC
    LIMIT :offset, :limit');

// Va de (P - 1) x nbParPage et prend les nbParPage ligne
$comments->bindValue(':offset', $offset, PDO::PARAM_INT);
$comments->bindValue(':limit', $maxByPage, PDO::PARAM_INT);
$comments->execute();

// Quand on ajout un commentaire on vérifie d'abord aussi qu'il est en session
if (!empty($_SESSION['user']) && !empty($_POST)) {
    $title = !empty($_POST['title']) ? $_POST['title'] : null;
    $content = !empty($_POST['content']) ? $_POST['content'] : null;

    $errors = [];

    if (empty($title) || empty($content)) {
        $errors['empty'] = 'Vous devez remplir tous les champs !';
    }

    if (empty($errors)) {
        // On prépare la requête avec des tag pour nos valeurs
        $insertComment = $db->prepare('
            INSERT INTO
                comments (title, content, posted_at, user_id)
            VALUES (:title, :content, NOW(), :userId)');
        
        $insertComment->bindValue(':title', $title, PDO::PARAM_STR);
        $insertComment->bindValue(':content', $content, PDO::PARAM_STR);
        $insertComment->bindValue(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
        $insertComment->execute();
        
        $_SESSION['flash'] = [
            'type' => 'success',
            'content' => 'Vous êtes maintenant connecté !'
        ];
            
        header('location: /index.php');
        exit;
    } else {
        // Tu peux mettre ce que tu veux
        $_SESSION['flash'] = [
            'type' => 'danger',
            'content' => 'Vous avez des erreurs dans le formulaire'
        ];

        header('location: /index.php');
        exit;
    }
}

require __DIR__.'/inc/header.php';
?>

<!--
On peut utiliser le foreach sur la variable $comments
Cela revient à faire un while :
while ($comment = $comments->fetch()) {}
-->
<h1>Les commentaires</h1>
<?php if (!empty($_SESSION['user'])): ?>
    <h2><em><a href="disconnect.php">Se déconnecter</a></em></h2>
<?php else: ?>
    <h2><em><a href="register.php">S'inscire</a></em> - <em><a href="login.php">Se connecter</a></em></h2>
<?php endif; ?>

<ul>
    <?php for ($i = 1; $i <= $maxPage; $i += 1): ?>
        <?php if ($i == $page): ?>
            <li><?= $i ?></li>
        <?php else: ?>
            <li><a href="/?p=<?= $i ?>"><?= $i ?></a></li>
        <?php endif; ?>
    <?php endfor; ?>
</ul>

<div class="comments">
<?php foreach ($comments as $comment): ?>
    <div class="comment">
        <h4>
            <!-- Avec htmlentities j'empêche les failles XSS -->
            <?= htmlentities($comment['title']) ?> écrit par <strong><?= htmlentities($comment['nickname']) ?></strong>
            le <?= DateTime::createFromFormat('Y-m-d H:i:s', $comment['posted_at'])->format('d/m/Y H:i:s') ?>
        </h4>
        <p>
            <?= nl2br(htmlentities($comment['content'])) ?>
        </p>
    </div>
<?php endforeach; ?>
</div>

<!-- Formulaire pour l'ajout du commentaire quand on est connecté -->
<?php if (!empty($_SESSION['user'])): ?>
    <form action="index.php" method="post">
        <label for="title">Votre titre :</label><br>
        <input type="text" name="title" id="title" placeholder="Votre titre ..."><br>
        <label for="content">Votre message :</label><br>
        <textarea name="content" id="content" cols="30" rows="10" placeholder="Votre message ..."></textarea><br>
        <button type="submit">Envoyer le commentaire</button>
    </form>
<?php endif; ?>

<?php
require __DIR__.'/inc/footer.php';
?>

