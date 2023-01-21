# e-commerce
Un petit site e-commerce

## Fonctionnalités

#### utilisateur
- lister les produits avec pagination
- voir la page produit
- mettre un produit au panier
- consulter le panier

#### client
- role utilisateur
- créer un compte
- se connecter 
- acheter un produit
- consulter votre profile : modifier votre compte, supprimer votre compte, voir l'historique de vos commandes

#### interface administrateur
- role client
- gerer les produits
- gerer les commandes
- gerer les lignes de commandes
- gerer les requetes de paiement
- gerer les clients

## Librairie et bundle externe
- webpack encore
- bootstrap 
- bootstrap-icon
- strip
- fixtures
- pagination

## Utilisation
- télécharger/cloner le projet
- installer les dépendances 
    - composer install
    - yarn install
- mettre à jour et remplir la base de données
    - symfony console d:s:u --force
    - symfony console d:f:l
- lancé le server
    - symfony serve -d
    - yarn dev-server
-  → Modifier le projet a votre guise
