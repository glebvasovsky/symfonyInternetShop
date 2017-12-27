<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Service\ProductRenderer;
use AppBundle\Service\UserBasket;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UnexpectedValueException;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{slug}", name="category")
     *
     * @param Request $request
     * @param string  $slug
     *
     * @return Response
     *
     * TODO: часть этих эксепшнов стоить обрабатывать, но пока хуй кладем
     *
     * @throws AccessDeniedHttpException
     * @throws EntityNotFoundException
     * @throws LogicException
     * @throws UnexpectedValueException
     */
    public function viewCategoryAction(Request $request, string $slug, ProductRenderer $productRenderer, UserBasket $userBasket): Response
    {
        $em = $this->getDoctrine()
                ->getManager();
        
        // TODO: Когда будет сделана авторизация, заменить на getUser()
        $user = $em->getRepository(User::class)
                ->find(8);
        
        // Проверяем, страница каталога или категории
        if ('catalog' == $slug) {
            // Достаём все родительские категории из БД
            $rootCategories = $em->getRepository(Category::class)
                    ->findBy([
                'isRoot' => true,
            ]);
            
            if (empty($rootCategories)) {
                throw new EntityNotFoundException('Корневые категории не найдены');
            }

            $pageTitle = 'Каталог товаров';
        } else {
            // Или достаём только переданную в категорию
            $rootCategories = $em->getRepository(Category::class)
                ->findBySlug($slug);
            
            if (empty($rootCategories)) {
                throw new EntityNotFoundException('Корневая категория с идентификатором ' . $slug . ' не найдена');
            }

            $pageTitle = 'Старница категории ' . $rootCategories[0]->getName();
        }

        $categories = [];

        foreach ($rootCategories as $rootCategory) {
            $childCategories = $em->getRepository(Category::class)
                    ->findChildren($rootCategory);

            $products = [];

            // Достаём все продукты для каждой подкатегории
            foreach ($childCategories as $childCategory) {
                $productsInCategory = $em->getRepository(Product::class)
                        ->findByCategory($childCategory);
                
                // Для каждого продукта в категории получаем массивы данных:
                foreach ($productsInCategory as $product) {
                    $products[] = $this->get('product.renderer')
                        ->getRenderData($product, $childCategory, $user);
                }
            }
            
            $categories[] = [
                'name' => $rootCategory->getName(),
                'products' => $products,
            ];
            
//            $basket = [
//                'totalCount' => 0,
//                'totalSize' => 0,
//                'totalWeight' => 0,
//                'totalCoast' => 0,
//                'products' => 0,
//            ];
            $basket = $userBasket->getUserBasket($user);
        }
        
        return $this->render('category/category.html.twig', [
                    'pageTitle' => $pageTitle,
                    'categories' => $categories,
                    'basket' => $basket,
                    'userOrders' => $this->get('user.orders.count')->getUserOrdersCount(),
        ]);
    }

}
