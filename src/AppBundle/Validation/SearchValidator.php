<?php

namespace AppBundle\Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class SearchValidator extends AbstractValidator
{
    /**
     * Возвращает список полей с правилами валидации
     *
     * @return Collection
     */
    protected function getConstraint(): Collection
    {
        return new Collection([
            'name' => $this->getSearchRules(),
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
    private function getSearchRules(): array 
    {
        return [
            new Assert\NotBlank([
                'message' => $this->getMessageForNotBlank(),
            ]),
            new Assert\Regex([
                'pattern' => "/^[a-zA-ZА-Яа-я0-9ёЁ\–\—\‒\―\«\»\-\'\"\s]*$/u",
                'message' => 'Вы пытаетесь ввести недопустимые символы. Разрешается вводить только буквы, цифры, пробелы, кавычки и дефисы',
            ]),
        ];
    }
}