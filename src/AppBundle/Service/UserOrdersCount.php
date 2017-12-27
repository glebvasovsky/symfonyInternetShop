<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Entity\Order;
use Doctrine\ORM\EntityManager;

class UserOrdersCount 
{
    /**
     * @var EntityManager
     */
    protected $em;
    
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @return int
     */
    public function getUserOrdersCount(): int
    {
//        TODO: Когда будет сделана авторизация пользователя, заменить
//        $user = $this->getUser();
        
        $user = $this->em
                ->getRepository(User::class)
                ->find(8);
        
        if ($user) {
            $userOrders = $this->em
                    ->getRepository(Order::class)
                    ->findByUser($user);
            
            if (empty($userOrders)) {
                return 0;
            }
            
            return count($userOrders);
        }

        return 0;
    }
}
