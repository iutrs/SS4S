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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * A controller to extend that contains all the common fields that are shared by the controllers in order to not repeat them in each one.
 *
 * @package Ss4s\CoreBundle\Controller\Services
 * @since Ss4s 1.0
 */
class ServicesController extends Controller {
	/**
	 * @var string $_servicesRepository The Services repository name.
	 */
	protected $_serviceRepository = 'Ss4sCoreBundle:Service';
}