<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Entity\Bulletin;
use App\Form\BulletinType;
use App\Repository\BulletinRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        //Afin de pouvoir dialoguer avec notre base de données, nous avons besoin de l'Entity Manager
        $entityManager = $this->getDoctrine()->getManager();
        // ensuite, nous créons un nouveau bulletin que nous lions à notre formulaire$bulletin = new Bulletin;
        $bulletin = new Bulletin;

        $bulletinForm = $this->createForm(\App\Form\BulletinType::class, $bulletin);
        // NOus transmetons la requête client à notre formulaire
        $bulletinForm->handleRequest($request);
        // Si le formulaire a été validé
        if ($request->isMethod('post') && $bulletinForm->isValid()) {
            // handleRequest ayant passé les infos à notre objet bulletin, nous avons juste à le persister
            $entityManager->persist($bulletin);

            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }

        //Nous récupérons le Repository de l'Entity qui nous intéresse, à savoir, Bulletin
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        //Nous récupérons la totalité des entrées de la table Bulletin, sous forme d'un tableau d'instances de classe Bulletin
        $bulletins = $bulletinRepository->findAll();
        $bulletins = array_reverse($bulletins); //Nous utilisons PHP pour inverser l'ordre de notre tableau

        //Nous envoyons notre bulletin tel quel à la page index.html.twig
        return $this->render('index/index.html.twig', [ //FIXME
            // 'categories' => $bulletinRepository->findEachCategory(),
            'bulletins' => $bulletins,
            'formName' => 'Création de bulletin',
            'dataForm' => $bulletinForm->createView()
        ]);
    }

    #[Route('/cheatsheet', name: 'index_cheatsheet')]
    public function cheatsheet(): Response
    {
        return $this->render('index/cheatsheet.html.twig');
    }

    // #[Route('/category/{category}', name: 'bulletin_category')]
    // public function indexCategory(Request $request, $category = false): Response
    // {
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $bulletinRepository = $entityManager->getRepository(Bulletin::class);
    //     // $categories = $bulletinRepository->findEachCategory();
    //     //La fonction findEachCategory rend un tableau de tableaux

    //     // $categorizedBulletins = $bulletinRepository->findBy(['category' => $categories]);

    //     // return $this->render('index/dump.html.twig', ['variable' => $categories]);
    //     // if (!$categorizedBulletins) {
    //     //     return $this->redirect($this->generateUrl('index'));
    //     // }

    //     // $bulletins = array_reverse($categorizedBulletins);
    //     return $this->render('index/category.html.twig', [
    //         'bulletins' => $bulletins,
    //         // 'categories' => $bulletinRepository->findEachCategory(),
    //         'formName' => 'Création de bulletin',
    //         // 'dataForm' => $bulletinForm->createView()
    //     ]);
    // }

    #[Route('/tag/{tagId}', name: 'index_tag')]
    public function indexTag(Request $request, $tagId = false): Response
    {
        // Cette fonction a pour but d'afficher la totalité des bulletins associés à un certain Tag
        $entityManager = $this->getDoctrine()->getManager();
        $TagRepository = $entityManager->getRepository(Tag::class);
        // Nous récupérons le Tag dont l'id est spécifié par notre URL
        $tag = $TagRepository->find($tagId);
        // Si aucun résultat n'est spécifié, nous retournons à l'index
        if (!$tag) {
            return $this->redirect($this->generateUrl('index'));
        }
        // return $this->render('index/dump.html.twig', ['variable' => $tag]);
        // Nous transmettons la liste des Bulletins liés au Tag à la vue Twig
        return $this->render('index/index.html.twig', [
            'bulletins' => $tag->getBulletins()
        ]);
    }

    #[Route('/bulletin/create', name: 'bulletin_create')]
    public function bulletinCreate(Request $request): Response
    {
        // Cete fonction nous servira à afficher un formulaire capable de créer un nouveau bulletin
        // Tout d'abord, nous appelons l'Entity Manager pour communiquer avec norre BDD
        $entityManager = $this->getDoctrine()->getManager();
        // ensuite, nous créons un nouveau bulletin que nous lions à notre formulaire$bulletin = new Bulletin;
        $bulletin = new Bulletin;
        $bulletinForm = $this->createForm(\App\Form\BulletinType::class, $bulletin);
        // NOus transmetons la requête client à notre formulaire
        $bulletinForm->handleRequest($request);
        // Si le formulaire a été validé
        if ($request->isMethod('post') && $bulletinForm->isValid()) {
            // handleRequest ayant passé les infos à notre objet bulletin, nous avons juste à le persisterO
            $entityManager = $this->getDoctrine()->getManager();
            $bulletinRepository = $entityManager->getRepository(Bulletin::class);
            $bulletinDuplicate = $bulletinRepository->findOneBy(['title' => $bulletin->getTitle()]);
            if (!$bulletinDuplicate) {
                $entityManager->persist($bulletin);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('index'));
            } else {
                return new Response("<h1>Opération impossible. Ce titre existe déjà dans la base de données.</h1>");
            }
        }
        // Si le formulaire n'a pas été validé, nous l'affichons pour l'utilisateur
        return $this->render('index/dataform.html.twig', [
            'formName' => 'Création de bulletin',
            'dataForm' => $bulletinForm->createView() // createView prépare l'affichage de notre formulaire
        ]);
    }

    #[Route('/bulletin/display/{bulletinId}', name: 'bulletin_display')]
    public function bulletinDisplay(Request $request, $bulletinId = false)
    {
        //Cette fonction a pour but de n'afficher qu'un seul bulletin, dont l'id correspond au paramètre de route
        //Tout d'abord, nous devons récupérer l'Entity Manager et le Repository nécessaire à notre recherche
        $entityManager = $this->getDoctrine()->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        //bulletinRepository nous sera utile pour effectuer une recherche dans notre table SQL "bulletin"
        $bulletin = $bulletinRepository->find($bulletinId);
        //Soit l'ID existe, et un objet Bulletin est instancié avec les informations de l'entrée récupérée
        //Soit l'ID n'existe pas, et $bulletin vaut null
        //Si le bulletin n'est pas trouvé, nous mettons prématurément fin à notre fonction
        if (!$bulletin) {
            return $this->redirect($this->generateUrl('index'));
        }
        //Etant donné que notre page index.html.twig lit des tableaux de Bulletin, nous rangeons notre variable $bulletin dans un tableau. Il ne s'agira d'un tableau qu'à une seule entrée et donc un seul bulletin sera présenté à l'utilisateur
        $bulletins = [$bulletin];
        //A présent, il faut envoyer ce tableau vers notre page twig
        return $this->render('index/index.html.twig', [
            // 'categories' => $bulletinRepository->findEachCategory(),
            'bulletins' => $bulletins,
        ]);
    }

    #[Route('/bulletin/update/{bulletinId}', name: 'bulletin_update')]
    public function bulletinUpdate(BulletinRepository $bulletinRepository, Request $request, $bulletinId = false): Response
    {
        // Cette fonction a pour but de récupérer un bulletin et d'en modifier le contenu
        // Nous commençons par récuperer l'Entity Manager et le Repository de Bulletin
        $entityManager = $this->getDoctrine()->getManager();
        // $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        // NOus récupérons le bulletin dont l'Id nous a été communiqué
        $bulletin = $bulletinRepository->find($bulletinId);
        // Si aucun bulletin n'est retrouvé, nous retournons vers l'index
        if (!$bulletin) {
            return $this->redirect($this->generateUrl('index'));
        }
        // Si un bulletin existe, la fonction continue et nous lions ce bulletin à un formulaire
        // Etant donné que $bulletin est déjà renseigné, les champs du formulaire seront préremplis
        $bulletinForm = $this->createForm(\App\Form\BulletinType::class, $bulletin);
        // NOus appliquons la Request si celle ci est pertinente
        $bulletinForm->handleRequest($request);
        // Si notre bulletin est valide et rempli, nous l'envoyons vers notre BDD
        if ($request->isMethod('post') && $bulletinForm->isValid()) {

            // NOus vérifions s'il existe un bulletin au même titre EN PLUS de notre bulletin
            // Nous récupérons le nom de notre bulletin actuel
            // Nous récupérons tous les bulletins dont le titre est identique
            $bulletins = $bulletinRepository->findBy(['title' => $bulletin->getTitle()]);
            // return $this->render('index/dump.html.twig', ['variable' => $bulletins]);
            $entityManager->persist($bulletin);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }
        // Si le bulletin n'a pas été rempli, nous affichons le formulaire
        return $this->render('index/dataform.html.twig', [
            'formName' => 'Création de bulletin',
            'dataForm' => $bulletinForm->createView() // createView prépare l'affichage de notre formulaire
        ]);
    }

    #[Route('/bulletin/delete/{bulletinId}', name: 'bulletin_delete')]
    public function bulletinDelete(Request $request, $bulletinId = false)
    {
        //Cette fonction a pour but de supprimer un bulletin, dont l'id correspond au paramètre de route
        //Tout d'abord, nous devons récupérer l'Entity Manager et le Repository nécessaire à notre recherche
        $entityManager = $this->getDoctrine()->getManager();
        $bulletinRepository = $entityManager->getRepository(Bulletin::class);
        //bulletinRepository nous sera utile pour effectuer une recherche dans notre table SQL "bulletin"
        $bulletin = $bulletinRepository->find($bulletinId);
        //Soit l'ID existe, et un objet Bulletin est instancié avec les informations de l'entrée récupérée
        //Soit l'ID n'existe pas, et $bulletin vaut null
        //Si le bulletin n'est pas trouvé, nous mettons prématurément fin à notre fonction
        if (!$bulletin) {
            return $this->redirect($this->generateUrl('index'));
        }
        //Nous procédons à la suppression du bulletin
        $entityManager->remove($bulletin);
        $entityManager->flush();
        //Nous retournons vers l'index
        return $this->redirect($this->generateUrl('index'));
    }

    #[Route('/tag/create', name: 'tag_create')]
    public function TagCreate(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        // Nous créons notre formulaire
        $tag = new Tag;
        $tagForm = $this->createForm(\App\Form\TagType::class, $tag);
        // Nous transmettons la requête client à notre formulaire et nous le validons
        $tagForm->handleRequest($request);
        // Si le formulaire a été validé
        if ($request->isMethod('post') && $tagForm->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }
        // Si le formulaire n'a pas été validé, nous l'affichons pour l'utilisateur
        return $this->render('index/dataform.html.twig', [
            'formName' => 'Création de tag',
            'dataForm' => $tagForm->createView() // createView prépare l'affichage de notre formulaire
        ]);
    }

    #[Route('/article-generate', name: 'article_generate')]
    public function generateArticle()
    {
        //Cette fonction génère un article/bulletin avant de rediriger le serveur vers la fonction index()
        //Tout d'abord, nous appelons l'Entity Manager dont nous allons avoir besoin plus tard
        $entityManager = $this->getDoctrine()->getManager();
        //Ensuite, nous initialisons les différentes listes de chaines de caractères utilisées par notre Bulletin
        //Ces listes se composent de propositions de contenu afin de de préparer un Bulletin original
        $bulletinCategories = ['General', 'Divers', 'Urgent'];
        $bulletinTitles = ['Rouge', 'Bleu', 'Jaune', 'Vert', 'Noir', 'Blanc', 'Gris', 'Cyan', 'Rose', 'Magenta', 'Turquoise', 'Fuschia', 'Orange', 'Marron', 'Violet'];
        //Nous créons et renseignons notre nouveau Bulletin
        $bulletin = new Bulletin;
        $bulletin->setTitle($bulletinTitles[rand(0, (count($bulletinTitles) - 1))] . ' #' . rand(1, 999));
        $bulletin->setCategory($bulletinCategories[rand(0, (count($bulletinCategories) - 1))]);
        $bulletin->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        //Nous pouvons faire notre demande de persistance pour le Bulletin à présent qu'il est renseigné
        $entityManager->persist($bulletin);
        $entityManager->flush();
        //Nous renvoyons à présent le serveur vers la fonction index() de notre Controller
        return $this->redirect($this->generateUrl('index'));
    }

    #[Route('/square/{response}', name: 'square')]
    public function squareResponse(Request $request, $response = "carré"): Response
    {
        // La valeur par défaut "carré" fait en sorte que $Response possède toujours une valeur. Ainsi, nous pouvons à présent accéder à notre fonction squareResponse même si nous ne marquons rien après "/square"
        switch ($response) {
            case 'carré':
                $response = 'square';
                $br = 0;
                break;
            case 'cercle':
                $response = 'circle';
                $br = 50;
                break;
        }

        $i = rand(1, 6);
        $i2 = rand(1, 6);
        $colors = [
            1 => str_replace("'", "", 'red'),
            2 => str_replace("'", "", 'blue'),
            3 => str_replace("'", "", 'green'),
            4 => str_replace("'", "", 'yellow'),
            5 => str_replace("'", "", 'violet'),
            6 => str_replace("'", "", 'purple'),
        ];
        // Nous retournons un carré coloré en réponse
        return new Response(
            "<div style='display: flex; justify-content: space-evenly; margin-top: 10%'>
            <div>
            <h1>This really is quite a <span style='color: {$colors[$i]}'>{$colors[$i]}</span> {$response}!</h1>
            <div style='width: 300px; height: 300px; border-radius: {$br}%; background-color: {$colors[$i]};'></div>
            </div>
            <div>
            <h1>This one is more <span style='color: {$colors[$i2]}'>{$colors[$i2]}</span>-ish...</h1>
            <div style='width: 300px; height: 300px; border-radius: {$br}%; background-color: {$colors[$i2]};'></div>
            </div>
            </div>"
        );
    }

    #[Route('/square-display/display', name: 'square_display')]
    public function squareDisplay(): Response
    {
        // Attention: il faut toujours prendre soin de nommer ses fonctions différemment des fonctions à segment dynamique d'URL. Nommer l'URL (adresse) de notre route ici "square/display" activera la fonction squareResponse à la place du fait du même début d'adresse et la partie différente "display" sera simplement enregistrée comme valeur de {response} au dessus. IL faut toujours faire attention à bien nommer ses URL, et choisir un début unique et caractéristique pour chaque route possédant un segment dynamique d'URL.
        return new Response("Display");
    }

    #[Route('/square-color/{color}', name: 'square_color')]
    public function squareColor(Request $request, $color = false): Response
    {
        switch ($color) {
            case 'rouge':
                $color = 'red';
                break;
            case 'vert':
                $color = 'green';
                break;
            case 'jaune':
                $color = 'yellow';
                break;
            case 'bleu':
                $color = 'blue';
                break;
            case false:
                $color = 'gray';
                break;
            default:
                $color = 'black';
                break;
        };
        return new Response(
            "<div style='width: 300px; height: 300px; background-color: {$color};'></div>"
        );
    }

    /* Fonctions inutilisées
 * 

    /**
     * @Route("/", name="index")
     *
    public function index(Request $request): Response
    {
        //Nous créons un tableau censé accueillir les bulletins que nous allons créer
        $bulletins = [];
        //Nous insérons nos instructions de création d'un bulletin à l'intérieur d'une boucle
        //Pour chaque tour de la boucle, le nouveau bulletin instancié sera ajouté à notre tableau $bulletins
        
        for($i = 0; $i < 10; $i++){
            //Nous créons un nouveau bulletin
            $bulletin = new Bulletin;
            //Nous pouvons obtenir la date de création du bulletin en récupérant son objet DateTime contenu par l'attribut $creationDate et l'afficher via un return si nous le désirons
            //return new Response($bulletin->getCreationDate()->format('G:i:s'));
            //A présent, nous allons renseigner les autres attributs (sauf id qui est automatiquement pris en charge par Doctrine)
            $bulletin->setTitle('#0' . $i . ' Rouge');
            $bulletin->setCategory('General');
            $bulletin->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
            //array_push est une fonction PHP qui permet d'ajouter au tableau (1) la valeur (2)
            array_push($bulletins, $bulletin);
        }

        
        //Nous envoyons notre bulletin tel quel à la page index.html.twig
        return $this->render('index/index.html.twig', [
            'bulletins' => $bulletins,
        ]);
    }
*/

    #[Route('/cards', name: 'cards')]
    public function cards(): Response
    {
        // Peut executer plusieurs instructions avant de renvoyer une réponse via le return
        return $this->render('index/index.html.twig', [
            'controller_name' => [
                1 => 'Michel',
                2 => 'Julie',
                3 => 'Matthieu'
            ]
        ]);
    }

    #[Route('/test/{name}', name: 'test')]
    public function test(Request $request, $name): Response
    {

        return new Response("Hello {$name}");
    }
}
