<?php

namespace App\Form;

use App\Entity\Bulletin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BulletinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre:',
                'attr' => [
                    'class' => 'mb-5'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie:',
                'choices' => [
                    'Général' => 'General',
                    'Urgent' => 'Urgent',
                    'Divers' => 'Divers'
                ],
                'expanded' => false, // change l'affichage en boutons plutôt que liste
                'multiple' => false, // permet de faire un choix multiple
                'attr' => [
                    'class' => 'mb-5'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu:',
                'attr' => [
                    'class' => 'mb-5'
                ]
            ])
            ->add('valider', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-success btn-lg px-5',
                    'style' => 'margin-top: 5px;'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bulletin::class,
        ]);
    }
}
