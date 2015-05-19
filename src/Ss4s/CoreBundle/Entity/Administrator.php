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
namespace Ss4s\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Administrator
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Administrator
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;

    /**
     * @var boolean 
     *
     * @ORM\Column(name="is_super_admin", type="boolean")
     */
    private $isSuperAdmin;

    /**
     * @var boolean 
     *
     * @ORM\Column(name="is_father_of_all", type="boolean")
     */
    private $isFatherOfAll;

    public function __construct()
    {
        $this->isFatherOfAll = false;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Administrator
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set isSuperAdmin
     *
     * @param boolean $isSuperAdmin
     */
    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    /**
     * Get isSuperAdmin
     *
     * @return boolean 
     */
    public function getIsSuperAdmin()
    {
        return $this->isSuperAdmin;
    }

    /**
     * Get isFatherOfAll
     *
     * @return boolean 
     */
    public function getIsFatherOfAll()
    {
        return $this->isFatherOfAll;
    }
}
