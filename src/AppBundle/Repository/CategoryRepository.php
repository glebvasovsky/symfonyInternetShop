<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryRelation;
use AppBundle\Entity\Product;
use AppBundle\Entity\CategoryProduct;

class CategoryRepository extends EntityRepository
{
    /**
     * @param Category $parentCategory
     * 
     * @return array
     */
    public function findChildren(Category $parentCategory): array
    {
        $parentRelations = $this->getEntityManager()
            ->getRepository(CategoryRelation::class)
            ->findBy([
                'parent' => $parentCategory,
            ]);
        
        return array_map(function (CategoryRelation $relation): Category {
                return $relation->getChild();
            }, $parentRelations);
    }
    
    /**
     * @param Category $childCategory
     * 
     * @return array
     */
    public function findParents(Category $childCategory): array
    {
        $childRelations = $this->getEntityManager()
            ->getRepository(CategoryRelation::class)
            ->findBy([
                'child' => $childCategory,
            ]);
        
        return array_map(function (CategoryRelation $relation): Category {
                return $relation->getParent();
            }, $childRelations);
    }
    
    /**
     * @param Product $product
     * 
     * @return array
     */
    public function findByProduct(Product $product): array
    {
        $categoryProductRelations = $this->getEntityManager()
            ->getRepository(CategoryProduct::class)
            ->findBy([
                'product' => $product,
            ]);
        
        return array_map(function (CategoryProduct $relation): Category {
                return $relation->getCategory();
            }, $categoryProductRelations);
    }
    
    /**
     * @param string $slug
     * 
     * @return Category
     */
    public function findOneBySlug(string $slug): Category
    {
        return $this->findOneBy([
                    'slug' => $slug,
                ]);
    }
}
