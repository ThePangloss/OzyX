<?php
// src/ozyx/PlatformBundle/Entity/Image

namespace ozyx\PlatformBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\HttpFoundation\File\File;
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
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    public function getImageCachePath()
    {
    // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
    return $this->getImageName();

    //__DIR__.'/../../../../web/'.'bundles/ozyxplatform/images/'.
    }
}
