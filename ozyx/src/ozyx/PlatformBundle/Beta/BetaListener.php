<?php
// src/ozyx/PlatformBundle/Beta/BetaListener.php

namespace ozyx\PlatformBundle\Beta;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
  // Notre processeur
  protected $betaHTML;
  protected $endDate;

  public function __construct(BetaHTML $betaHTML, $endDate)
  {
    $this->betaHTML = $betaHTML;
    $this->endDate  = new \Datetime($endDate);
  }

  public function processBeta(FilterResponseEvent $event)
  {
    if (!$event->isMasterRequest()) {
      return;
    }

    $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');

    if ($remainingDays <= 0) {
      // Si la date est dépassée, on ne fait rien
      return;
    }
       
    // On utilise notre BetaHRML
    $response = $this->betaHTML->displayBeta($event->getResponse(), $remainingDays);
    // On met à jour la réponse avec la nouvelle valeur
    $event->setResponse($response);
  }
}