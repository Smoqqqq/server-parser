<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Server;
use App\Entity\UserGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('servers', EntityType::class, [
                "class" => Server::class,
                "choice_label" => "name",
                "multiple" => true,
                "expanded" => false,
                "required" => false,
                "choices" => $options["servers"],
                "attr" => [
                    "class" => "select2-multiple"
                ]
            ])
            ->add('users', EntityType::class, [
                "class" => User::class,
                "choice_label" => "email",
                "multiple" => true,
                "expanded" => false,
                "required" => false,
                "choices" => $options["users"],
                "attr" => [
                    "class" => "select2-multiple"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserGroup::class,
            "servers" => [],
            "users" => []
        ]);
    }
}
