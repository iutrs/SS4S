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
use Symfony\Component\OptionsResolver\OptionsResolverInterface; 
use Ss4s\CoreBundle\Entity\Service;
use Ss4s\CoreBundle\Service\PluginsFinder;
  
class ServicePathType extends AbstractType
{
    /**
     * @var PluginsFinder
     */
 	private $_existingServices;

    /**
     * @param PluginsFinder $pluginsFinder
     */
    public function __construct(PluginsFinder $pluginsFinder)
    {
        $this->_existingServices = $pluginsFinder->getExistingServices();
    }   

    /**
     * @param OptionsResolverInterface $resolver
     */ 
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = array();
        foreach($this->_existingServices as $s){
             $choices[$s->getServicePath()] = $s->getName();
        }

        $resolver->setDefaults(array(
            'choices' => $choices
        ));
    }

    /** 
     * @return string 
     */
    public function getParent()
    {
    	return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'servicepath';
    }
}