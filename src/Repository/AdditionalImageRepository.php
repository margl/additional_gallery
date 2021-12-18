<?php

namespace PrestaShop\Module\AdditionalGallery\Repository;

use Doctrine\ORM\EntityRepository;

class AdditionalImageRepository extends EntityRepository
{
    public function getByProductId($idProduct)
    {
        $images = $this->findByProductId($idProduct);

        foreach ($images as $image) {
            $image->uri = $image->getImageUri();
        }

        return $images;
    }
}