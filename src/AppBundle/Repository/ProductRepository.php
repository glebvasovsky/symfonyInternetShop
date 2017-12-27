<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryProduct;
use AppBundle\Entity\Product;

class ProductRepository extends EntityRepository
{
    /**
     * @param Category $category
     * 
     * @return array
     */
    public function findByCategory(Category $category, int $limit = null): array
    {
        $categoryProductRelations = $this->getEntityManager()
            ->getRepository(CategoryProduct::class)
            ->findBy([
                'category' => $category,
            ], null, $limit);
        
        return array_map(function (CategoryProduct $relation): Product {
                return $relation->getProduct();
            }, $categoryProductRelations);
    }
    
    /**
     * @param string $slug
     *  
     * @return Product
     */
    public function findOneBySlug(string $slug): Product
    {
        return $this->findOneBy([
                    'slug' => $slug,
                ]);
    }
}
