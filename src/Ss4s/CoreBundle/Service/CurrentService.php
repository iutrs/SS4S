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
namespace Ss4s\CoreBundle\Service;

use Symfony\Component\Yaml\Parser;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Ss4s\CoreBundle\Entity\ServiceRepository;
use Ss4s\CoreBundle\Entity\Service;

class CurrentService 
{
	/**
	 * @var ServiceRepository
	 */
	private $_serviceRepository;

	/**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $_serviceContainer;

	/**
	 * @var array|null
	 */
	private $_args;

	/**
	 * @param Doctrine $doctrine
	 * @param Container $serviceContainer
	 */
	public function __construct(Doctrine $doctrine, $serviceContainer)
	{
		$this->_serviceRepository = $doctrine->getRepository('Ss4sCoreBundle:Service');
		$this->_serviceContainer = $serviceContainer;
	}

	/**
	 * @param Service|null $service
	 */
	public function setCurrentService($service)
	{
		if(!is_null($service)) {
			// On va chercher dans les paramètres de services le numéro d'implémentation
			// Celui-ci correspond en fait à l'id du service 
			// Ce numéro doit se retrouver dans un fichier args.yml au format suivant:
			// ex: 
			// 7:
			//     example: yolo
			$path = '';
	    	$kernel = $this->_serviceContainer->get('kernel');
			$path = $kernel->locateResource('@'.$service->getServicePath().'/Resources/config/args.yml');

	    	$yaml = new Parser();
			$args = $yaml->parse(file_get_contents($path));
			if(array_key_exists($service->getId(), $args))
				$this->_args = $args[$service->getId()];
		} else {
			$this->_args = null;
		}
	}

	/**
	 * @param integer $serviceId
	 *
	 * @return array|null
	 */
	public function getArgs() 
	{
		return $this->_args;
	}
}