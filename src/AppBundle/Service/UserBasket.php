<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\BasketProduct;
use AppBundle\Entity\Basket;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\ProductRenderer;
use Doctrine\ORM\Query;

class UserBasket
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ProductRenderer
     */
    protected $productRenderer;

    /**
     * @param EntityManager $em
     * @param ProductRenderer $productRenderer
     */
    public function __construct(EntityManager $em, ProductRenderer $productRenderer)
    {
        $this->em = $em;
        $this->productRenderer = $productRenderer;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUserBasket(User $user): array
    {
        $basket = $this->em
                ->getRepository(Basket::class)
                ->findOneBy([
                    'user' => $user,
                    'status' => 'open',
                ]);

        $productsId = $this->em
                ->createQueryBuilder()
                ->select('IDENTITY(bp.product)')
                ->from(BasketProduct::class, 'bp')
                ->where('IDENTITY(bp.basket) = ' . $basket->getId())
                ->orderBy('bp.id', 'ASC')
                ->getQuery()
                ->getResult();
        
        $products = array_map(function (array $productId) use ($user): array {
            $product = $this->em
                    ->getRepository(Product::class)
                    ->find($productId[1]);
            
            $category = $this->em
                    ->getRepository(Category::class)
                    ->findByProduct($product);
            
           return $this->productRenderer->getRenderData($product, $category[0], $user);
        }, $productsId);
        
        $totalSize = array_reduce($products, function (int $totalSize, array $product): int {
            return $totalSize + (isset($product['fieldValues']['boxSize']) ? str_replace(',', '.', $product['fieldValues']['boxSize']->getValue()) : 0);
        }, 0);
        
        $totalWeight = array_reduce($products, function (int $totalWeight, array $product): int {
            return $totalWeight + (isset($product['fieldValues']['weight']) ? str_replace(',', '.', $product['fieldValues']['boxSize']->getValue()) : 0);
        }, 0);
        
        $totalCost = array_reduce($products, function (int $totalCost, array $product): int {
            return $totalCost + (isset($product['price']) ? str_replace(',', '.', $product['price']) : 0);
        }, 0);
        
        return [
            'totalCount' => count($productsId),
            'totalSize' => $totalSize,
            'totalWeight' => $totalWeight,
            'totalCoast' => $totalCost,
            'products' => $products,
        ];
    }
}
