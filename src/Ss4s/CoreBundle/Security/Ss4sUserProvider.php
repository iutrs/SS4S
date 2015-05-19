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

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\Mapping as ORM;
use Ss4s\CoreBundle\Security\Ss4sUser;

class Ss4sUserProvider implements UserProviderInterface
{
    /**
     * @var Container
     */
    protected $_serviceContainer;

    /**
     * @param Container $serviceContainer
     */
    public function __construct(Container $serviceContainer)
    {
        $this->_serviceContainer = $serviceContainer;
    }

    public function loadUserByUsername($username)
    {
        // Obtention des informations sur l'utilisateur via LDAP
        // Retour d'un tableau $data : $data['fullname'] et $data['groups']
        if(!$ldapCheck = $this->_serviceContainer->get('ss4s.ldap_check')){
            throw new UsernameNotFoundException();
        }
        $userInfos = $ldapCheck->getInfos($username);

        // On regarde dans la base si l'utilisateur est déclaré comme administrateur
        $superAdmin = false;
        $administratorCheck = $this->_serviceContainer->get('ss4s.administrator_check');
        $roles = $administratorCheck->getAdministratorType($username);

        $groups = array();
        // On enlève le préfixe "GG_" devant les groupes importés
        foreach($userInfos['groups'] as $u) {
            $groups[] = substr($u, 3);
        }

        // On créer l'User 
        $user = new Ss4sUser($username, '', '', $roles, $userInfos['fullname'], $groups);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Ss4sUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Ss4s\CoreBundle\Security\Ss4sUser';
    }
}

?>