<?php

declare(strict_types=1);

namespace PrestaShop\Module\AdditionalGallery\Entity;

use Image;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShop\Module\AdditionalGallery\Repository\AdditionalImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AdditionalImage
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_additional_image", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_product", type="integer")
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string")
     */
    private $extension;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param $productId int
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @param $extension string
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Returns image folder
     * @return string
     */
    public function getImageFolder()
    {
        return _PS_IMG_DIR_.'p_additional/'.Image::getImgFolderStatic($this->getId());
    }

    /**
     * Returns full image path
     * @return string
     */
    public function getImagePath()
    {
        return $this->getImageFolder().$this->getId().'.'.$this->extension;
    }

    /**
     * Returns image url
     * @return string
     */
    public function getImageUri()
    {
        return _PS_IMG_.'p_additional/'.Image::getImgFolderStatic($this->getId()).$this->getId().'.'.$this->extension;
    }
}