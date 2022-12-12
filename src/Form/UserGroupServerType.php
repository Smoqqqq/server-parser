<?php

namespace App\Form;

use App\Entity\Server;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGroupServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "servers" => []
        ]);
    }
}
