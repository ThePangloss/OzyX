<?php

// src/ozyx/PlatformBundle/Controller/AdvertController.php

namespace ozyx\PlatformBundle\Controller;

use ozyx\PlatformBundle\Entity\Advert;
use ozyx\PlatformBundle\Entity\Image;
use ozyx\PlatformBundle\Entity\Application;
use ozyx\PlatformBundle\Entity\AdvertSkill;
use ozyx\PlatformBundle\Form\AdvertType;
use ozyx\PlatformBundle\Form\AdvertEditType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AdvertController extends Controller
{

  public function menuAction($limit = 3)
  {
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('ozyxPlatformBundle:Advert')
      ->findBy(
        array(),                 // Pas de critère
        array('date' => 'desc'), // On trie par date décroissante
        $limit,                  // On sélectionne $limit annonces
        0                        // À partir du premier
    );

    return $this->render('ozyxPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }

  public function indexAction($page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // Ici je fixe le nombre d'annonces par page à 3
    // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
    $nbPerPage = 3;

    // Pour récupérer la liste de toutes les annonces : on utilise findAll()
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('ozyxPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts)/$nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // On donne toutes les informations nécessaires à la vue
    return $this->render('ozyxPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,
    ));
  }

  public function viewAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em
      ->getRepository('ozyxPlatformBundle:Advert')
      ->find($id)
    ;

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }
    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em
      ->getRepository('ozyxPlatformBundle:Application')
      ->findBy(array('advert' => $advert))
    ;

    // On récupère maintenant la liste des AdvertSkill
    $listAdvertSkills = $em
      ->getRepository('ozyxPlatformBundle:AdvertSkill')
      ->findByAdvert($advert)
    ;

    //Test d'une var
    //$path =  'bundles/ozyxplatform/images/'.$advert->getImage()->getImageName();

    return $this->render('ozyxPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  public function addAction(Request $request)
  {
     // On crée un objet Advert
    $advert = new Advert();

    $formBuilder = $this->createForm(AdvertType::class, $advert);

    $ipClient = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();

    if (($formBuilder->handleRequest($request)->isValid()) && !empty($ipClient))  {
  
      $advert->setDate(new \Datetime());
      $advert->setIp($ipClient);

      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();
            
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

    /*
    return $this->render('ozyxPlatformBundle:Advert:add.html.twig', array(
      'formBuilder' => $formBuilder->createView(),
      'test'        => $test
    ));
    */
      return $this->redirect($this->generateUrl('ozyx_platform_view', array('id' => $advert->getId())));
    }

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('ozyxPlatformBundle:Advert:add.html.twig', array(
      'formBuilder' => $formBuilder->createView()/*,
      'isFlood'     => $isFlood*/
    ));
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    // On récupère l'annonce $id
    $advert = $em->getRepository('ozyxPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $formBuilder = $this->createForm(AdvertEditType::class, $advert);

    $ipClient = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();

    if ($formBuilder->handleRequest($request)->isValid()) {
    /*
   
      $path =  'ozyx/src/ozyx/PlatformBundle/Resources/public/images'.$advert->getImage()->getImageCachePath();
      //__DIR__.'/../../../../web/'.

      $filter           = 'cache';
      $filterStrip      = 'stripImage';
      $filterMiniature  = 'miniature';

      //A tester
      $cacheManager = $this->get('liip_imagine.cache.manager');
      $srcPath = $cacheManager->getBrowserPath($path, $filterStrip);
      $cacheManager->resolve($this->container->get('request_stack')->getCurrentRequest(), $path, $filterStrip);
      $cacheManager->remove($srcPath, $filterStrip);
      //
   $cacheManager = $this->container->get('liip_imagine.cache.manager');
      // Remove the cached image corresponding to that path & filter, if it is stored
      if ($cacheManager->isStored($path, $filter)) {
          $this->$cacheManager->remove($path, $filter);
      }    
      if ($cacheManager->isStored($path, $filterStrip)) {
          $this->$cacheManager->remove($path, $filterStrip);
      }     
      if ($cacheManager->isStored($path, $filterMiniature)) {
          $this->$cacheManager->remove($path, $filterMiniature);
      }
      */

      $advert->setIp($ipClient);
      $advert->setUpdatedAt(new \Datetime());

      $em = $this->getDoctrine()->getManager();
      $em->flush();
      
      //ATTENTION ! Efface la nouvelle image et pas l'ancienne => Trouver comment récupérer le nom de l'ancienne image...
      //$path =  $advert->getImage()->getImageCachePath();
      //unlink('ozyx/web/media/cache/stripImage/'.$path);

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirect($this->generateUrl('ozyx_platform_view', array('id' => $advert->getId())));
    }

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('ozyxPlatformBundle:Advert:edit.html.twig', array(
      'advert'      => $advert,
      'formBuilder' => $formBuilder->createView(),
    ));
  }

  public function editImageAction($advertId)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce
    $advert = $em->getRepository('ozyxPlatformBundle:Advert')->find($advertId);

    // On modifie l'URL de l'image par exemple
    $advert->getImage()->setUrl('test.png');
    
    // On déclenche la modification
    $em->flush();

    return new Response('OK');
  }

  public function deleteAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em
      ->getRepository('ozyxPlatformBundle:Advert')
      ->find($id)
    ;

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {

      $em->remove($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
      return $this->redirect($this->generateUrl('ozyx_platform_home'));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de delete
    return $this->render('ozyxPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView()
    ));
  }
}