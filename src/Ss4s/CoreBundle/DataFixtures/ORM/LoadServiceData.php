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
namespace Ss4s\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ss4s\CoreBundle\Entity\Service;

/**
 * Fixtures to add fake services to the database.
 */
final class LoadServiceData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $activeService = new Service();
        $activeService->setName('Active Service Test');
        $activeService->setDescription('An active service Test');
        $activeService->setStatus(0);
        $activeService->setServicePath('');
        $activeService->setServiceRoute('');
        $activeService->setImgPath('');

        $manager->persist($activeService);
        $manager->flush();

        $inactiveService = new Service();
        $inactiveService->setName('Inactive Service Test');
        $inactiveService->setDescription('An inactive service Test');
        $inactiveService->setStatus(1);
        $inactiveService->setServicePath('');
        $inactiveService->setServiceRoute('');
        $inactiveService->setImgPath('');

        $manager->persist($inactiveService);
        $manager->flush();
    }
}