<?php
// src/ozyx/PlatformBundle/Entity/Image

namespace ozyx\PlatformBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ozyx\PlatformBundle\Entity\ImageRepository")
 * @Vich\Uploadable
 */
class Image
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="imageName", type="string", length=255)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(name="oldImageName", type="string", length=255,  nullable=true)
     *
     * @var string
     */
    private $oldImageName;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     * 
     * @var File
     */
    private $imageFile;


    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAtImage;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Image
     */
   
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAtImage = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

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
     * Set updatedAtImage
     *
     * @param \DateTime $updatedAtImage
     *
     * @return Image
     */
    public function setUpdatedAtImage($updatedAtImage)
    {
        $this->updatedAtImage = $updatedAtImage;

        return $this;
    }

    /**
     * Get updatedAtImage
     *
     * @return \DateTime
     */
    public function getUpdatedAtImage()
    {
        return $this->updatedAtImage;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     *
     * @return Image
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Set oldImageName
     *
     * @param string $oldImageName
     *
     * @return Image
     */
    public function setOldImageName($oldImageName)
    {
        $this->oldImageName = $oldImageName;

        return $this;
    }

    /**
     * Get oldImageName
     *
     * @return string
     */
    public function getOldImageName()
    {
        return $this->oldImageName;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Get getImageCachePath
     *
     * @return string
     */
    public function getImageCachePath()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return $this->getImageName();

        //__DIR__.'/../../../../web/'.'bundles/ozyxplatform/images/'.
    }


    /**
     * Get GetOldCacheStrip
     *
     * @return string
     */
    public function GetOldCacheStrip()
    {    
        $fs = new Filesystem();
        $cacheRepStrip = '';

        $cacheRepStrip = __DIR__.'/../../../../web/'.'media/cache/stripImage/'.$this->getoldImageName();
        if (($fs->exists($cacheRepStrip))) {
            return $cacheRepStrip;
        } else {
            return false;
        }

    }

    /**
     * Get GetOldCacheMiniat
     *
     * @return string
     */
    public function GetOldCacheMiniat()
    {    
        $fs = new Filesystem();

        $cacheRepMiniat = __DIR__.'/../../../../web/'.'media/cache/miniature/'.$this->getoldImageName();
        if ($fs->exists($cacheRepMiniat)){
            return $cacheRepMiniat;
        } else {
            return false;
        }
    }
}
