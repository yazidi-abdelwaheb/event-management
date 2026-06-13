<p align="center">
  <img width="200" height="200" src="./assets/logo.png" alt="Event Management logo">
</p>

<h3 align="center">Event Management</h3>

<div align="center">

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4.svg)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.4-000000.svg)](https://symfony.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED.svg)](https://www.docker.com/)

</div>

---

<p align="center">
  Une application Symfony pour gérer les événements, les inscriptions, les newsletters et l’administration d’un portail événementiel.
  <br>
</p>

## 📝 Table of Contents

- [About](#about)
- [Demo / Working](#demo)
- [How it works](#working)
- [Usage](#usage)
- [Getting Started](#getting_started)
- [Deploying your own project](#deployment)
- [Built Using](#built_using)
- [Authors](#authors)
- [Acknowledgements](#acknowledgement)

## 🧐 About <a name="about"></a>

Ce projet est une plateforme web Symfony dédiée à la gestion d’événements. Elle permet de présenter des événements publics, de gérer les inscriptions, d’envoyer des confirmations par email, et de superviser les contenus via un tableau de bord administrateur.

L’application combine Symfony 7.4, Doctrine ORM, Twig, EasyAdmin et des composants de sécurité et d’authentification pour offrir une base complète de gestion événementielle avec une interface moderne.

## 🎥 Demo / Working <a name="demo"></a>

Le projet couvre plusieurs scénarios fonctionnels :

- consultation des événements publics ;
- soumission d’un formulaire de contact ;
- inscription à un événement avec génération d’un QR code ;
- gestion de la newsletter ;
- administration des catégories, utilisateurs, inscriptions et messages.

## 💭 How it works <a name="working"></a>

L’application repose sur une architecture Symfony classique avec :

- des contrôleurs front-office pour l’affichage et les formulaires ;
- des entités Doctrine pour les événements, les inscriptions, les contacts et les newsletters ;
- un service d’email pour les confirmations et notifications ;
- EasyAdmin pour l’administration des données ;
- des composants Symfony tels que Security, Validator, Messenger et Asset Mapper.

Le flux principal suit la logique suivante : un utilisateur consulte un événement, s’inscrit via un formulaire, l’inscription est enregistrée en base, puis une confirmation est envoyée par email avec un QR code lié à l’inscription.

## 🎈 Usage <a name="usage"></a>

Pour démarrer l’application localement :

```bash
composer install
docker compose up -d
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve
```

Une fois lancée, l’application permet d’accéder aux pages publiques et à l’espace administrateur pour gérer les contenus et les inscriptions.

### Exemple de parcours

1. Ouvrir la page des événements publics.
2. Sélectionner un événement et s’inscrire.
3. Recevoir la confirmation par email avec QR code.
4. Gérer les données depuis le tableau de bord admin.

## 🏁 Getting Started <a name="getting_started"></a>

### Prerequisites

Vous aurez besoin de :

- PHP 8.2+
- Composer
- Docker et Docker Compose
- Un navigateur web moderne

### Installing

```bash
composer install
docker compose up -d
php bin/console doctrine:migrations:migrate
```

Puis lancer le serveur de développement :

```bash
symfony serve
```

## 🚀 Deploying your own project <a name="deployment"></a>

Le projet est prêt pour un déploiement basé sur Docker et Symfony. La configuration de base utilise PostgreSQL via Docker Compose, ce qui facilite la mise en production ou la mise à l’échelle de l’environnement applicatif.

## ⛏️ Built Using <a name="built_using"></a>

- [Symfony 7.4](https://symfony.com/) - Framework PHP principal
- [Doctrine ORM](https://www.doctrine-project.org/) - Gestion des entités et de la base de données
- [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html) - Tableau de bord d’administration
- [Twig](https://twig.symfony.com/) - Moteur de templates
- [Mailer](https://symfony.com/doc/current/mailer.html) - Envoi d’emails de confirmation
- [Docker Compose](https://docs.docker.com/compose/) - Environnement de développement local

## ✍️ Authors <a name="authors"></a>

- Projet Symfony événementiel maintenu par l’équipe de développement
- Interface administrateur et logique métier intégrées dans le code source du dépôt

## 🎉 Acknowledgements <a name="acknowledgement"></a>

- Merci à la communauté Symfony pour ses bundles et bonnes pratiques
- Merci à Doctrine, EasyAdmin et Twig pour l’architecture de ce projet
- Inspiration issue des applications de gestion d’événements modernes
