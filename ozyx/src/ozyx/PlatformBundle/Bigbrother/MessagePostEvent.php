<?php
// src/ozyx/PlatformBundle/Bigbrother/MessagePostEvent.php

namespace ozyx\PlatformBundle\Bigbrother;

use Symfony\Component\EventDispatcher\Event;

class MessagePostEvent extends Event
{
  protected $message;
  protected $author;

  public function __construct($message, UserInterface $author)
  {
    $this->message = $message;
    $this->author    = $author;
  }

  // Le listener doit avoir accès au message
  public function getMessage()
  {
    return $this->message;
  }

  // Le listener doit pouvoir modifier le message
  public function setMessage($message)
  {
    return $this->message = $message;
  }

  // Le listener doit avoir accès à l'utilisateur
  public function getAuthor()
  {
    return $this->author;
  }
}