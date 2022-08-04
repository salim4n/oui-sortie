<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use PhpParser\Builder\Class_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeurDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('mbInscriptionMax')
            ->add('infosSortie')
            ->add('lieu',EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=> 'nom'
            ])
            ->add('etat', EntityType::class,[
                'class'=> Etat::class,
                'choice_label'=> 'libelle'
            ])
            ->add('campus', EntityType::class,[
                'class'=> Campus::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
