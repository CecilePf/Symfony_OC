<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// On utilise la requête pour récupérer les paramètres de l'URL
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//use pour redirection
use Symfony\Component\HttpFoundation\RedirectResponse;
// use pour return JSON
use Symfony\Component\HttpFoundation\JsonResponse;

// Le contrôleur doit tjs renvoyer un objet Response. new Response(), renderResponse() ou render() (raccourci)
class AdvertController extends Controller
{
  //TEST
  public function testAction()
  {
    return $this->render('OCPlatformBundle:Advert:test.html.twig');
  }

  // INDEX
  public function indexAction($page)
  {
    if ($page < 1) {
    // On déclenche une exception NotFoundHttpException, cela va afficher
      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }
      $listAdverts = array(
      array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => 1,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );
      return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
  'listAdverts' => $listAdverts
  ));
  }

  // VIEW
  public function viewAction($id, Request $request)
  {
    // $id vaut 5 si l'on a appelé l'URL /platform/advert/5

    // Ici, on récupèrera depuis la base de données
    // l'annonce correspondant à l'id $id.
    // Puis on passera l'annonce à la vue pour
    // qu'elle puisse l'afficher

    //return new Response("Affichage de l'annonce d'id : ".$id);

    // $content = $this->get('templating')->render('OCPlatformBundle:Advert:view.html.twig', array('id' => $id));
    // return new Response($content);

    //-------------------------------

    // RECUPERER PARAMETRES URL
    // Pas besoin de tester l'existence du paramètre : s'il n'existe pas, retourne chaîne vide
    // $tag = $request->query->get('tag');
    // return new Response("Affichage de l'annonce id : " .$id. ", avec le tag '" .$tag. "'.");

    // On utilise le raccourci : il crée un objet Response
    // Et lui donne comme contenu le contenu du template
    // return $this->get('templating')->renderResponse(
    //   'OCPlatformBundle:Advert:view.html.twig',
    //   array('id'  => $id, 'tag' => $tag)
    // );

    // Ou
    // return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
    //   'id'  => $id,
    //   'tag' => $tag,
    // ));

    // return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
    //   'id' => $id
    // ));

    $advert = array(
      'title'   => 'Recherche développpeur Symfony2',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert' => $advert
    ));
    //-------------------------------

    // SESSION
    // Récupération de la session
    // $session = $request->getSession();
    // // On récupère le contenu de la variable user_id
    // $userId = $session->get('user_id');
    // // On définit une nouvelle valeur pour cette variable user_id
    // $session->set('user_id', 91);

    //-------------------------------

    // REDIRECTION
    //$url = $this->get('router')->generate('oc_platform_home');
    //return new RedirectResponse($url);
    // Ou plus court :
    // return $this->redirectToRoute('oc_platform_home');

    //-------------------------------

    // RENVOYER DU JSON
    // Créons nous-mêmes la réponse en JSON, grâce à la fonction json_encode()
    // $response = new Response(json_encode(array('id' => $id)));
    // Ici, nous définissons le Content-type pour dire au navigateur
    // que l'on renvoie du JSON et non du HTML
    // $response->headers->set('Content-Type', 'application/json');
    // return $response;
    // OU
    // return new JsonResponse(array('id' => $id));
  }

  // ADD : Page http://localhost/PHP/Symfony/Symfony/web/app_dev.php/platform/add
  public function addAction(Request $request)
  {
    // La gestion d'un formulaire est particulière, mais l'idée est la suivante :

    // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
    if ($request->isMethod('POST')) {
      // Ici, on s'occupera de la création et de la gestion du formulaire

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // Puis on redirige vers la page de visualisation de cettte annonce
      return $this->redirectToRoute('oc_platform_view', array('id' => 5));
    }

    // Si on n'est pas en POST, alors on affiche le formulaire
    return $this->render('OCPlatformBundle:Advert:add.html.twig');
  }

  public function editAction($id, Request $request)
  {
    $advert = array(
      'title'   => 'Recherche développpeur Symfony',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

  public function deleteAction($id) {
    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }

  public function menuAction($limit)
  {
    // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
    $listAdverts = array(
      array('id' => 2, 'title' => 'Recherche développeur Symfony'),
      array('id' => 5, 'title' => 'Mission de webmaster'),
      array('id' => 9, 'title' => 'Offre de stage webdesigner')
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }

}