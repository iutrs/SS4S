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
namespace Ss4s\CoreBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\User\EquatableInterface;

class Ss4sUser implements UserInterface, EquatableInterface
{
    /**
     * @var Container
     */
    private $_serviceContainer;

    /**
     * @var string
     */
    private $_username;

    /**
     * @var string
     */
    private $_password;

    /**
     * @var string
     */
    private $_salt;

    /**
     * @var array
     */
    private $_roles;

    /**
     * @var string
     */
    private $_fullname;

    /**
     * @var array
     */
    private $_groups;

    /**
     * @param Container $serviceContainer
     * @param string $username
     * @param string $password
     * @param string $salt 
     * @param array $roles
     * @param array $groups
     */
    public function __construct($username, $password, $salt, array $roles, $fullname, array $groups)
    {
        $this->_username = $username;
        $this->_password = $password;
        $this->_salt = $salt;
        $this->_roles = $roles;
        $this->_fullname = $fullname;
        $this->_groups = $groups;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->_salt;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->_roles;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->_fullname;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->_groups;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if($this->getUsername() != $user->getUsername()) {
            return false;
        }    

        return true;
    }
}