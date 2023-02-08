<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'entrez votre mot de passe',
                'attr' => [
                    'placeholder' => 'mot de passe',
                    'class' => 'form-control my-4 ' //bootstrap pour mise en forme
                ]
            ]);
    }

    public function configureOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
