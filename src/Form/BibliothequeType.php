<?php

namespace App\Form;

use App\Entity\Bibliotheque;
use App\Entity\Jeu;
use App\Entity\Member;
use App\Repository\JeuRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BibliothequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $bibliotheque = $options['data'] ?? null;
        $membre = $bibliotheque ? $bibliotheque->getCreateur() : null;

        $builder    
            ->add('description')
            ->add('publiee')
            ->add('createur', EntityType::class, [
                'class' => Member::class,
                'choice_label' => 'nom', 
                'disabled' => true, 
            ])
            ->add('jeux', EntityType::class, [
                'class' => Jeu::class,
                'choice_label' => 'nom', 
                'multiple' => true,
                'expanded' => true, 
                'query_builder' => function (JeuRepository $jeuRepository) use ($membre) {
                    return $jeuRepository->createQueryBuilder('j')
                        ->innerJoin('j.coffre', 'c')
                        ->innerJoin('c.membre', 'm')
                        ->where('m.id = :membreId')
                        ->setParameter('membreId', $membre->getId());
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bibliotheque::class,
        ]);
    }
}
