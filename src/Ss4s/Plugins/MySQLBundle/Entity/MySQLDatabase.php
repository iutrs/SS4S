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
namespace Ss4s\Plugins\MySQLBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ss4s\CoreBundle\Entity\Service;

/**
 * MySQLDatabase
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ss4s\Plugins\MySQLBundle\Entity\MySQLDatabaseRepository")
 */
class MySQLDatabase
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
     * @ORM\Column(name="db_name", type="string", length=25)
     */
    private $dbName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="db_date", type="datetime")
     */
    private $dbDate;

    /**
     *@var string
     *
     *@ORM\Column(name="db_user", type="string", length=30)
     */
    private $dbUser;

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
     * Set dbName
     *
     * @param string $dbName
     * @return MySQLDatabase
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    
        return $this;
    }

    /**
     * Get dbName
     *
     * @return string 
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Set dbDate
     *
     * @param \DateTime $dbDate
     * @return MySQLDatabase
     */
    public function setDbDate($dbDate)
    {
        $this->dbDate = $dbDate;
    
        return $this;
    }

    /**
     * Get dbDate
     *
     * @return \DateTime 
     */
    public function getDbDate()
    {
        return $this->dbDate;
    }

    /**
     * Set dbUser
     *
     * @param string $dbUser
     * @return MySQLDatabase
     */
    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    
        return $this;
    }

    /**
     * Get dbUser
     *
     * @return string 
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }
}
