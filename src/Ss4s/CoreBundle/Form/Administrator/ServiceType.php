<?php
/*
 * Copyright (C) 2013-2014 F.Schmitt, A.Haas, S.Reiss, E.Blindauer. All Rights Reserved.
 *
 *  This file is part of SS4S.
 *
 *  SS4S is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SS4S is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Foobar.  If not, see <http://www.gnu.org/licenses/>
 */
namespace Ss4s\CoreBundle\Form\Administrator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ss4s\CoreBundle\Form\Administrator\CollegeGroupType;
use Ss4s\CoreBundle\Entity\CollegeGroup;
use Ss4s\CoreBundle\Entity\Service;

class ServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'textarea')
            ->add('status', 'choice', array( 
                'choices' => array(
                    0 => 'En ligne', 
                    1 => 'En développement'
                ),
                'multiple' => false
            ))
            ->add('servicePath', 'servicepath', array(
                'empty_value' => 'Selectionner le plugin...'
            ))
            ->add('imgPath', 'text', array(
                'required' => false
            ))
            ->add('collegeGroups', 'collection', array(
                'type' => new CollegeGroupType(),
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ss4s\CoreBundle\Entity\Service'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'service';
    }
}
