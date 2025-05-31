# RAPPORT DE PROJET - GL2  
**Année Scolaire : 2024-2025**  

## Plateforme E-Commerce : Aim&Deploy  

**Équipe :**  
- Ben Hazem Ahmed Omar  
- Chourou Houssem  
- Khili Karim  
- Skhiri Ahmed  

**Date :** 30/05/2025  
**Établissement :** Institut National des Sciences Appliquées et Technologies (INSAT)  
**Repo GitHub :** [Projet Symfony](https://github.com/aobh2763/Projet-Symfony)  

---

## Sommaire
1. [Introduction](#1-introduction)  
2. [Cahier des charges](#2-cahier-des-charges)  
   - [Contexte et objectifs](#21--contexte-et-objectifs)  
   - [Description générale](#22--description-générale)  
   - [Contexte du système](#23--contexte-du-système)  
   - [Acteurs du système](#24--acteurs-du-système)  
   - [Spécifications fonctionnelles](#25--spécifications-fonctionnelles)  
   - [Spécifications non-fonctionnelles](#26--spécifications-non-fonctionnelles)  
3. [Diagrammes](#3-diagrammes)  
4. [Structure du projet](#4-structure-du-projet)  
5. [Aperçu visuel de la plateforme](#5-apercu-visuel-de-la-plateforme)  
6. [Contributions](#6-contributions)  
7. [Conclusion](#7-conclusion)  

---

## 1. INTRODUCTION  

Le projet **Aim&Deploy** consiste à développer une application web de e-commerce spécialisée dans la vente d’armes, de munitions et d’accessoires en Tunisie.  

L’objectif est de fournir une plateforme sécurisée, conforme à la législation tunisienne, permettant aux utilisateurs de consulter les produits, passer des commandes et gérer leur compte.

---

## 2. CAHIER DES CHARGES  

### 2.1 Contexte et Objectifs  

Face à l’absence d’un site tunisien fiable dédié à la vente légale d’armes et accessoires, Aim&Deploy vise à combler ce besoin via un site web sécurisé, moderne et réservé aux utilisateurs adultes autorisés.  

**Objectifs :**
- Interface claire et intuitive.
- Gestion complète du compte utilisateur.
- Catalogue organisé (armes, munitions, accessoires).
- Pages informatives légales.

---

### 2.2 Description Générale  

Développé avec **Symfony 6+**, utilisant **Twig** et une base **MySQL**.  

**Fonctionnalités principales :**
- Page d’accueil dynamique.
- Catalogue par catégories.
- Fiches produits détaillées.
- Authentification + rôles (utilisateur/admin).
- Compte utilisateur (commandes, wishlist, etc.).
- Paiement simulé (Flouci / e-dinar).
- Pages légales (CGU, confidentialité).
- Interface d’administration.

---

### 2.3 Contexte du Système  

Développement en local, déploiement possible sur serveur distant.  

**Utilisateurs :**
- Utilisateurs finaux
- Administrateurs  

**Architecture :** MVC  
**Design :** sobre et professionnel

---

### 2.4 Acteurs du Système  

- **Internaute** : consulte sans s’authentifier  
- **Utilisateur inscrit** : peut commander, gérer wishlist, profil  
- **Administrateur** : gère produits, utilisateurs, commandes

---

### 2.5 Spécifications Fonctionnelles  

**Frontend Utilisateur :**
- Navigation fluide  
- Visualisation des produits  
- Détail des produits  
- Inscription / Connexion  
- Compte (commandes, wishlist, etc.)  
- Panier + commande  
- Accès CGU et confidentialité  

**Backend Administrateur :**
- CRUD Produits  
- Gestion utilisateurs  
- Modération  
- Suivi commandes  

---

### 2.6 Spécifications Non-Fonctionnelles  

**Sécurité :**
- Routes protégées  
- Validation serveur  

**Performance :**
- Requêtes optimisées  
- Cache  

**Design :**
- Responsive  
- UI claire et cohérente  
- Accessibilité  

**Maintenabilité :**
- Fixtures Symfony  
- Organisation modulaire  

**Extensibilité :**
- Vérification d'identité possible  

---

## 3. DIAGRAMMES  

### 3.1 Diagramme de Cas d’Utilisation  
*Voir Figure 1 (non incluse)*  

### 3.2 Diagramme de Classes  
Suivant le modèle **MVC**.  
*Voir Figure 2 (non incluse)*  

---

## 4. STRUCTURE DU PROJET  

*Section illustrant l’organisation du projet (contenu visuel non inclus)*  

---

## 5. APERÇU VISUEL DE LA PLATEFORME  

**5.1 Page d’accueil**  
- Filtrage par catégorie  

**5.2 Page produits**  
- Filtres : recherche, type, prix, etc.

**5.3 Détails produits**  

**5.4 Espace utilisateur**  
- Voir commandes : produit, date, état, total  
- Voir wishlist : reviews, ajout panier  
- Gestion profil : modifier informations  

**5.5 Carte utilisateur**  

**5.6 Checkout**  

**5.7 Interface Admin**  
- Création / modification / suppression produit  

**5.8 Footer**  

---

## 6. CONTRIBUTIONS  

Organisation des tâches via Google Sheets :  
- Attribution des tâches  
- Évitement de conflits  
- Suivi clair et transparent  
- Répartition équitable  

---

## 7. CONCLUSION  

Le projet **Aim&Deploy** établit les bases d’une plateforme e-commerce sécurisée et conforme pour la vente d’armes en Tunisie. Grâce à une planification rigoureuse et une répartition efficace, le cahier des charges définit clairement les besoins, fonctionnalités et contraintes techniques.

---

