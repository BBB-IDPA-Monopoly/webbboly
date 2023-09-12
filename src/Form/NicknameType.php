<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NicknameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class, [
                'label' => 'Spitzname',
                'required' => true,
                'attr' => [
                    'maxlength' => '20',
                    'class' => 'form-control',
                    'autofocus' => 'autofocus',
                    'placeholder' => 'Spitzname eingeben',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 20]),
                    new Expression(
                        'value not in ' . json_encode($options['taken_nicknames']),
                        'Dieser Spitzname wurde bereits benutzt.'
                    ),
                ],
                'invalid_message' => 'Bitte geben Sie einen gültigen Spitzname ein (3-20 zeichen).',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'taken_nicknames' => [],
        ]);
    }
}
