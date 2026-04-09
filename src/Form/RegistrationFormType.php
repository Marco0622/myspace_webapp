<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
// use Symfony\Component\Validator\Constraints\Length;
// use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Votre nom...'],
                'label' => 'Nom*',
                'label_attr' => ['class' => 'text-white'],
            ])

            ->add('firstname', TextType::class, [
                'attr' => ['placeholder' => 'Votre prenom...'],
                'label' => 'Prénom*',
                'label_attr' => ['class' => 'text-white'],
            ])

            ->add('pseudo', TextType::class, [ 'required' => false,
                'attr' => ['placeholder' => 'Votre pseudo...'],
                'label' => 'Pseudo',
                'label_attr' => ['class' => 'text-white'],
            ])

            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance*',
                'label_attr' => ['class' => 'text-white'],
            ])

            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'nom@exemple.com'],
                'label' => 'Adresse e-mail*',
                'label_attr' => ['class' => 'text-white'],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les conditions d'utilisation.*",
                'label_attr' => ['class' => 'text-white'],
                'constraints' => [
                    new IsTrue(
                        message: "Vous devez accepter les conditions d'utilisation.",
                    ),
                ],
            ])
            
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'label_attr' => ['class' => 'text-white'],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
                'required' => true,
                'first_options' => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Votre mot de passe...']],
                'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => ['placeholder' => 'Votre mot de passe de comfirmation...']],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'btn btn-light btn-lg w-100']
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
