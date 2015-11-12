# Euro16_API



## PHPUnit

Utiliser la version 4.8 de PHPUnit

- Installer php 5.5

        WINDOWS : http://windows.php.net/qa/
    
        MAC : > brew install php56
    
- Télécharger PHPUnit en choisissant la version 4.5 (old stable)

        https://phar.phpunit.de/phpunit.phar

### Ligne de commande 
    
- L'archive est directement utilisable comme un executable (chmod +x est peut-être néceéssaire). Il est conseillé de :
        
        - Copier l'archive dans un dossier phpunit
        - Renommer l'archive par "phpunit"
        - Ajouter le dossier phpunit au PATH

- PHPUnit s'execute alors simplement :

        > cd <racine_du_projet>
        > phpunit
        
- A noter que phpunit utilise le fichier de configuration phpunit.xml à la racine du projet, qui indique pour le moment d'executer tous les tests.



### Intellij
    
- Installer le plugin "php"

        Intellij | Preferences | Plugin | Browse repositories
        "PHP"

- Définir le projet comme un projet PHP

        File | Project Structure | Module
        Supprimer le module existant
        Creer un nouveau module PHP et "Empty PHP Project"
        Choisir la racine du projet
        Choisir 5.6 pour "PHP Language Level"
        Choisir PHP 5.6 pour "Interpreter"
        
- Configurer PHPUnit sur Intellij

        Intellij | Preferences | PHP | PHPUnit
        Choisir "Path to phpunit.phar"
        Pointer sur le phpunit.phar téléchargé précédemment
        
- Pour utiliser PHPUnit, il suffit de faire clic droit sur une méthode ou une classe de test, et de choisir "Run [...]"
         

### Eclipse

- (TODO: Expliquer procédure pour plugin Eclipse)

        TODO
    
***

## Composer

Composer permet de gérer les différentes dépendences du projet

- Installer composer

        curl -sS https://getcomposer.org/installer | php
        
- L'archive est directement utilisable comme un executable. Il est conseillé de :

        - Copier l'archive dans un dossier composer
        - Renommer l'archive par "composer"
        - Ajouter le dossier composer au PATH
        
- PHPUnit s'execute alors simplement :

        > cd <racine_du_projet>
        > composer install
        
- A noter que composer utilise le fichier de configuration composer.json à la racine du projet, qui indique les dépendances à télécharger (dans le dossier ./vendor)

***

## Environnements dev/test/prod

- Penser à adapter le fichier index.php suivant l'environnement de travail

- Pour l'environnement de dev

        $api = new Controller("dev");
        
- Pour l'environnement de prod

        $api = new Controller("prod");
        
- Il faut également prévoir les bases de données mysql correspondantes 

        euro16_test : utilisée lors des tests unitaires avec PHPUnit
        euro16_dev : utilisée pour tester l'appli avec une BDD locale
        euro16_prod : uniquement utilisée par l'environnement de prod (donc pas besoin d'être créee localement)