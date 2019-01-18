# PHQ : Framework PHP

[![Github Releases](https://img.shields.io/badge/version-0.5.7-blue.svg)]() [![Github Releases](https://img.shields.io/badge/php-7.1-blue.svg)]() [![Github Releases](https://img.shields.io/badge/composer-quenti77/phq-red.svg)]()

**Besoin d'une structure de projet ADR pour vos petits projet ? Ce framework est surement fait pour vous !**

## Pré-requis :

Pour utiliser ce framework il vous faut composer pour pouvoir installer et mettre à jour les dépendances si besoin. Le framework est fonctionnel en PHP 5.6 mais aussi en PHP 7. Vous devrez utiliser un fichier htaccess ou configurer votre nginx de cette façons :

**> htaccess** (déjà intégré)
```
# Dans le dossier public et votre document root doit pointer ce dosser
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php?url=$1
```
**> nginx**
```
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

        location ~ \.php$ {
                try_files $uri =404;

                fastcgi_pass unix:/var/run/php/php7.1-fpm.sock; # PHP 7.1
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
        }
}
```

## Installation :
Voici les étapes d'installation du framework :

 1. Dirigez-vous dans le dossier ou sera votre projet
 2. Faite un composer (ou composer.phar) ```composer create-project quenti77/phq [nom_project]```
 3. Si vous voulez pas du dossier qu'il créé mais seulement des fichiers qui s'y trouve vous pouvez faire : 
	 - ```mv ./[nom_project|phq]/* ./```
	 - ```rm -rf ./[nom_project|phq]```

## La doc :

Voici le liens vers la doc du framework : [Doc officiel de PHQ](https://phq.gitbook.io/doc/)
