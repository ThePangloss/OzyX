<?php
// src/ozyx/PlatformBundle/DoctrineListener/ozyxListener.php
namespace ozyx\PlatformBundle\DoctrineListener;
 
use Doctrine\ORM\Event\LifecycleEventArgs;
use ozyx\PlatformBundle\Entity\Image;

class ozyxVideCacheImagine
{
  protected $cacheManager;

  public function __construct($cacheManager)
  {
      $this->cacheManager = $cacheManager;
  }

  // en cas la modification l'image d'origine
  public function preUpdate(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();

    if ($entity instanceof Image) {
        // vider le cache des vignettes
        $this->cacheManager->remove($entity->getImageCachePath());
    }
  }

  // en cas du supprission l'image d'origine
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