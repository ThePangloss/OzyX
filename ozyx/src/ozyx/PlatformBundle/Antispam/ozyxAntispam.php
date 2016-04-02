<?php
// src/ozyx/PlatformBundle/Antispam/ozyxAntispam.php

namespace ozyx\PlatformBundle\Antispam;

class ozyxAntispam extends \Twig_Extension
{
  protected   $mailer;
  protected   $locale;
  protected   $minLength;

  public function __construct(\Swift_Mailer $mailer, $minLength)
  {
    $this->mailer    = $mailer;
    $this->minLength = (int) $minLength;
  }

  // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
  public function getFunctions()
  {
    return array(
      'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam')
    );
  }
  /**
   * Vérifie si le texte est un spam ou non
   *
   * @param string $text
   * @return bool
   */
  public function isSpam($text)
  {
    $this->minLength = 3;
    if ((strlen($text)) < ($this->minLength)) {
      return true;
    }else{
      return false;
    }  
  }

  // La méthode getName() identifie votre extension Twig, elle est obligatoire
  public function getName()
  {
    return 'ozyxAntispam';
  }

  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
}