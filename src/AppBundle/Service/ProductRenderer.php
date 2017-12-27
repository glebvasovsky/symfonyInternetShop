<?php

namespace AppBundle\Service;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\BasketProduct;
use AppBundle\Entity\Basket;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\ProductPhoto;
use AppBundle\Entity\FieldType;
use AppBundle\Entity\FieldValue;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductRenderer
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * @var Route 
     */
    protected $router;
    
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param EntityManager $em
     * @param Router $router
     * @param RequestStack $request
     */
    public function __construct(EntityManager $em, Router $router, RequestStack $request) {
        $this->em = $em;
        $this->router = $router;
        $this->request = $request;
    }
    
    /**
     * @param Product $product
     * @param Category $category
     * @param User $user
     * 
     * @return array
     */
    public function getRenderData(Product $product, Category $category, User $user): array
    {
        // -- Число продуктов данного вида, уже находящееся в корзине
        $amountInBasket = 0;

        // Проверяем, есть ли у нас id корзины
        if (!empty($basketId)) {
            $basket = $this->em->getRepository(Basket::class)
                    ->find($basketId);
            // Проверяем, принадлежит ли корзина авторизованному пользователю
            if ($basket->getUser() !== $user) {
                throw new AccessDeniedHttpException('Доступ к данной корзине закрыт');
            }

            $basketProduct = $this->em->getRepository(BasketProduct::class)
                    ->findOneBy([
                'basket' => $basket,
                'product' => $product,
            ]);
            $amountInBasket = $basketProduct ? $basketProduct->getAmount() : 0;
        }

        // -- Уровень цен, установленный для данного пользователя 
        $level = $user ? $user->getLevel() : $this->em->getRepository(Level::class)
                            ->findOneBy([
                                'name' => 'postponement_payment',
                            ]);
;

        $price = $this->em->getRepository(ProductPrice::class)
                ->findOneBy([
            'product' => $product,
            'level' => $level,
        ]);

        if (empty($price)) {
            throw new EntityNotFoundException('Для товара с id=' . $product->getId() . ' не установлена цена уровня ' . $level->getName());
        }
        
        // -- Ссылка на фото товара
        $productPhoto = $this->em->getRepository(ProductPhoto::class)
                            ->findOneBy([
                        'product' => $product,
                    ]);

        $pathToPhoto = '';

        if (!empty($productPhoto)) {
            $pathToPhoto = $productPhoto->getFile()
                ->getPath();
        }

        // -- Атрибуты товара - все атрибуты данной категории
        $productTypes = $this->em->getRepository(FieldType::class)
                ->findBy([
            'category' => $category,
        ]);

        if (empty($productTypes)) {
            throw new EntityNotFoundException('Для категории с id=' . $category->getId() . ' не установлены атрибуты');
        }

        // -- Значения всех атрибутов категории
        $productFieldValue = [];

        foreach ($productTypes as $type) {
            $productFieldValue[$type->getName()] = $this->em->getRepository(FieldValue::class)
                    ->findOneBy([
                'product' => $product,
                'fieldType' => $type,
            ]);

            if (empty($productFieldValue[$type->getName()])) {
                throw new EntityNotFoundException('У атрибута с id=' . $type->getId() . ' не определено значение FieldValue');
            }
        }

        $productRenderData = [
            'category' => $category,
            'amountInBasket' => $amountInBasket,
            'price' => $price->getValue(),
            'pathToPhoto' => $pathToPhoto,
            'fieldValues' => $productFieldValue,
            'url' => $this->request->getCurrentRequest()->getSchemeAndHttpHost() . $this->router->generate('products', ['slug' => $product->getSlug()]),
        ];

        return $productRenderData;
    }
}
