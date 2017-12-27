<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Level;
use AppBundle\Entity\Code;
use AppBundle\Validation\AuthorizationValidator;
use DateTime;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;

class RegistrationController extends BaseController
{
    /**
     * @Route("/register", name="register")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request): Response
    {
        $data = ['error' => ''];
        
        // Валидируем данные, введённые пользователем
        $errors = (new AuthorizationValidator())->validate($request->request->all());
        
        if (!empty($errors)) {
            throw new ValidatorException('Введённые данные некорректны: ' . implode('; ', $errors));
        }
        
        $phone = $request->get('phone');
        $code = $request->get('code'); 
        
        $em = $this->getDoctrine()
                ->getManager();
        
        $user = $em->getRepository(User::class)
                ->findOneBy([
                    'phone' => $phone,
                ]);
        
        if (!empty($user)) {
            $data['error'] = 'Пользователь уже зарегистрирован';
            
            return new JsonResponse(['status' => 'error', 'message' => $data['error']], Response::HTTP_BAD_REQUEST);
        }
        
        $user = new User();
        $user->setEnabled(true);

        $checkSmsStatus = $this->isSmsCodeConsist($phone, $code);

        if (empty($checkSmsStatus['success'])) {
            $data['error'] = $checkSmsStatus['error'];

            return new JsonResponse(['status' => 'error', 'message' => $data['error']], Response::HTTP_BAD_REQUEST);
        }
        
        $level = $em->getRepository(Level::class)
                ->findOneBy([
                    'name' => 'postponement_payment',
                ]);
        
        $user->setPhone($phone);
        $user->setLevel($level);
        $user->addRole(User::ROLE_DEFAULT);
        
        $em->persist($user);
        $em->flush();
        
        return $this->redirectToRoute('login', ['phone' => $phone, 'code' => $code]);
    }

    /**
     * @Route("/register/generateSmsCode", name="code")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function generateSmsCode(Request $request): Response
    {
        $phone = $request->get('phone');

        $rand = rand(1000, 9999);

        $code = new Code();
        $code->setPhone($phone);
        $code->setCreatedAt(new DateTime());
        $code->setValue($rand);

        $em = $this->getDoctrine()
                        ->getManager();
        $em->persist($code);
        $em->flush();

        file_get_contents('https://smsc.ru/sys/send.php?login='.$login.'&psw='.$pass.'&phones='.$phone.'&mes=Code: '.$rand);

        return new JsonResponse([
            'status' => 'success',
        ]);
    }

    /**
     * @param string $phone
     * @param string $code
     * 
     * @return array $data содержит ошибку 'error' или запись о том, что ошибок нет 'success'
     */
    public function isSmsCodeConsist(string $phone, string $code): array
    {
        $data = [];
        
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
        $data['success'] = 'Пользователь идентифицирован';
                
        return $data;
    }
}