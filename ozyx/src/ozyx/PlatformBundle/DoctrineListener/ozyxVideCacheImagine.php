<?php
// src/ozyx/PlatformBundle/DoctrineListener/ozyxListener.php
namespace ozyx\PlatformBundle\DoctrineListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
//use ozyx\PlatformBundle\Entity\Advert;
use ozyx\PlatformBundle\Entity\Image;

class ozyxVideCacheImagine
{
  protected $cacheManager;
  /*
  protected $Filesystem;
  */
  protected $stripPath;
  protected $miniaturePath;

  public function __construct($cacheManager/*, $Filesystem*/)
  {
      $this->cacheManager   = $cacheManager;
  }

  // en cas la modification l'image d'origine
  public function postUpdate(LifecycleEventArgs $args)
  {

      $bool   =  False;
      $entity = $args->getEntity();

      if ($entity instanceof Image) {
        
        $bool     = $entity->GetOldCacheStrip();
        $boolM    = $entity->GetOldCacheMiniat();
        $fs       = new Filesystem();

        if (!$bool) {
         // throw new NotFoundHttpException("Impossible d'effacer le cache: L'ancienne image n'a pas été trouvée...");
          throw new \Exception("Impossible d'effacer le cache de l'image (Strip): Inexistant...");
        } else {
          $fs->remove($entity->GetOldCacheStrip());
        }
        if (!$boolM) {
         // throw new NotFoundHttpException("Impossible d'effacer le cache: L'ancienne image n'a pas été trouvée...");
          throw new \Exception("Impossible d'effacer le cache de l'image (Miniat): Inexistant...");
        } else {
          $fs->remove($entity->GetOldCacheMiniat());
        }
      }
  }
  
  // en cas du suppression l'image d'origine
  public function preRemove(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    if ($entity instanceof Image) {
        // vider le cache des vignettes
        $this->cacheManager->remove($entity->getImageCachePath());
    }
  }
}
?>