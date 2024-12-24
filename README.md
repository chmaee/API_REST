# Organisation d'evenement - Projet Symfony G4 2024/2025

## Description
- Ce projet utilise les frameworks Symfony et API Platform pour développer une API dédiée à la gestion d'événements.
---
### Fonctionnalités principales :

- **ROLE_USER :**
    - S'inscrit et se désinscrit d'événements.

- **ROLE_ORGANISATEUR :**
    - Crée, modifie et supprime des événements.

- **ROLE_ADMIN :**
  - Supprime des événements et des comptes d'utilisateurs.
  
- **Définir les droits :**
  - Les administrateurs et organisateurs sont nommées en modifiant leur rôle directement dans la base de données. Exemple :

    ```bash
    UPDATE utilisateur SET roles = '["ROLE_ADMIN"]' WHERE login = 'utilisateur1';
    ```
     ```bash
    UPDATE utilisateur SET roles = '["ROLE_ORGANISATEUR"]' WHERE login IN ('organisateur1', 'organisateur2');
    ```

---
## Technologies Utilisées

- **Langages :** PHP
- **Framework :** Symfony, API Platform
- **Base de données :** MySQL
- **Versionnage :** GitLab
- **Déploiement :** Docker
- **Requêtes HTTP :** Postman
---
## Installation

1. **Cloner le dépôt :**

   ```bash
   git clone git@gitlabinfo.iutmontp.univ-montp2.fr:web-but3/sponsoringtest.git
   ```

2. **Installer les dépendances :**

   ```bash
   composer install
   ```
3. **Créer la base de données :**

   ```bash
   php bin/console doctrine:database:create
   ```
   
4.  **Créer et appliquer une migration :**

    ```bash
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
    ```

7. **Appliquer la mise à jour du schéma doctrine :**

   ```bash
    php bin/console doctrine:schema:update --force
    ```
---
## Liste des principales routes

| Name                                               | Method | Path                                |
|----------------------------------------------------|--------|-------------------------------------|
| _api_/evenements{._format}_get_collection          | GET    | /api/evenements.{_format}           |
| _api_/evenements{._format}_post                    | POST   | /api/evenements.{_format}           |
| _api_/evenements/{id}{._format}_delete             | DELETE | /api/evenements/{id}.{_format}      |
| _api_/evenements/{id}{._format}_patch              | PATCH  | /api/evenements/{id}.{_format}      |
| _api_/utilisateurs/{id}{._format}_get              | GET    | /api/utilisateurs/{id}.{_format}    |
| _api_/utilisateurs{._format}_get_collection        | GET    | /api/utilisateurs.{_format}         |
| _api_/utilisateurs{._format}_post                  | POST   | /api/utilisateurs.{_format}         |
| _api_/utilisateurs/{id}{._format}_delete           | DELETE | /api/utilisateurs/{id}.{_format}    |
| _api_/utilisateurs/{id}{._format}_patch            | PATCH  | /api/utilisateurs/{id}.{_format}    |
| _api_/evenements/{id}/inscription_post             | POST   | /api/evenements/{id}/inscription    |
| _api_/evenements/{id}/desinscription_delete        | DELETE | /api/evenements/{id}/desinscription |
| api_auth                                           | POST   | /api/auth                           |
| _api_/participations/{id}{._format}_get            | GET    | /api/participations/{id}.{_format}  |
| _api_/participations/{id}{._format}_get_collection | GET    | /api/participations/.{_format}      |
| _api_/participations{._format}_post                | POST   | /api/participations/.{_format}      |
| _api_/participations/{id}{._format}_patch          | PATCH  | /api/participations/{id}.{_format}  |
| _api_/participations/{id}{._format}_delete         | DELETE | /api/participations/{id}.{_format}  |
| gesdinet_jwt_refresh_token                         | POST   | /api/token/refresh                  |
| api_token_invalidate                               | POST   | /api/token/invalidate               |

---
## Contributions des membres de l'équipe

- **Chaïmae Asiamar - [chaimae.asiamar@etu.umontpellier.fr](mailto:chaima.asiamar@etu.umontpellier.fr)** : 33%
    - Création d’événements par organisateurs. 
    - Modification et suppression par organisateurs. 
    - Consultation, inscription, et désinscription.

- **Lilian Bramand - [lilian.bramand@etu.umontpellier.fr](mailto:lilian.bramand@etu.umontpellier.fr)** : 33%
    - Base du projet et adaptation
    - Gestion de projet

- **Cédric Leretour - [cedric.leretour@etu.umontpellier.fr](mailto:cedric.leretour@etu.umontpellier.fr)** : 33%
    - Sécurité
    - Test des routes
    - README

---
# Annexe

## Proposition d'insertion dans la base de données via POSTMAN

#### **Créer un utilisateur (utilisateur1)**
- **Méthode :** `POST`
- **Nom route :** `_api_/utilisateurs{._format}_post`

```bash
POST https://localhost/sponsoringtest/public/api/utilisateurs
Body (JSON):
{
  "login": "utilisateur1",
  "prenom": "Alan",
  "nom": "Terieur",
  "plainPassword": "Password123",
  "adresseEmail": "alan.terieur@example.com"
}
```

#### **Créer un utilisateur (utilisateur2)**
- **Méthode :** `POST`
- **Nom route :** `_api_/utilisateurs{._format}_post`

```bash
POST https://localhost/sponsoringtest/public/api/utilisateurs
Body (JSON):
{
  "login": "utilisateur2",
  "nom": "Bricot",
  "prenom": "Juda",
  "plainPassword": "Password123",
  "adresseEmail": "juda.bricot@example.com"
}
```

#### **Créer un organisateur (organisateur1)**
- **Méthode :** `POST`
- **Nom route :** `_api_/utilisateurs{._format}_post`

