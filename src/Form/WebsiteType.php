<?php

namespace App\Form;

use App\Entity\Server;
use App\Entity\Website;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class WebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', TextType::class, [
                "label" => "url"
            ])
            ->add('server', EntityType::class, [
                "label" => "Serveur",
                "class" => Server::class,
                "choice_label" => "name",
                "multiple" => false,
                "expanded" => false,
                "choices" => $options["servers"],
                "attr" => [
                    "class" => "select2-single"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Website::class,
            "servers" => []
        ]);
    }
}
