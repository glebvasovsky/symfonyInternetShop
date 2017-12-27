<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\ProductPhoto;
use AppBundle\Entity\Product;

class ProductPhotoRepository extends EntityRepository
{
    /**
     * @param Product $product
     * 
     * @return ProductPhoto
     */
    public function findOneByProduct(Product $product): ProductPhoto
    {
        return $this->findOneBy([
            'product' => $product,
        ]);
    }
}
