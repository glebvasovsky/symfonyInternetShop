<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use AppBundle\Entity\ProductPhoto;
use AppBundle\Entity\FieldValue;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/product/{slug}", name="products")
     * 
     * @param Request $request
     * @param string $slug
     * 
     * @return Response
     * 
     * @throws EntityNotFoundException
     * @throws LogicException
     */
    public function viewAction(Request $request, string $slug): Response
    {
        $em = $this->getDoctrine()
                ->getManager();
        
        // Получаем товар
        $product = $em->getRepository(Product::class)
                ->findOneBySlug($slug);
        
        if (empty($product)) {
            throw new EntityNotFoundException('Продукт с идентификатором: ' . $product->getSlug() . ' не найден');
        }
        
        // Получаем путь к фото товара
        $productPhotoRepository = $em->getRepository(ProductPhoto::class);
        
        $productPhoto = $productPhotoRepository->findOneByProduct($product);
        
        $productPhotoPath = '';
        
        if (!empty($productPhoto)) {
            $productPhotoPath = $productPhoto->getFile()
                ->getPath();
        }
                
        
        // Получаем похожие товары
        // Достаём все категории товара
        $allProductCategories = $em->getRepository(Category::class)
                ->findByProduct($product);
        
        if (empty($allProductCategories)) {
            throw new EntityNotFoundException('Категории продукта с id = ' . $product->getId() . ' не найдены');
        }
        
        $similarProducts = [];
        
        // Из каждой категории достаём все товары той же категории,что и $product и пути к их фото
        foreach ($allProductCategories as $similarCategory) {
            $similarProductsObjects = $em->getRepository(Product::class)
                    ->findByCategory($similarCategory, 7);
            
            // Получаем пути к товарам той же категории,что и $product 
            foreach ($similarProductsObjects as $similarProduct) {
                // Исключаем $product, остальные записываем в массив $similarProducts
                if ($similarProduct->getId() == $product->getId()) {
                    continue;
                } else {
                    $similarProductPhoto = $productPhotoRepository->findOneByProduct($similarProduct);
                    
                    $similarProducts[$similarProduct->getId()] = [
                        'name' => $similarProduct->getName(),
                        'url' => $request->getSchemeAndHttpHost() . $this->generateUrl('products', ['slug' => $similarProduct->getSlug()]),
                        'pathToPhoto' => $similarProductPhoto ? $similarProductPhoto->getFile()->getPath() : '',
                    ];
                }
            }
        }
        
        // Получаем свойства данного товара и их значения
        $types = [];
        
        $fieldValues = $em->getRepository(FieldValue::class)
                ->findByProduct($product);
        
        if (empty($fieldValues)) {
            throw new EntityNotFoundException('Значения свойств продукта с id = ' . $product->getId() . ' не найдены');
        }
        
        foreach ($fieldValues as $fieldValue) {
            $fieldType = $fieldValue->getFieldType();
            
            $types[$fieldType->getName()]['label'] = $fieldType->getLabel();
            $types[$fieldType->getName()]['value'] = $fieldValue->getValue();
        }
        
        return $this->render('product/product_view.html.twig', [
            'mainProduct' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'path' => $productPhotoPath,
                'types' => $types,
            ],
            'similarProducts' => $similarProducts,
            'userOrders' => $this->get('user.orders.count')->getCount(),
        ]);
    }
}
