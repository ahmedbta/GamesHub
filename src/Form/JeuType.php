<?php

namespace App\Form;

use App\Entity\Bibliotheque;
use App\Entity\Coffre;
use App\Entity\Jeu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class JeuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        
        $builder
            ->add('nom')
            ->add('annee')
            ->add('nb_joueurs_max')
            ->add('nb_joueurs_mini')
            ->add('temps_jeu')
            ->add('type')
            ->add('description')
            ->add('imageFile', VichImageType::class, [
                'required' => false,  
                'label' => 'Upload Image', 
                'mapped' => true,
                ])
            ->add('coffre', null, [
                'disabled' => true, 
            ])  ;
         
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Jeu::class,
            'bibliotheques' => [], 
        ]);
    }
} 