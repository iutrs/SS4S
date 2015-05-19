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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Ss4s\CoreBundle\Entity\Service;
 
class PluginsFinder
{
  private $_fileFinder;
  private $_fileSystem;
  private $_servicesDirPath;
  private $_serviceRepository;

/** 
 * @param string $servicesDir
 * @param Doctrine $doctrine
 */
public function __construct($servicesDirPath, Doctrine $doctrine)
{
$this->_fileFinder = new Finder();
$this->_fileSystem = new Filesystem();
$this->_servicesDirPath = $servicesDirPath;
$this->_serviceRepository = $doctrine->getRepository('Ss4sCoreBundle:Service');
}

/**
 * @return array 
 */
public function getExistingServices()
{
	$foundServices = array();
	$serviceControllerPath = $this->_servicesDirPath.'/*/Controller/';
	// Permet de trouver tous les Controller dans le dossier des plugins

	// if($this->_fileSystem->exists($serviceControllerPath)) {
	$this->_fileFinder->files()->in($serviceControllerPath);

	foreach($this->_fileFinder as $file) {
		// On obtient le chemin absolu du service
		$classFile = $file->getRealPath();
		
		// On reconstitue le namespace du service
		$controllerClass = '';
		$controllerPath = str_replace('\\','/',$file->getRealPath());
		$controllerFrags = explode('/',$controllerPath);

		$i = count($controllerFrags);
		$servicePath = $controllerFrags[$i-5]. $controllerFrags[$i-4]. $controllerFrags[$i-3];
		$serviceName = substr($controllerFrags[$i-3], 0, count($controllerFrags[$i-3])-7);
		// Instanciation d'un service
		$service = new Service();
		$service->setName($serviceName);
		$service->setServicePath($servicePath);
		// Si le service n'est pas déjà utilisé dans l'application, on l'ajoute au tableau retour
		$foundServices[] = $service;
	}
	// }

	return $foundServices;
  }
}
