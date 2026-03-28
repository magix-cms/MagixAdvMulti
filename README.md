# MagixAdvMulti

[![Release](https://img.shields.io/github/release/magix-cms/MagixAdvMulti.svg)](https://github.com/magix-cms/MagixAdvMulti/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/MagixAdvMulti.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**MagixAdvMulti** est un plugin hybride de gestion de "Points Forts" (réassurance, avantages, services) pour Magix CMS 4.x. Il permet d'associer des icônes vectorielles à des contenus textuels et des liens, avec une gestion unifiée pour la page d'accueil et les modules natifs.

## 🌟 Fonctionnalités principales

* **Architecture Hybride** : Permet de gérer les points forts de la **Page d'Accueil** via une interface de configuration dédiée, tout en s'injectant comme onglet contextuel dans les modules **Produits, Pages, Catégories, News et About**.
* **Scanner d'Icônes Intelligent** : Inclut la classe `IconScanner` qui détecte automatiquement les icônes disponibles dans le thème actif (`Bootstrap Icons`, `IcoMoon`, `IcoFont`) en analysant les fichiers CSS compilés via Regex.
* **Layouts Dynamiques** : Support natif de deux modes d'affichage configurables par module via le dictionnaire Smarty :
    * `Top` : Icône centrée au-dessus du titre (idéal pour l'accueil).
    * `Left` : Icône alignée à gauche avec contenu à droite (idéal pour les fiches produits).
* **Interface Master-Detail AJAX** : Listing et formulaire d'édition ultra-fluides pilotés par `MagixAjaxManager`, sans rechargement de page.
* **Drag & Drop** : Réorganisation intuitive de l'ordre d'affichage des blocs par simple glisser-déposer.
* **SASS & Design Moderne** : Animations au survol (hover) intégrées et utilisation des variables CSS du thème (`var(--main-color)`) pour une intégration visuelle parfaite.

## ⚙️ Installation

1. Téléchargez la dernière version.
2. Placez le dossier `MagixAdvMulti` dans le répertoire `plugins/`.
3. Dans l'administration, allez dans **Extensions > Plugins** et cliquez sur **Installer**.
4. Le plugin créera automatiquement les tables `mc_plug_advmulti` et `mc_plug_advmulti_content`.
5. **Note technique** : Pour le scanner d'icônes, assurez-vous que votre thème génère un fichier `icons.css` dans son répertoire de styles.

## 🚀 Utilisation

### Côté Administration
* **Configuration Globale** : Pour la page d'accueil, allez dans **Extensions > Plugins > MagixAdvMulti > Configurer**.
* **Modules** : Le plugin apparaît automatiquement sous l'onglet **"Points Forts"** dans l'édition de vos contenus (Produits, Pages, etc.).
* L'interface propose une recherche instantanée et un aperçu dynamique des icônes détectées dans votre thème.

### Côté Public (Frontend)
Le rendu est géré par le fichier `widget.tpl` qui adapte son layout selon le hook d'appel. Le plugin est pré-configuré sur :
* `displayHomeBottom` (Accueil)
* `displayProductExtraContent` (Produits)
* `displayPageBottom` (Pages)
* `displayCategoryBottom` (Catégories)

## 🛠️ Architecture Technique

* **Mapping de Layout** : Utilise un `layoutMap` dans Smarty pour définir le design (`top` ou `left`) en fonction du module appelant, offrant une flexibilité totale sans multiplier les templates.
* **Optimisation UI** : Utilisation de la classe Bootstrap 5 `stretched-link` pour rendre les vignettes entièrement cliquables tout en respectant l'accessibilité.
* **Performance** : Analyse Regex optimisée des fichiers CSS dédiés pour la récupération des glyphes, garantissant un backoffice rapide même avec des milliers d'icônes.

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)