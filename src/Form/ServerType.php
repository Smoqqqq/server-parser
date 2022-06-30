<?php

namespace App\Form;

use App\Entity\Server;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom"
            ])
            ->add('host', TextType::class, [
                "label" => "Hôte"
            ])
            ->add('username', TextType::class, [
                "label" => "Nom d'utilisateur"
            ])
            ->add('password', TextType::class, [
                "label" => "Mot de passe"
            ])
            ->add("rootDirectory", TextType::class, [
                "label" => "Répertoire raçine (sans '/' à la fin)",
                "attr" => [
                    "placeholder" => "vhosts"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Server::class,
        ]);
    }
}
