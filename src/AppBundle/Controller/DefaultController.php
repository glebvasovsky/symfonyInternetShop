<?php

namespace AppBundle\Controller;

use AppBundle\Form\FeedbackType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('default/index.html.twig', [
            'userOrders' => $this->get('user.orders.count')->getUserOrdersCount(),
            'feedbackForm' => $this->createForm(FeedbackType::class)
                ->createView(),
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function viewContactsAction(Request $request): Response
    {
        return $this->render('default/contact.html.twig', [
            'userOrders' => $this->get('user.orders.count')->getUserOrdersCount(),
            'feedbackForm' => $this->createForm(FeedbackType::class)
                ->createView(),
        ]);
    }

    /**
     * @Route("/delivery", name="delivery")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function deliveryAction(Request $request): Response
    {
        return $this->render('default/delivery.html.twig', [
            'userOrders' => $this->get('user.orders.count')->getUserOrdersCount(),
        ]);
    }

    /**
     * @Route("/about", name="about")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function viewAboutAction(Request $request): Response
    {
        return $this->render('default/about.html.twig', [
            'userOrders' => $this->get('user.orders.count')->getUserOrdersCount(),
        ]);
    }
    
    /**
     * @Route("/galery", name="galery")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function viewGaleryAction(Request $request): Response
    {
        return $this->render('default/galery.html.twig', [
            'userOrders' => $this->get('user.orders.count')->getUserOrdersCount(),
        ]);
    }
}
