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
use Doctrine\Common\Collections\ArrayCollection;
use Ss4s\CoreBundle\Entity\CollegeGroup;

/**
 * Service
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ss4s\CoreBundle\Entity\ServiceRepository")
 */
class Service
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="service_path", type="string")
     */
    private $servicePath;

    /**
     * @var string
     *
     * @ORM\Column(name="service_route", type="string")
     */
    private $serviceRoute;

    /**
     * @var string
     *
     * @ORM\Column(name="img_path", type="string", nullable=true)
     */
    private $imgPath;

    /**
     * @ORM\ManyToMany(targetEntity="Ss4s\CoreBundle\Entity\CollegeGroup", cascade={"persist"})
     */
    private $collegeGroups;

    public function __construct()
    {
        $this->params = new ArrayCollection();
        $this->collegeGroups = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Service
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Service
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Service
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set servicePath
     *
     * @param string $servicePath
     */
    public function setServicePath($servicePath)
    {
        $this->servicePath = $servicePath;
    }

    /**
     * Get servicePath
     *
     * @return string 
     */
    public function getServicePath()
    {
        return $this->servicePath;
    }

    /**
     * Set serviceRoute
     *
     * @param string $serviceRoute
     */
    public function setServiceRoute($serviceRoute)
    {
        $this->serviceRoute = $serviceRoute;
    }

    /**
     * Get serviceRoute
     *
     * @return string
     */
    public function getServiceRoute()
    {
        return $this->serviceRoute;
    }

    /**
     * Set imgPath
     *
     * @param string $imgPath
     */
    public function setImgPath($imgPath)
    {
        $this->imgPath = $imgPath;
    }

    /**
     * Get imgPath
     *
     * @return string 
     */
    public function getImgPath()
    {
        return $this->imgPath;
    }

    /**
     * Set collegeGroups
     *
     * @param ArrayCollection $collegeGroups
     */
    public function setCollegeGroups(ArrayCollection $collegeGroups)
    {
        $this->collegeGroups = $collegeGroups;
    }

    /**
     * Get collegeGroups
     *
     * @return ArrayCollection
     */
    public function getCollegeGroups()
    {
        return $this->collegeGroups;
    }

    /**
     * Add collegeGroup 
     *
     * @param CollegeGroup $collegeGroup
     */
    public function addCollegeGroup(CollegeGroup $collegeGroup)
    {
        $this->collegeGroups->add($collegeGroup);
    }

    /** 
     * Remove collegeGroup
     *
     * @param CollegeGroup $collegeGroup
     */
    public function removeCollegeGroup(CollegeGroup $collegeGroup)
    {
        $this->collegeGroups->removeElement($collegeGroup);
    }

    /**
     * Equals Service
     * 
     * @param Service $service
     */
    public function equals(Service $service)
    {
        if($this->getId() !== $service->getId()) {
            return false;
        } else if ($this->getCollegeGroups() !== $service->getCollegeGroups()) {
            return false;
        } else if ($this->getParams() !== $service->getParams()) {
            return false;
        }

        return true;
    }
}
