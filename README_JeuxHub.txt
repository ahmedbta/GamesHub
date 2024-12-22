
# JeuxHub

**Auteur :** Ahmed Ben Taleb Ali  
**Nom du projet :** JeuxHub

---

### **Description du projet**  
JeuxHub est une plateforme de gestion d'objets, permettant aux utilisateurs d'organiser leur collection via un système d'inventaire personnalisé.  
Chaque utilisateur possède un "Coffre" (inventaire) pour stocker ses objets. De plus, les objets peuvent être consultés et affichés via une "Bibliothèque" publique.

---

### **Thématique**  
Le projet tourne autour de la gestion et de la visualisation des collections d'objets. Les utilisateurs peuvent créer un inventaire personnel (Coffre), y ajouter leurs objets, tout en explorant d'autres objets via une interface publique.

---

### **Comment Lancer le Projet**  
1. Dézippez le projet et placez-le dans votre répertoire de travail.
2. Installez les dépendances :  
   ```bash
   composer install
   ```
3. Configurez la base de données :  
   ```bash
   symfony console doctrine:database:create
   symfony console doctrine:schema:update --force
   symfony console doctrine:fixtures:load
   ```
4. Lancez le serveur Symfony :  
   ```bash
   symfony server:start
   ```

5. Accédez à l'application :  
   - `http://127.0.0.1:8000`

---

### **Entités Créées**  

1. **jeu**  [ objet ]
   - **Attributs :** `id`, `nom`, `description`, `nombre_joueurs_maximum`, `nombre_joueurs_minimum`, `temps_jeu_moyen`, `type`, `image`.  
   - **Relations :**  
     - Un objet peut appartenir à plusieurs Coffres.

2. **Coffre**  [ inventaire ]
   - **Attributs :** `id`, `user`.  
   - **Relations :**  
     - Un Coffre peut contenir plusieurs objets.  
     - Un Coffre appartient à un utilisateur.

3. **Utilisateur**  
   - **Attributs :** `id`, `username`, `email`.  
   - **Relations :**  
     - Un utilisateur possède un seul Coffre.  

4. **Bibliothèque**  [ galerie ]
   - **Attributs :** `id`, `nom`, `description`, `objetsAffiches`, `user`.  
   - **Relations :**  
     - Une Bibliothèque peut contenir plusieurs objets.  
     - Une Bibliothèque appartient à un utilisateur.  

---

Comptes Disponibles

Type de Compte	             Email	            Mot de Passe
Admin	                     admin@localhost  	    adminpass          Rôle : ROLE_ADMIN (Accès complet, gestion de tous les utilisateurs, objets et bibliothèques)
Utilisateur 1	             user1@localhost	    userpass           Rôle : ROLE_USER (Accès : Gère uniquement ses propres objets et bibliothèques ) 
Utilisateur 2	             user2@localhost        userpass           Rôle : ROLE_USER
 

---

### **Routes Implémentées**  

| **Nom de Route**           | **Méthode HTTP** | **Chemin**                                           | **Accès**                              
|----------------------------|-----------------|----------------------------------------------------|---------------------------------------|
| `app_bibliotheque_index`   | `GET`           | `/bibliotheque`                                    | Authentification requise (ROLE_USER). |
| `app_bibliotheque_show`    | `GET`           | `/bibliotheque/{id}`                               | Authentification requise (ROLE_USER). |
| `app_bibliotheque_edit`    | `GET|POST`      | `/bibliotheque/{id}/edit`                          | Propriétaire requis (ROLE_USER).      |
| `app_bibliotheque_delete`  | `POST`          | `/bibliotheque/{id}`                               | Propriétaire requis (ROLE_USER).      |
| `app_coffre_index`         | `GET`           | `/coffres`                                         | Authentification requise (ROLE_USER). |
| `app_coffre_show`          | `GET`           | `/coffre/{id}`                                     | Propriétaire requis (ROLE_USER).      |
| `app_objet_index`          | `GET`           | `/objet`                                           | Affiche les objets de l'utilisateur.  |
| `app_objet_show`           | `GET`           | `/objet/{id}`                                      | Propriétaire requis (ROLE_USER).      |
| `app_objet_new`            | `GET|POST`      | `/objet/new/{id}`                                  | Authentification requise (ROLE_USER). |
| `app_objet_edit`           | `GET|POST`      | `/objet/{id}/edit`                                 | Propriétaire requis (ROLE_USER).      |
| `app_objet_delete`         | `POST`          | `/objet/{id}`                                      | Propriétaire requis (ROLE_USER).      |
| `app_login`                | `GET|POST`      | `/login`                                           | Accès public.                         |
| `app_logout`               | `POST`          | `/logout`                                          | Authentification requise.             |
| `app_member_index`         | `GET`           | `/member`                                          | Accès administrateur (ROLE_ADMIN).    |
| `app_member_show`          | `GET`           | `/member/{id}`                                     | Accès admin ou utilisateur.           |

---

### **Caractéristiques Notables**  
- **Authentification & Autorisation :**  
  - Certaines routes (comme `/objet` et `/coffre`) nécessitent une authentification.  
  - La route `/bibliotheque` est accessible uniquement au propriétaire ou à un administrateur.  
  - Les administrateurs disposent de privilèges élevés, tels que la gestion des utilisateurs et la visualisation de tous les objets.  

- **Contenu Dynamique :**  
  - Dans `/objet`, seuls les objets appartenant à l'utilisateur connecté sont affichés.  
  - `/coffre/{id}` affiche tous les objets stockés dans un Coffre spécifique appartenant à l'utilisateur.  

- **Téléversement d'Images :**  
  - Les utilisateurs peuvent téléverser des photos de profil et des images d'objets via le VichUploaderBundle.  
  - Les images téléversées sont stockées dans les répertoires `uploads/profile_pictures` et `uploads/objets`.  

- **Interface Interactive :**  
  - Design basé sur Bootstrap pour une expérience utilisateur fluide.  


---

### **Informations Supplémentaires**  
- Utilisez `symfony console debug:router` pour inspecter toutes les routes disponibles.

---


