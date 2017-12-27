<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Форма обратной связи
 */
class FeedbackType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
                'constraints' => [
                    new Type([
                        'type' => 'string',
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern'=> "/^8[0-9]{10}$/x",
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new Type([
                        'type' => 'string',
                    ]),
                ],
            ]);
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'feedback';
    }

}
