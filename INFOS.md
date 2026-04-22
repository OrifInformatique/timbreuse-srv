# INFOS - Modifications en cours et objectifs

Ce document synthétise les changements actuellement présents dans l'arbre de travail, ainsi que leur intention fonctionnelle.

## 1) Journalisation des suppressions définitives (hard delete)

### Fichiers concernés
- `orif/timbreuse/Database/Migrations/2026-04-16-000000_AddEventType.php`
- `orif/timbreuse/Models/EventTypeModel.php`
- `orif/timbreuse/Database/Seeds/AddEventTypeSyncDatas.php`
- `orif/timbreuse/Controllers/Badges.php`
- `orif/timbreuse/Controllers/Users.php`
- `orif/timbreuse/Controllers/EventLogsAPI.php`

### Modifications
- Ajout d'une nouvelle table `event_type` (migration) avec les colonnes:
  - `id`
  - `type`
  - `user_sync_id` (nullable)
  - `badge_number` (nullable)
  - `created_at`
- Ajout du modèle `EventTypeModel` avec une méthode `log_hard_delete(entityType, entityId, payload)` pour centraliser l'écriture des événements de suppression définitive.
- Appel de cette journalisation dans:
  - suppression définitive d'un badge (`Badges::delete_badge_post`)
  - suppression définitive d'un utilisateur (`Users`, branche `delete`)
- Ajout d'un endpoint API dédié (`EventLogsAPI::get`) qui:
  - vérifie un token de sécurité
  - retourne les événements depuis une date (`startDate`)
  - mappe le format serveur vers le contrat attendu côté client (`id_event`, `event_type`, `entity_type`, `entity_id`, `payload`, `date_event`)
- Ajout d'un seed (`AddEventTypeSyncDatas`) qui garantit l'existence d'une entrée de type `hard_delete` si absente.

### Objectif
- Disposer d'une trace synchronisable des suppressions définitives pour que les clients (ou systèmes reliés) puissent rejouer/aligner l'état local avec la source serveur.
- Éviter les désynchronisations silencieuses lors de suppressions irréversibles de badges ou d'utilisateurs.

## 2) Robustesse de l'API de logs badge

### Fichier concerné
- `orif/timbreuse/Controllers/LogsAPI.php`

### Modifications
- Remplacement de l'affectation directe de `id_user` depuis la recherche badge par une vérification explicite:
  - si le badge est introuvable, retour `404` (`failNotFound('badge not found')`)
  - sinon, affectation explicite de `data['id_user']`

### Objectif
- Éviter des insertions de logs incohérentes lorsque le badge n'existe pas.
- Rendre le comportement API plus prévisible pour les appels externes.

## 3) Ajustement de l'interface des logs personnels

### Fichier concerné
- `orif/timbreuse/Controllers/PersoLogs.php`

### Modification
- Retrait du bouton/lien `create_event_planning_link(...)` de la liste des actions.

### Objectif
- Simplifier l'interface affichée et retirer un accès devenu non souhaité ou non pertinent dans ce contexte.

## 4) Environnement Docker local (Apache + MariaDB + phpMyAdmin)

### Fichiers concernés
- `docker-compose.yml`
- `docker/apache/dockerfile`
- `docker/apache/000-default.conf`
- `docker/mariadb/dockerfile`
- `docker/mariadb/init.sql`
- `docker/mariadb/my.cnf`

### Modifications
- Ajout d'une stack Docker compose avec:
  - `apache` (PHP 8.1 + extensions `zip`, `pdo`, `pdo_mysql`, `intl`, `mysqli`)
  - `mariadb` (image 10.11 + configuration custom)
  - `phpmyadmin`
- Configuration Apache:
  - `DocumentRoot` sur `/var/www/html/public`
  - `AllowOverride All`
- Initialisation SQL MariaDB:
  - création des BDD `ci4` et `ci4_test`
  - création des utilisateurs applicatifs et droits associés

### Objectif
- Uniformiser et accélérer la mise en place d'un environnement de dev local reproductible.
- Faciliter les tests applicatifs et les vérifications manuelles de données (phpMyAdmin).

## 5) Outils de préparation des comptes de test E2E

### Fichiers concernés
- `tests/tools/create_test_timbreuse_user.php`
- `tests/tools/delete_test_timbreuse_user.php`

### Modifications
- Script de création/mise à jour de comptes de test:
  - crée/met à jour des comptes web
  - garantit le lien vers `user_sync` via `access_tim_user`
  - gère un fallback de connexion DB utile avec Docker (`mariadb` -> `127.0.0.1:3307`)
- Script de suppression nettoyant les dépendances:
  - suppression des liens `access_tim_user`
  - déliaison des badges (`badge_sync.id_user = NULL`)
  - purge des données liées (`log_sync`, `user_planning`, `user_sync`, puis `user`)

### Objectif
- Rendre les scénarios de tests intégration/E2E répétables et propres.
- Limiter les manipulations manuelles de base de données pendant les campagnes de test.

## Remarques de périmètre

- Ce document décrit les changements fonctionnels et techniques visibles dans les fichiers métier/infrastructure listés ci-dessus.
- Les artefacts générés localement (ex: logs temporaires) ne portent pas d'objectif produit et ne sont pas décrits ici.
