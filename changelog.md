---
description: >-
  Cette doc étant commencé avec la version 0.5 qui est une refonte complète du
  framework, il n'y aura donc pas de changelog pour les version d'avant.
---

# Changelog

## v0.5.6 - 2018-05-24

### Added

* Ajout du système de validation pour les données en provenance du body de la request ou d'un tableau.

## v0.5.5 - 2018-05-21

### Added

* Ajout de méthode dans la classe mère des Repositories
  * findAll\(\)
  * findById\(\)
  * save\(\)
* Ajout de la prise en charge des sessions
* Ajout des messages flash
* Ajout d'une extension Twig pour ajouter des helpers aux templates
* Ajout d'un middleware pour les token CSRF

### Updated

* Design de la page 404 revu

## v0.5.4 - 2018-05-19

### Added

* Ajout d'un exemple dans le module Blog

## v0.5.3 - 2018-05-18

### Added

* Ajout des Widgets. Cela permet àun module d'y inclure d'autre composant venant d'un autre module. Par exemple sur la page d'administration on pourrai y avoir aucune page par défaut et ce sont les autres modules comme le blog qui ajoute leur propre logique à l'intérieur.

## v0.5.2 - 2018-05-17

### Updated

* Déplacement de la liste des modules et des middlewares de l'application dans un fichier de configuration php **bin/app.php**.

## v0.5.1

### Fixed

* Phinx command
* Migration limit SQL in PostgreSQL
* Default charset in DSN for PostgreSQL

## v0.5 - 2018-05-11

### Added

* Base du framework et du système de module.
* Ajout de middleware de base
* Ajout de 2 méthode de rendu : PHP natif ou Twig
* Ajout d'un petit QueryBuilder et d'un système de Repositories.

### Removed

* De tout le code de la version d'avant \(v0.4.1.2\)



