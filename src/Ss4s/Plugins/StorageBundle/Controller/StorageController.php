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
 
namespace Ss4s\Plugins\StorageBundle\Controller;

use Ss4s\CoreBundle\Controller\Service\PluginController; 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;
 
class StorageController extends Controller implements PluginController
{
    public function indexAction($sid)
    {
        $parameters = $this->get('ss4s.current_service')->getArgs();

        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
        $storageService = $this->container->get('ss4s_plugins_storage.storage_service');
        $storageService->setParameters($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['Dossier']);
        if ($storageService->checkFolder($username)) {
            return $this->render('Ss4sPluginsStorageBundle:Default:index.html.twig', array(
                'folder' => false,
                'sid' => $sid
            ));
        } else {
            return $this->render('Ss4sPluginsStorageBundle:Default:index.html.twig', array(
                'folder' => true,
                'sid' => $sid
            ));
        }
    }

    public function createAction($sid) {
        $parameters = $this->get('ss4s.current_service')->getArgs();
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
        $storageService = $this->container->get('ss4s_plugins_storage.storage_service');
        $storageService->setParameters($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['Dossier']);
        if ($storageService->createFolder($username)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dossier créé'
            );
        }
        return $this->redirect($this->generateUrl('Ss4sPluginsStorageBundle_index', array(
            'sid' => $sid
        )));
    }

    public function deleteAction($sid) {
        $parameters = $this->get('ss4s.current_service')->getArgs();
        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();
        $storageService = $this->container->get('ss4s_plugins_storage.storage_service');
        $storageService->setParameters($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['Dossier']);
        $storageService->deleteFolder($username);
        $this->get('session')->getFlashBag()->add(
                        'success',
                        'Dossier supprimé'
                    );
        return $this->redirect($this->generateUrl('Ss4sPluginsStorageBundle_index', array(
            'sid' => $sid
        )));
    }
}