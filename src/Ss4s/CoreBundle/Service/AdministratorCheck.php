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

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class AdministratorCheck
{
	/**
	 * @var Administrator
	 */
	protected $_administratorRepository;

	/**
	 * @param Doctrine $doctrine
	 */
	public function __construct(Doctrine $doctrine)
	{
		$this->_administratorRepository = $doctrine->getRepository('Ss4sCoreBundle:Administrator');
	}

	/**
	 * @param string $username
	 *
	 * @return array
	 */
	public function getAdministratorType($username)
	{
		// On regarde si le user est un admin
		if($admin = $this->_administratorRepository->findOneByUsername($username)){
			if($admin->getIsFatherOfAll()){
				return array('ROLE_FATHER_OF_ALL');
			}
			if($admin->getIsSuperAdmin()){
				return array('ROLE_SUPERADMIN');

			}
			return array('ROLE_ADMIN');
		}

		return array('ROLE_USER');
	}
}