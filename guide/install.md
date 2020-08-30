---
description: >-
  Dans cette partie nous allons voir comment installer le framework via composer
  et comment faire la première configuration.
---

# Installation

## Création du projet et lancement en développement

Lancer un terminal dans le dossier ou vous souhaitez mettre votre projet et lancez-y la commande :

```bash
composer create-project quenti77/phq <nomDuDossier>
```

Pour lancer le serveur vous pouvez le faire soit via un apache/nginx configurer ou via la commande php :

```bash
php -S host:port -t public
#host: localhost ou tout 0.0.0.0
#port: celui que vous voulez (ex: 3000)
```

Il vous faudra : soit éditer le fichier **bin/configs/database.php** :

{% code title="bin/configs/database.php" %}
```php
<?php
// ...

return [
    // DSN Value
    'db.type'       => DI\env('DB_TYPE',    'mysql'),
    'db.host'       => DI\env('DB_HOST',    'localhost'),
    'db.name'       => DI\env('DB_NAME',    'blog'),
    'db.port'       => DI\env('DB_PORT',    3306),
    'db.charset'    => DI\env('DB_CHARSET', 'UTF8'),

    // DSN generate ...

    'db.user'       => DI\env('DB_USER',    'root'),
    'db.pass'       => DI\env('DB_PASS',    'root'),

    // Instance ...
];
```
{% endcode %}

Soit définir des variables d'environnement correspondant au variable du fichier ci-dessus. Par exemple pour changer le nom de la db sur linux :

```bash
set DB_NAME=nomDeVotreBase
```

Voilà pour l'installation du framework et sa configuration minimal.

## Installation pour un serveur

Quand vous voudrez le mettre sur un serveur avec apache ou nginx il faudra utiliser une configuration soit via les vhosts apache ou le fichier .htaccess ou les vhosts de nginx.

### Apache via htaccess :

Normalement le fichier est déjà présent et suffit pour avoir accès au routing mais il se peut que vous ayez besoin de le modifier surtout avec des hébergement mutualisé.

### Apache avec un vhost :

```markup
#Require all granted

<VirtualHost example.fr:80>

  ServerAdmin admin@example.fr
  ServerName example.fr
  ServerAlias example.fr
  DocumentRoot "/var/www/public"

  <Directory "/var/www/public">
    Options Indexes FollowSymLinks MultiViews
    AllowOverride all
    Require all granted
  </Directory>

</VirtualHost>
```

### Nginx avec un vhost :

```javascript
server {
    listen 80;
    server_name example.fr;

    root /var/www/public;
    index index.php index.html;

    error_log /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ .php$ {
	    try_files $uri =404;

	    fastcgi_pass unix:/var/run/php/php7.2-fpm.sock; # PHP 7.2
	    fastcgi_index index.php;
	    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
	    include fastcgi_params;
    }

}
```

Voilà les différentes configurations possible qui couvre les cas les plus utilisés. Si besoin demandé sur github dans les issues \(n'oubliez pas de faire une recherche avant ^^ \)

