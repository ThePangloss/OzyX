<?php

// src/ozyx/PlatformBundle/Controller/AdvertController.php

namespace ozyx\PlatformBundle\Controller;

use ozyx\PlatformBundle\Entity\Advert;
use ozyx\PlatformBundle\Entity\Image;
use ozyx\PlatformBundle\Entity\Application;
use ozyx\PlatformBundle\Entity\AdvertSkill;
use ozyx\PlatformBundle\Form\AdvertType;
use ozyx\PlatformBundle\Form\AdvertEditType;
use ozyx\UserBundle\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\PropertyAccess\PropertyAccess;
use ozyx\PlatformBundle\Bigbrother\MessagePostEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use ozyx\PlatformBundle\Bigbrother\BigbrotherEvents;


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
      'listAdverts' => $listAdverts));
  }

  public function indexAction($page)
  {
    
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    $nbPerPage = 3;

    // Pour récupérer la liste de toutes les annonces : on utilise findAll()
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('ozyxPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts)/$nbPerPage);

    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    return $this->render('ozyxPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,));
  }

/**
 * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
 */
  public function viewAction(Advert $advert)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em
      ->getRepository('ozyxPlatformBundle:Application')
      ->findBy(array('advert' => $advert));

    // On récupère maintenant la liste des AdvertSkill
    $listAdvertSkills = $em
      ->getRepository('ozyxPlatformBundle:AdvertSkill')
      ->findByAdvert($advert);

    $advertDcd = $advert->getContent();
    $advertDcd = htmlspecialchars_decode($advertDcd);

    return $this->render('ozyxPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'advertDcd'        => $advertDcd,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function addAction(Request $request)
  {
    /*if ($this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
      throw new AccessDeniedException('Accès limité aux auteurs.');
    }*/
    $em = $this->getDoctrine()->getManager();
    $advert = new Advert();

    $formBuilder = $this->createForm(AdvertType::class, $advert);
    if (($formBuilder->handleRequest($request)->isValid()))  {
      /*
      // On crée l'évènement avec ses 2 arguments
      $event = new MessagePostEvent($advert->getContent(), $advert->getAuthor());
      // On déclenche l'évènement
      $this->get('event_dispatcher')->dispatch(BigbrotherEvents::onMessagePost, $event);
      // On récupère ce qui a été modifié par le ou les listeners, ici le message
      $advert->setContent($event->getMessage());
      */
      $content   = $formBuilder['content']->getData();
      $contentSf =htmlspecialchars($content);
      $advert->setContent($contentSf);

      $advert->setDate(new \Datetime());
      $ipClient = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();
      $advert->setIp($ipClient);
      $em->persist($advert);
      $em->flush();  

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirect($this->generateUrl('ozyx_platform_view', array('id' => $advert->getId(),)));
    }
    return $this->render('ozyxPlatformBundle:Advert:add.html.twig', array(
    'formBuilder' => $formBuilder->createView(),));
  }

  /**
   * @Security("has_role('ROLE_AUTEUR')")
   * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
   */
  public function editAction(Advert $advert, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    // On récupère l'annonce $id
    $advert = $em->getRepository('ozyxPlatformBundle:Advert')->find($advert);

    $formBuilder = $this->createForm(AdvertEditType::class, $advert);

    $ipClient = $this->container->get('request_stack')->getCurrentRequest()->getClientIp();

    $oldImage = $advert->getImage()->getImageName();
    $advert->getImage()->setOldImageName($oldImage);

    if ($formBuilder->handleRequest($request)->isValid()) {
      //utile ??
      $em = $this->getDoctrine()->getManager();

      $advert->setIp($ipClient);
      $advert->setUpdatedAt(new \Datetime());
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien modifiée.');

      return $this->redirect($this->generateUrl('ozyx_platform_view', array('id' => $advert->getId(),)));
    }

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('ozyxPlatformBundle:Advert:edit.html.twig', array(
      'advert'      => $advert,
      'formBuilder' => $formBuilder->createView(),
      'oldImage'    => $oldImage));
  }

  /**
   * @Security("has_role('ROLE_SUPER_ADMIN')")
   */
  public function editUserAction(Request $request)
  {
    $userManager = $this->get('fos_user.user_manager');
    $formBuilder = $this->createForm(UserType::class);

    if ($formBuilder->handleRequest($request)->isValid()) {

      //Accès aux nom de l'utilisateur de l'objet user.
      $accessor = PropertyAccess::createPropertyAccessor();
      $username  = $accessor->getValue($formBuilder['user']->getData(), 'username');
      //Accès à la propriété du password et du mail.
      $userpass = $formBuilder['password']->getData();
      $usermail = $formBuilder['email']->getData();
      //Accès au propriétés de l'utilisateur.
      $userroles = $formBuilder['roles']->getData();   

      //On hydrate puis enregistre l'objet user en question (L'entitymanager provoque le flush tout seul!)
      $user = $userManager->findUserBy(array('username' => $username));
      $user->setEmail($usermail);
      $user->setPlainPassword($userpass);
      $user->setRoles($userroles);
      $userManager->updateUser($user);

      return $this->redirect($this->generateUrl('ozyx_platform_editUser'));
    }

    return $this->render('ozyxPlatformBundle:Advert:userEdit.html.twig', array(
      'formBuilder' => $formBuilder->createView()));
  }

  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
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

  /**
   * @Security("has_role('ROLE_ADMIN')")
   * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
   */
  public function deleteAction(Advert $advert, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('ozyxPlatformBundle:Advert')->find($advert);

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
      'form'   => $form->createView()));
  }

  public function translationAction($name)
  {
    return $this->render('ozyxPlatformBundle:Blog:translation.html.twig', array(
      'name' => $name));
  }
}