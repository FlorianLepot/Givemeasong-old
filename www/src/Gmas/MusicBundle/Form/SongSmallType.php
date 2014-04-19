<?php

namespace Gmas\MusicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SongSmallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('youtubeid', null, array('label' => 'Lien', 'attr' => array('placeholder' => 'Lien youtube')))
            ->add('category','entity', array('label' => 'Catégorie', 'class'=>'Gmas\MusicBundle\Entity\Categories', 'property'=>'name', ));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gmas\MusicBundle\Entity\Song'
        ));
    }

    public function getName()
    {
        return 'gmas_musicbundle_songsmalltype';
    }
}
