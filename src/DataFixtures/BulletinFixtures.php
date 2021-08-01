<?php

namespace App\DataFixtures;

use App\Entity\Bulletin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BulletinFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Nous allons placer dans notre BDD 15 Bulletin
        //Chaque bulletin doit avoir un nom original
        //Chaque bulletin doit appartenir au hasard à une catégorie de type "General", "Divers", et "Urgent"
        //Le contenu (content) de nos bulletins peut rester le même
        //Notre code doit être modulable
        
        //Nous créons une variable conservant le contenu identique des bulletins afin d'améliorer la lisibilité du code
        $bulletinContent = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
        //Nous créons une liste des différentes catégories susceptibles de contenir nos bulletins
        //En créant une liste de catégories dès maintenant, nous pouvons facilement ajouter, supprimer ou modifier des noms possibles de catégories pour nos bulletins
        //Une chaine de caractères est une chaine de caractères, peu importe sa signification. Il importe donc de faciliter autant que possible la manipulation de cette chaine car sa nature ne changera pas
        $bulletinCategories = ['General', 'Divers', 'Urgent'];
        //Nous créons aussi une liste de titres possibles pour nos bulletins
        $bulletinTitres = ['Rouge', 'Bleu', 'Jaune', 'Vert', 'Noir', 'Blanc', 'Gris', 'Cyan', 'Rose', 'Magenta', 'Turquoise', 'Fuschia', 'Orange', 'Marron', 'Violet'];
        
        //Nous allons à présent créer une boucle qui sélectionnera un élément de chaque attribut de notre Bulletin et concluera par une requête de persistance envers l'Entity Manager
        for($i = 0; $i < 15; $i++){
            //Nous plaçons un nouveau bulletin au sein de la variable $bulletin
            $bulletin = new Bulletin;
            //Nous renseignons les attributs de ce nouveau Bulletin
            //rand(min, max) est une fonction prédéfinie PHP laquelle rend un entier situé entre la valeur minimum et maximum incluses. La valeur minimum ici est 0, ce qui correspond à l'indice initial de notre tableau.
            //count($array) est une fonction prédéfinie PHP, laquelle nous rend le nombre d'entrées d'un tableau
            //Ainsi, la valeur count($array) - 1 correspond au dernier indice du tableau en question étant donné que la numérotation part de zéro. Ecrire rand(0, (count($array) - 1)) revient à obtenir une valeur au hasard parmi tous les indices possibles de notre tableau
            $bulletin->setTitle($bulletinTitres[rand(0,(count($bulletinTitres) - 1))] . ' #' . rand(1,999));
            $bulletin->setCategory($bulletinCategories[rand(0,(count($bulletinCategories) - 1))]);
            $bulletin->setContent($bulletinContent);
            //Une fois que nous avons renseigné tous les éléments du Bulletin de cette boucle, nous effectuons la demande de persistance
            $manager->persist($bulletin);
            //La boucle soit repart avec un nouveau Bulletin enregistré dans notre variable Bulletin ou prend fin
        }
        //Une fois que la boucle a pris fin, nous exécutons toutes les demandes de persistance que nous avons enregistrées au sein de cette dernière
        $manager->flush();
    }
    
    
    public function loadSingle(ObjectManager $manager)
    {
        //Nous allons ici créer notre objet Bulletin
        //Après avoir créé un objet Bulletin, nous importons la classe via le bouton "Import All Classes"
        $bulletin = new Bulletin;
        //Nous allons ensuite renseigner ses attributs
        $bulletin->setTitle('Rouge');
        $bulletin->setCategory('General');
        $bulletin->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        //Nous allons enfin le transférer au sein de notre base de données
        $manager->persist($bulletin);
        //Nous appliquons la demande de persistance avec la fonction flush()
        $manager->flush();
    }
}
