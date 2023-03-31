<?php

namespace App\Form;

use App\Entity\ProductionTime;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project', EntityType::class, [
                'label' => 'Projet concerné',
                'class' => Project::class,
                'query_builder' => function (ProjectRepository $projectRepository) {
                    return $projectRepository->createQueryBuilder('p')
                        ->where('p.deliveryDate is NULL');
                },
                'choice_label' => 'name',
            ])
            ->add('nbDays', TextType::class, [
                'label' => 'Nombre de jours'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductionTime::class,
        ]);
    }
}