<?php

declare(strict_types=1);

namespace Margl\AdditionalGallery\Entity;

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
     * @param $productId int
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }
}