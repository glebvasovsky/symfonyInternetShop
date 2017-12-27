<?php

namespace AppBundle\Service;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class FeedbackMailSender
{
    /**
     * @var string
     */
    private $adminEmail;
    
    /**
     * @var string
     */
    private $senderEmail;
    
    /**
     * @var Twig_Environment
     */
    private $twig;
    
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    
    /**
     * @param Swift_Mailer $mailer
     * @param string $adminEmail
     * @param string $senderEmail
     * @param Twig_Environment $twig
     */
    public function __construct(
        Swift_Mailer $mailer,
        string $adminEmail, 
        string $senderEmail,
        Twig_Environment $twig
    ) {
        $this->adminEmail = $adminEmail;
        $this->senderEmail = $senderEmail;
        $this->twig = $twig;
        $this->mailer = $mailer;
    }
    
    /**
     * @param string $userName
     * @param string $userPhone
     * @param string $userMessage
     */
    public function sendFeedback(string $userName, string $userPhone, string $userMessage): void
    {
        $mail = (new Swift_Message('Feedback Email'))
            ->setFrom($this->senderEmail)
            ->setTo($this->adminEmail)
            ->setBody(
                $this->twig->render(
                    'feedback/feedback_email.html.twig', [
                        'name' => $userName,
                        'phone' => $userPhone,
                        'message' => $userMessage,
                ]),
                'text/html');
        
        $this->mailer->send($mail);
    }
}