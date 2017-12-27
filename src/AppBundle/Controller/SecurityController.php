<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Code;
use AppBundle\Validation\AuthorizationValidator;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Exception\ValidatorException;

class SecurityController extends Controller
{
    /**
     * @Route("login", name="login")
     * 
     * @param Request $request
     * 
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $data = [
            'error' => null,
            'csrf_token' => null,
        ];
        
        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')
                ->getToken('authenticate')
                ->getValue()
            : null;
        
        $data['csrf_token'] = $csrfToken;
        
        $phone = $request->get('phone');
        
        // Валидируем данные, введённые пользователем
        $errors = (new AuthorizationValidator())->validate($request->request->all());
        
        if (!empty($errors)) {
            throw new ValidatorException('Введённые данные некорректны: ' . $errors);
        }
        
        $checkSmsStatus = $this->isSmsCodeConsist($phone, $request->get('code'));

        if (!empty($checkSmsStatus['error'])) {
            $data['error'] = $checkSmsStatus['error'];

            return new JsonResponse(['status' => 'error', 'message' => $data['error']], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()
                ->getManager();
        
        $user = $em->getRepository(User::class)
                ->findOneBy(['phone' => $phone]);

        if (empty($user)) {
            $data['error'] = 'Номер телефона должен начинаться с +7 и должен быть записан без пробелов (например, +79991234567).'
                    . ' Возможно, Вы ещё не зарегистрированы.';

            return new JsonResponse(['status' => 'error', 'message' => $data['error']], Response::HTTP_BAD_REQUEST);
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')
                ->setToken($token);

        $this->get('session')
                ->set('_security_main', serialize($token));

        $this->get('event_dispatcher')
                ->dispatch('security.interactive_login', new InteractiveLoginEvent($request, $token));
        
        $user->setLastLogin(new DateTime());
        
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
    
    /**
     * @param string $phone
     * @param string $code
     * 
     * @return array $data содержит ошибку 'error' или запись о том, что ошибок нет 'success'
     */
    public function isSmsCodeConsist(string $phone, string $code): array
    {
        // Достаём код из БД по известному нам мобильному номеру
        $codeFromDataBase = $this->getDoctrine()
                        ->getManager()
                        ->getRepository(Code::class)
                        ->findOneBy([
                            'phone' => $phone,
                            'value' => $code,
                            'isLogin' => null,
                        ]);
        
        // Если такого кода в базе нет, возвращаем ошибку
        if (empty($codeFromDataBase)) {
            $data['error'] = 'Неверно введён SMS-код';
            
            return $data;
        }
        
        // Проверка, не просрочен ли код
        $createCodeTime = $codeFromDataBase->getCreatedAt();
        $checkTime = (new DateTime())->modify('-5 minutes'); // время действия кода - 5 минут
        
        if ($checkTime > $createCodeTime) { 
            $data['error'] = 'Данный SMS-код уже недействителен. Запросите новый код.';
            
            return $data;
        }
        
        // Если же ошибок не обнаружено
        $codeFromDataBase->setIsLogin(1);
        
        $em = $this->getDoctrine()
                ->getManager();
        $em->persist($codeFromDataBase);
        $em->flush();
        
        $data['success'] = 'Пользователь идентифицирован';
                
        return $data;
    }

    /**
     * @return Response
     * 
     * @Route("/logout", name="logout")
     */
    public function logoutAction(): Response
    {
        $this->get('security.token_storage')
                ->setToken(null);
        $this->get('session')
                ->invalidate();
        
        return new JsonResponse(['status' => 'success'], Response::HTTP_OK);
    }
}

