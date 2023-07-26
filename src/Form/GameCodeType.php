<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class GameCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', NumberType::class, [
                'label' => 'Game Code',
                'required' => true,
                'attr' => [
                    'maxlength' => '6',
                    'class' => 'form-control',
                    'autofocus' => 'autofocus',
                    'placeholder' => 'Game Code',
                ],
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                    new Length(['min' => 6, 'max' => 6]),
                ],
                'invalid_message' => 'Please enter a valid game code (6 digits).',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