```bash
POST https://localhost/sponsoringtest/public/api/utilisateurs
Body (JSON):
{
  "login": "organisateur1",
  "prenom": "Nadine",
  "nom": "Greux",
  "plainPassword": "Password123",
  "adresseEmail": "nadine.greux@example.com"
}
```

#### **Créer un organisateur (organisateur2)**
- **Méthode :** `POST`
- **Nom route :** `_api_/utilisateurs{._format}_post`

```bash
POST https://localhost/sponsoringtest/public/api/utilisateurs
Body (JSON):
{
  "login": "organisateur2",
  "prenom": "Guy",
  "nom": "Tare",
  "plainPassword": "Password123",
  "adresseEmail": "guy.tare@example.com"
}
```
---
### **Accorder les droits nécessaires dans la base de données**

```bash
UPDATE utilisateur SET roles = '["ROLE_ADMIN"]' WHERE login = 'utilisateur1';
```
 ```bash
UPDATE utilisateur SET roles = '["ROLE_ORGANISATEUR"]' WHERE login IN ('organisateur1', 'organisateur2');
```
--- 

### **Auhtentification**

- **Méthode :** `POST`
- **Nom route :** `api_auth`

```bash
POST https://localhost/sponsoringtest/public/api/auth
Body (JSON):
{
  "login": "organisateur1",
  "password": "Password123"
}
```
---
    
### **Création des événements**

#### **Créer un événement (organisateur1)**
- **Méthode :** `POST`
- **Nom route :** `_api_/evenements{._format}_post`

```bash
POST https://localhost/sponsoringtest/public/api/evenements
Body (JSON):
{
  "nom": "Conférence sur la grenadine",
  "description": "Un événement organisé par Nadine Greux.",
  "date_debut": "2024-01-10T09:00:00+00:00",
  "date_fin": "2024-01-10T17:00:00+00:00",
  "lieu": "Paris",
  "capacite": 100,
  "budget": 2000,
  "categorie": "Conférence",
  "participant_visibilite": true
}
```

#### **Créer un événement (organisateur2)**
- **Méthode :** `POST`
- **Nom route :** `_api_/evenements{._format}_post`

```bash
POST https://localhost/sponsoringtest/public/api/evenements
Body (JSON):
{
  "nom": "Concert de guitare",
  "description": "Un événement organisé par Guy Tare.",
  "date_debut": "2024-02-15T09:00:00+00:00",
  "date_fin": "2024-02-15T17:00:00+00:00",
  "lieu": "Lyon",
  "capacite": 150,
  "budget": 3000,
  "categorie": "Atelier",
  "participant_visibilite": true
}
```


### **Consultation et gestion des événements et utilisateurs**

#### **Consulter la liste des événements**
- **Méthode :** `GET`
- **Nom route :** `_api_/evenements{._format}_get_collection`

```bash
GET https://localhost/sponsoringtest/public/api/evenements
```

#### **Consulter un événement (ID = 1)**
- **Méthode :** `GET`
- **Nom route :** `_api_/evenements/{id}{._format}_get`

```bash
GET https://localhost/sponsoringtest/public/api/evenements/1
```

#### **S'inscrire à un événement (utilisateur1)**
- **Méthode :** `POST`
- **Nom route :** `_api_/evenements/{id}/inscription_post`

```bash
POST https://localhost/sponsoringtest/public/api/evenements/1/inscription
Body (JSON):
{}
```

#### **Se désinscrire d'un événement (utilisateur1)**
- **Méthode :** `DELETE`
- **Nom route :** `_api_/evenements/{id}/desinscription_delete`

```bash
DELETE https://localhost/sponsoringtest/public/api/evenements/1/desinscription
```

#### **Modifier un événement (organisateur1, événement ID = 1)**
- **Méthode :** `PATCH`
- **Nom route :** `_api_/evenements/{id}{._format}_patch`

```bash
PATCH https://localhost/sponsoringtest/public/api/evenements/1
Body (JSON):
{
  "capacite": 200
}
```

#### **Supprimer un événement (administrateur ou organisateur, événement ID = 1)**
- **Méthode :** `DELETE`
- **Nom route :** `_api_/evenements/{id}{._format}_delete`

```bash
DELETE https://localhost/sponsoringtest/public/api/evenements/1
```

#### **Consulter un utilisateur (ID = 1)**
- **Méthode :** `GET`
- **Nom route :** `_api_/utilisateurs/{id}{._format}_get`

```bash
GET https://localhost/sponsoringtest/public/api/utilisateurs/1
```

### **Consulter la collection des utilisateurs**
- **Méthode :** `GET`
- **Nom route :** `_api_/utilisateurs{._format}_get_collection`

```bash
GET https://localhost/sponsoringtest/public/api/utilisateurs
```

### **Modifier un utilisateur (utilisateur1, ID = 1)**
- **Méthode :** `PATCH`
- **Nom route :** `_api_/utilisateurs/{id}{._format}_patch`

```bash
PATCH https://localhost/sponsoringtest/public/api/utilisateurs/1
Body (JSON):
{
  "nom": "Leponge"
}
```

#### **Supprimer un utilisateur (administrateur uniquement, utilisateur ID = 1)**
- **Méthode :** `DELETE`
- **Nom route :** `_api_/utilisateurs/{id}{._format}_delete`

```bash
DELETE https://localhost/sponsoringtest/public/api/utilisateurs/2
```

#### **Consulter les événements d'un organisateur (ID = 1)**
- **Méthode :** `GET`
- **Nom route :** `_api_/utilisateurs/{id}/evenements_get_collection`

```bash
GET https://localhost/sponsoringtest/public/api/organisateur/evenements
```
