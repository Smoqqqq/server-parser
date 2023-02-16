# SERVER PARSER

Une application en Symfony pour stocker un ensemble de serveurs et tracker la position des sites internet qu'ils hébergent, avec un système de groupes.

## Créer un compte utilisateur
Lors de votre première connexion, ou si vous êtes déconnectés, vous serez envoyés vers la page de connexion. CLiquez alors sur le bouton S'inscrire Cliquez alors votre email ainsi que votre mot de passe.
Validez ; le tour est joué !

vous pouvez désormais vous connecter sur la page /login.

## Serveurs

### Liste des serveurs
Vous pouvez consulter une liste de tous vos serveurs depuis la page "liste" dans le menu "serveurs" de la sidebar.  
Vous y retrouverez l'ensemble des serveurs auxquels vous avez accès. 

### Ajouter un serveur
Juste en dessous du bouton liste se situe le bouton "Ajouter". il vous dirigera vers la page de création d'un serveur.  

Vous devrez alors vous fournir :
- un nom 
- l'hôte
- le dossier raçine (celui ou se trouve les sites hébergés)
- le nom d'utilisateur
- le mot de passe

Une fois crée, vous pourrez le retrouver dans liste des serveurs.

## Sites

### Liste des sites
Vous pouvez consulter une liste de tous vos sites depuis la page "liste" dans le menu "sites" de la sidebar.  
Vous y retrouverez l'ensemble des sites auxquels vous avez accès. 

## Groupes
Le groupe est le système d'organisation implementé par cette application.  

### Création d'un groupe
Allez sur la page d'ajout ("Ajouter" dans l'onglet "Groupes" de la sidebar)  
Renseignez son nom, assignez lui des utilisateurs et des serveurs, puis validez.

### Utilisateurs et serveurs du groupe
Lors de la création d'un groupe, ou de sa modification, vous avez la posibilité de rajouter / supprimer des utilisateurs et des serveurs.

L'ajout d'un serveur au groupe permetra à chacun de ses utilisateurs d'y avoir un accès <u>complet</u>

## Recherche
Il existe plusieurs manières de rechercher (des sites / serveurs) sur l'application :

- sur la sidebar se trouve une partie "Recherche instantanée" : sélectionnez ce que vous cherchez et trouvez le en un instant !
- sur les pages liste (des sites / serveurs), vous pouvez rechercher en haut à droite sur la liste actuelle.

## Sécurité
L'application utilise les normes actuelles de sécurité, l'ensemble des données personelles sont encryptées avec [paragonie/halite](https://github.com/paragonie/halite).

de plus, nous utilisons toutes les méthodes de sécurisations fournies par Symfony, comme les jetons crsf, ou la protection contre les injections SQL grâce à Doctrine...

voir [Symfony/Security](https://symfony.com/doc/current/security.html)