<?php
// src/ozyx/PlatformBundle/Entity/Skill.php

namespace ozyx\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ozyx\PlatformBundle\Entity\SkillRepository")
 */
class Skill
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  public function getId()
  {
    return $this->id;
  }

  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  public function getName()
  {
    return $this->name;
  }
}
