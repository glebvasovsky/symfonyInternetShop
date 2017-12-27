<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Validation\SearchValidator;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     * 
     * @Method("GET")
     * 
     * @param Request $request
     * 
     * @return Response
     *
     * @throws LogicException
     * @throws ValidatorException
     */
    public function searchAction(Request $request): Response
    {
        // Получаем данные, введённые пользователем
        $name = $request->get('name');
        
        // Валидируем данные, введённые пользователем
        $errors = (new SearchValidator())->validate($request->query->all());
        
        if (!empty($errors)) {
            throw new ValidatorException('Введённые данные некорректны');
        }
        
        // Удаляем лишние пробелы, введённый пользователем
        $name = preg_replace('/\s{2,}/', ' ', $name);
        
        $qb = $this->getDoctrine()
                ->getManager()
                ->createQueryBuilder();
        
        $expr = $qb->expr();
        
        $searchResult = $qb->select('p')
                ->from(Product::class, 'p')
                ->where(
                    $expr->like($expr->lower('p.name'), $expr->lower(':name'))
                )
                ->orderBy('p.id', 'ASC')
                ->setParameter('name', "%$name%")
                ->getQuery()
                ->getResult();
        
        $foundProducts = array_map(function(Product $product) use ($request): array {
            return [
                'name' => $product->getName(),
                'url' => $request->getSchemeAndHttpHost() . $this->generateUrl('products', ['slug' => $product->getSlug()]),
            ];
        }, $searchResult);
        
        return new JsonResponse($foundProducts, Response::HTTP_OK);
    }
}
