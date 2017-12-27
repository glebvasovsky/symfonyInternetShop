<?php

namespace AppBundle\Controller;

use AppBundle\Form\FeedbackType;
use AppBundle\Service\FeedbackMailSender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;

class FeedbackController extends Controller
{
    /**
     * @Route("/feedback", name="feedback")
     * 
     * @Method("POST")
     *  
     * @param Request            $request
     * @param FeedbackMailSender $feedbackMailSender
     * 
     * @return Response
     * 
     * @throws ValidatorException
     */
    public function indexAction(Request $request, FeedbackMailSender $feedbackMailSender): Response
    {
        // Получаем данные, введённые пользователем
        $form = $this->createForm(FeedbackType::class);
        $form->handleRequest($request);
        
        if (!$form->isValid()) {
            throw new ValidatorException('Введённые данные некорректны');
        }
        
        $userFeedback = $form->getData();
        
        $feedbackMailSender->sendFeedback(
            $userFeedback['name'], 
            $userFeedback['phone'], 
            $userFeedback['message']
        );
        
        return new Response(null, Response::HTTP_CREATED);
    }
}
