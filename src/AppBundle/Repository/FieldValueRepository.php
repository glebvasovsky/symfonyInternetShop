<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\FieldValue;
use AppBundle\Entity\Product;

class FieldValueRepository extends EntityRepository
{
    /**
     * @param Product $product
     * 
     * @return array
     */
    public function findByProduct(Product $product): array
    {
        return $this->findBy([
            'product' => $product,
        ]);
    }
}
