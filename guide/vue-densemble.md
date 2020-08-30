# Vue d'ensemble

## Architecture de base

Le framework ce base sur le principe du pattern ADR découpé en module. Il dispose de plusieurs dossier racine comme suit :

* Le dossier **app** : C'est ici que seront placer vos modules pour votre application
* Le dossier **bin** : Permet la configuration du framework de base même si un fichier de config peut-être mis dans le module.
* le dossier **public** : La ou se trouve le document root du projet et c'est ici que devra pointer votre serveur web \(apache, nginx, ...\)
* Le dossier **src** : Les sources du framework car pas encore découper dans plusieurs repo pour le moment.
* Le dossier **tests** : Permet de créer des fichier de test unitaires via PHPUnit.

## Les modules

On va se pencher un peu plus sur les modules qui permettent de mettre toute la logique de code, les vues, etc. dans un même dossier que l'on peut copier dans d'autre projet. Cela permet de faire des parties d'application réutilisable facilement.

Dans un module on peut y mettre autant de dossier que l'on souhaite et y mettre les noms que l'on veut. Le seul fichier à mettre obligatoirement c'est une classe qui étend de **PHQ\Module**.

{% code title="app/Blog/BlogModule.php" %}
```php
<?php

namespace App\Blog;

use PHQ\Module;

class BlogModule extends Module
{
}
```
{% endcode %}

Dans les modules vous pourrez retrouver différents dossier comme :

* **Actions** : Représente les actions de votre module. Par exemple l'affichage du formulaire d'édition serait une action \(EditAction\) et sa vérification et ajout en serait une autre \(UpdateAction\).
* **Db** : Représente le dossier ou seront les migrations et le seeding du module \(via 2 sous-dossiers\).
* **Entities** : Toute vos entité serait placé ici.
* **Renderer** : Toutes les classes gérant le rendu serait ici
* **Repositories** : Toutes les actions possible sur les données. Gestion des tables, des API, ...
* **Views** : Toutes vos vue serait ici \(html, xml, ...\)
* **Widgets** : Tous les widgets pour les autres modules \(1 sous-dossier par module au besoin\)

Dans les exemples on trouve aussi un fichier de configuration pour le DIC qui permet de définir pour un module des actions supplémentaire.

Bien sure les modules d'exemples peuvent être facilement retiré en supprimant les dossiers **Admin** et **Blog**. Et en supprimant dans le fichier **bin/app.php**  les lignes 8 et 9 qui ajoute les modules à l'application.

{% code title="bin/app.php" %}
```php
<?php

return [
    /**
     * Liste des modules de l'application
     */
    'modules' => [
        \App\Blog\BlogModule::class,
        \App\Admin\AdminModule::class
    ],

    /**
     * Liste des middlewares globaux de l'app
     * (attention l'ordre est important !)
     */
    // ...
];
```
{% endcode %}

Très simple un module au final ^^

