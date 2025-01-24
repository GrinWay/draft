<?php

namespace App\Form;

use App\Entity\Foundry;
use App\Entity\FoundryOwner;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FoundryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null,
                options: [
                    'label_format' => \sprintf('[%s]', '%name%'),
                ])
            ->add('description')
            ->add('owner', EntityType::class, [
                'class' => FoundryOwner::class,
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Foundry::class,
        ]);
    }
}
