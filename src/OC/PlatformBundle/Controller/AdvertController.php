<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// On utilise la requête pour récupérer les paramètres de l'URL
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

//use pour redirection
use Symfony\Component\HttpFoundation\RedirectResponse;
// use pour return JSON
use Symfony\Component\HttpFoundation\JsonResponse;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;

use OC\PlatformBundle\Entity\User;

// uses pour formulaire
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;

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

    // Ici je fixe le nombre d'annonces par page à 3
    $nbPerPage = $this->container->getParameter('nb_per_page');

    // On récupère notre objet Paginator
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
  'listAdverts' => $listAdverts,
  'nbPages' => $nbPages,
  'page' => $page,
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

    //----------------------------------

    // $repository = $this->getDoctrine()
    // ->getManager()
    // ->getRepository('OCPlatformBundle:Advert');

    // On récupère l'entité correspondante
    // $advert = $repository->find($id);

    // Ou avec la méthode find de l'EntityManager (et non du repository)
    // $advert = $this->getDoctrine()
    //   ->getManager()
    //   ->find('OCPlatformBundle:Advert', $id)
    // ;

    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if ($advert == null) {
      throw new NotFoundHttpException("L'annonce avec l'id " .$id. " n'existe pas.");
    }

    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert));

    $listSkills = $em
      ->getRepository('OCPlatformBundle:AdvertSkill')
      ->findBy(array('advert' => $advert));

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array('advert' => $advert, 'listApplications' => $listApplications, 'listSkills' => $listSkills));
  }

  // ADD : Page http://localhost/PHP/Symfony/Symfony/web/app_dev.php/platform/add
  ///**
 // * @Security("has_role('ROLE_AUTEUR')")
 // */
  public function addAction(Request $request)
  {
    // La gestion d'un formulaire est particulière, mais l'idée est la suivante :

    // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
    // if ($request->isMethod('POST')) {
    //   // Ici, on s'occupera de la création et de la gestion du formulaire

    //   $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

    //   // Puis on redirige vers la page de visualisation de cettte annonce
    //   return $this->redirectToRoute('oc_platform_view', array('id' => 5));
    // }

    // // Si on n'est pas en POST, alors on affiche le formulaire
    // return $this->render('OCPlatformBundle:Advert:add.html.twig');

    // --------------------------
    // UTILISATION D'UN SERVICE
    // On récupère le service
    // $antispam = $this->container->get('oc_platform.antispam');

    // Je pars du principe que $text contient le texte d'un message quelconque
    // $text = '...';
    // if ($antispam->isSpam($text)) {
    //   throw new \Exception('Votre message a été détecté comme spam !');
    // }

    // Ici le message n'est pas un spam
    // --------------------------


    // ENTITIES
    // Création de l'entité Advert
    // $advert = new Advert();
    // $advert->setTitle('Dev Web');
    // $advert->setAuthor('Jean');
    // $advert->setContent("Recherche développeur web expérimenté.");
    // On peut ne pas définir ni la date ni la publication,
    // car ces attributs sont définis automatiquement dans le constructeur

    // Création de l'entité Image
    // $image = new Image();
    // $image->setUrl('https://www.annei.fr/wp-content/uploads/2016/08/photo-1429051883746-afd9d56fbdaf.jpg');
    // $image->setAlt('dev-web');

    // Lie l'image à l'annonce
    // $advert->setImage($image);

    // Création d'une première candidature
    // $application1 = new Application();
    // $application1->setAuthor('Martin');
    // $application1->setContent("Moimoimoi");

    // Création d'une deuxième candidature par exemple
    // $application2 = new Application();
    // $application2->setAuthor('Pierre');
    // $application2->setContent("Je suis très motivé.");

    // On lie les candidatures à l'annonce
    // $application1->setAdvert($advert);
    // $application2->setAdvert($advert);

    // ------------------------------

    // On récupère l'EntityManager
    // $em = $this->getDoctrine()->getManager();

    // On récupère toutes les compétences possibles
    // $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

    // // Pour chaque compétence
    // foreach ($listSkills as $skill) {
    //   // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
    //   $advertSkill = new AdvertSkill();

    //   // On la lie à l'annonce, qui est ici toujours la même
    //   $advertSkill->setAdvert($advert);
    //   // On la lie à la compétence, qui change ici dans la boucle foreach
    //   $advertSkill->setSkill($skill);

    //   // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
    //   $advertSkill->setLevel('Expert');

    //   // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
    //   $em->persist($advertSkill);

    // Étape 1 : On « persiste » l'entité
    // à partir de là, cette entité est gérée par Doctrine
    // $em->persist($advert);

    // Étape 1 bis : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    // $em->persist($application1);
    // $em->persist($application2);

    // Étape 2 : On « flush » tout ce qui a été persisté avant
    // On dit à Doctrine d'exécuter les requêtes pour sauvegarder les entités en BDD
    // (INSERT INTO...)
    // $em->flush();

    // Reste de la méthode qu'on avait déjà écrit
    // if ($request->isMethod('POST')) {
    //   $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

    //   // Puis on redirige vers la page de visualisation de cettte annonce
    //   // On utiliser $advert->getId() car Doctrine a attribué un id grâce au flush
    //   return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    // }
    // // Si on n'est pas en POST, alors on affiche le formulaire
    // return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));

    $advert = new Advert();

    $form = $this->createForm(AdvertType::class, $advert);
    // OU $form = $this->get('form.factory')->create(AdvertType::class, $advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // On redirige vers la page de l'annonce créée
      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce avec l'id " .$id. " n'existe pas.");
    }

    $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert,
      'form' => $form->createView(),
    ));
  }

  public function deleteAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->get('form.factory')->create();

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirectToRoute('oc_platform_home');
    }

    return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView(),
    ));
  }

  public function menuAction($limit)
  {
    $em = $this->getDoctrine()->getManager();

    $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
      array(),                 // Pas de critère
      array('date' => 'desc'), // On trie par date décroissante
      $limit,                  // On sélectionne $limit annonces
      0                        // À partir du premier
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }

}