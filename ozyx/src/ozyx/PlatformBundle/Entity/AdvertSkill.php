<?php
// src/ozyx/PlatformBundle/Entity/AdvertSkill.php

namespace ozyx\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ozyx\PlatformBundle\Entity\AdvertSkillRepository")
 */
class AdvertSkill
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="level", type="string", length=255)
   */
  private $level;

  /**
   * @ORM\ManyToOne(targetEntity="ozyx\PlatformBundle\Entity\Advert", inversedBy="advertSkills")
   * @ORM\JoinColumn(nullable=false)
   */
  private $advert;

  /**
   * @ORM\ManyToOne(targetEntity="ozyx\PlatformBundle\Entity\Skill")
   * @ORM\JoinColumn(nullable=false)
   */
  private $skill;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return AdvertSkill
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set advert
     *
     * @param \ozyx\PlatformBundle\Entity\Advert $advert
     *
     * @return AdvertSkill
     */
    public function setAdvert(\ozyx\PlatformBundle\Entity\Advert $advert)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \ozyx\PlatformBundle\Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Set skill
     *
     * @param \ozyx\PlatformBundle\Entity\Skill $skill
     *
     * @return AdvertSkill
     */
    public function setSkill(\ozyx\PlatformBundle\Entity\Skill $skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return \ozyx\PlatformBundle\Entity\Skill
     */
    public function getSkill()
    {
        return $this->skill;
    }
}
