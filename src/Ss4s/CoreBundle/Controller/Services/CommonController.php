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

namespace Ss4s\CoreBundle\Controller\Services;

use Ss4s\CoreBundle\Controller\Services\ServicesController;

/**
 * The common services controller that execute actions used by both administrators and users.
 *
 * @package Ss4s\CoreBundle\Controller\Services
 * @since Ss4s 1.0
 */
final class CommonController extends ServicesController {
	/**
	 * This action lists all the services for both administrators and users.
	 */
	public function listAction() {
		// Get all services.
		$services = $this->_getAll();

		// Retrieves the admin role in order to display admin-specific areas.
		$adminRole = $this->container
			->getParameter('admin_role');

		return $this->render('Ss4sCoreBundle:Services/Common:list.html.twig', array(
			'services' => $services,
			'admin_role' => $adminRole
		));
	}

	// Utilitiy functions used by several actions.

	/**
	 * Find all services and do some security and role checks in order to display only the accessible ones.
	 */
	private function _getAll() {
		// Get the security context.
		$security = $this->get('security.context');

		// Creates the initial query.
		$query = $this->getDoctrine()
			->getManager()
			->getRepository($this->_serviceRepository)
			->createQueryBuilder('s');

		// Adds the user conditions, as they can't see inactive services.
		if ($security->isGranted('ROLE_ADMIN') === false) {
			$query->where('s.status = :active')
				->setParameter(':active', 0)
				->orderBy('s.name', 'ASC');
		}

		$services = $query->getQuery()
			->getResult();

		return $services;
	}

	// JSON related functions

	/**
	 * Gets all the services in a JSON fromatted array.
	 *
	 * @return void The function returns nothing as it uses the exit() function to end the script. But it echoes the json array before its shutdown.
	 */
	public function getAllJsonAction() {
		$services = $this->_getAll();

		// Echoes the json formatted array.
		echo json_encode($services);
		exit();
	}
}