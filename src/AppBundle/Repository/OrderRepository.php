<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Basket;
use AppBundle\Entity\Order;
use AppBundle\Entity\User;

class OrderRepository extends EntityRepository
{
    /**
     * @param User $user
     * 
     * @return array
     */
    public function findByUser(User $user): array
    {
        $qb = $this->getEntityManager()
                ->createQueryBuilder();
        
        // TODO: собрать два простых запроса в один запрос с поздапросом
        $userBaskets = $qb->select('b.id')
                    ->from(Basket::class, 'b')
                    ->where('b.user = '. $user->getId())
                    ->orderBy('b.id', 'ASC')
                    ->getQuery()
                    ->getResult();
        
     $userOrders = [];
        
        if (!empty($userBaskets)) {
            $userBasketsId = array_map(function ($basket) {
                return $basket['id'];
            }, $userBaskets);
            
            $stringUserBasketId = implode(',', $userBasketsId);

            $userOrders = $qb->select('o')
                    ->from(Order::class, 'o')
                    ->where('o.basket in (' . $stringUserBasketId . ')')
                    ->orderBy('o.id', 'ASC')
                    ->getQuery()
                    ->getResult();
        }

        return $userOrders;
    }
    
}
