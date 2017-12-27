<?php

namespace AppBundle\Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class AuthorizationValidator  extends AbstractValidator
{
    /**
     * Возвращает список полей с правилами валидации
     *
     * @return Collection
     */
    protected function getConstraint(): Collection
    {
        return new Collection([
            'phone' => $this->getPhoneRules(),
            'code' => $this->getCodeRules(),
        ]);
    }
    
    /**
     * Возвращает текст сообщения об ошибке не заполненого поля
     *
     * @return string
     */
    private function getMessageForNotBlank(): string 
    {
        return 'Поле обязательно к заполнению';
    }

    /**
     * Возвращает правила валидации для данных введённых в строку поиска
     *
     * @return array
     */
    private function getPhoneRules(): array 
    {
        return [
            new Assert\NotBlank([
                'message' => $this->getMessageForNotBlank(),
            ]),
            new Assert\Regex([
                'pattern' => "/^\+79\d{9}$/",
                'message' => 'Вы пытаетесь ввести недопустимые символы. Введите номер телефона в формате +7**********',
            ]),
        ];
    }
    
    /**
     * Возвращает правила валидации для данных введённых в строку поиска
     *
     * @return array
     */
    private function getCodeRules(): array
    {
        return [
            new Assert\Regex([
                'pattern' => "/^[0-9]{4}$/",
                'message' => 'Вы пытаетесь ввести недопустимые символы. Введите четырёхзначный код.',
            ]),
        ];
    }
}
