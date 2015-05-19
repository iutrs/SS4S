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
namespace Ss4s\Plugins\MySQLBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
use Ss4s\CoreBundle\Controller\Service\PluginController; 
use Ss4s\Plugins\MySQLBundle\Entity\MySQLDatabase;
use Ss4s\Plugins\MySQLBundle\Form\MySQLDatabaseType;

class MySQLController extends Controller implements PluginController
{
	public function indexAction($sid) {
		/* Récupération des paramètres (args.yml) */
		$parameters = $this->get('ss4s.current_service')->getArgs();

		/*Formulaire*/
		$mysql = new MySQLDatabase();
	    $form = $this->createForm(new MySQLDatabaseType(), $mysql);

  		/* Récupération du service MySQLService*/
  		$mysqlService = $this->container->get('ss4s_plugins_my_sql.mysql_service');
  		$mysqlService->setParams($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['passlength']);

		/* Récupération du username de l'user connecté*/
		$username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		/*$correctUser = str_replace('.', '_', $username);*/

		$repository = $this->getDoctrine()->getRepository('Ss4sPluginsMySQLBundle:MySQLDatabase');

	    $request = $this->get('request');
	    if($request->getMethod() == 'POST') {
	      	$form->bind($request);
	      	if ($form->isValid()) {
	      		if (self::checkDbName($form->getData()->getDbName()) == false) {
	      			$this->get('session')->getFlashBag()->add(
		                'error',
		                'Echec création : Le nom de la base contient un caractère interdit !'
		            );
		        	return $this->redirect($this->generateUrl('ss4s_plugins_mysql_index'));
	      		}
		        if (!$mysqlService->userExists($username)) {
		        	if ($pass = $mysqlService->createUser($username)) {
				        $this->get('session')->getFlashBag()->add(
			                'success',
			                "L'utilisateur ".$username." a bien été créé ! Le mot de passe phpMyAdmin est ".$pass
			            );
		        	}
		        }

		        /*$completeName = $correctUser."_".$form->getData()->getDbName();*/
		        if (!$mysqlService->databaseExists($form->getData()->getDbName())
		        	&& $mysqlService->createDb($form->getData()->getDbName())
		        		&& $mysqlService->grant($form->getData()->getDbName(), $username)) {

			        /* Utilisation de Doctrine pour la base de l'appli */
			        $em = $this->getDoctrine()->getManager();
	      			$dbs = $repository->findByDbUser($username);
			        $mysql->setDbDate(new \DateTime('now'));
			        $mysql->setDbUser($username);
			        $em->persist($mysql);
			        $em->flush();

			        $this->get('session')->getFlashBag()->add(
		                'success',
		                'La base "'.$form->getData()->getDbName().'" a bien été ajoutée.'
		            );
		        } else {
					$this->get('session')->getFlashBag()->add(
		                'error',
		                'La base "'.$form->getData()->getDbName().'" existe déjà !'
		            );
		        }

		        return $this->redirect($this->generateUrl('ss4s_plugins_mysql_index', array(
		        	'sid' => $sid,
		        )));
      		}
      	}


      	$dbs = $repository->findByDbUser($username);

      	foreach ($dbs as $db) {
	        /*$completeName = $correctUser."_".$db->getDbName();*/
      		if (!$mysqlService->databaseExists($db->getDbName())) {
      			$this->deleteDb($db->getId());
      		}
      	}

      	$dbs = $repository->findByDbUser($username);
      	$maxReached = false;
      	if (count($dbs) >= $parameters['maximum']) {
      		$maxReached = true;
      	}

      	$administrator = $this->getDoctrine()->getRepository('Ss4sCoreBundle:Administrator')->findByUsername($username);
      	if ($administrator) {
      		$dbsAll = $repository->findAll();
      		return $this->render('Ss4sPluginsMySQLBundle:Default:index.html.twig', array(
	      		'form' => $form->createView(),
	      		'dbs' => $dbs,
	      		'maxReached' => $maxReached,
	      		'dbsAll' => $dbsAll,
	      		'admin' => true
	    	));
      	}

    	return $this->render('Ss4sPluginsMySQLBundle:Default:index.html.twig', array(
      		'form' => $form->createView(),
      		'dbs' => $dbs,
      		'maxReached' => $maxReached,
      		'admin' => false
    	));
	}

	private function deleteDb($id) {
		/* Utilisation de Doctrine pour la base de l'appli */
		$em = $this->getDoctrine()->getManager();
		$db = $em->getRepository('Ss4sPluginsMySQLBundle:MySQLDatabase')
			->find($id);
		$em->remove($db);
		$em->flush();
	}

	public function deleteAction($sid, $id) {
		/* Récupération des paramètres (args.yml) */
		$parameters = $this->get('ss4s.current_service')->getArgs();

		/* Récupération du username de l'user connecté */
		$username = $this->container->get('security.context')->getToken()->getUser()->getUsername();

		/* Récupération du service MySQLService*/
		$mysqlService = $this->container->get('ss4s_plugins_my_sql.mysql_service');
  		$mysqlService->setParams($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['passlength']);

		/* Utilisation de Doctrine pour la base de l'appli */
		$em = $this->getDoctrine()->getManager();
		$db = $em->getRepository('Ss4sPluginsMySQLBundle:MySQLDatabase')
			->find($id);

		$mysqlService->deleteDb($db->getDbName());
		$this->deleteDb($id);

		$this->get('session')->getFlashBag()->add(
            'success',
            'La base "'.$db->getDbName().'" a bien été supprimée.'
        );

        return $this->redirect($this->generateUrl('ss4s_plugins_mysql_index', array(
		        	'sid' => $sid,
		        )));
	}

	public function forgottenAction($sid) {
		/* Récupération des paramètres (args.yml) */
		$parameters = $this->get('ss4s.current_service')->getArgs();
		/* Récupération du username de l'user connecté*/
		$username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		/* Récupération du service MySQLService*/
		$mysqlService = $this->container->get('ss4s_plugins_my_sql.mysql_service');
  		$mysqlService->setParams($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['passlength']);

  		/* Si l'utilisateur mysql existe */
		if ($mysqlService->userExists($username)) {
			/* On change le mot de passe */
			$pass = $mysqlService->changePass($username);

			$this->get('session')->getFlashBag()->add(
	            'success',
	            'Nouveau mot de passe phpMyAdmin : '.$pass
	        );
		} else {
			$this->get('session')->getFlashBag()->add(
	            'error',
	            'Impossible, il faut d\'abord créer une base !'
	        );
		}
        return $this->redirect($this->generateUrl('ss4s_plugins_mysql_index', array(
		        	'sid' => $sid,
		        )));
	}

	private static function checkDbName($name) {
		/* Vérification qu'il n'y a pas de caractère interdit dans le nom de la base */
		$forbidden_chars = "@,#,$,%,&, ";
		$array = explode(",",$forbidden_chars);
		$bool = true;
		foreach ($array as $char) {
			$pos = strpos($name, $char);
			if ($pos !== false) {
				$bool = false;
			}
		}
		return $bool;
	}
}