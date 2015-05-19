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
namespace Ss4s\Plugins\StorageBundle\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StorageService
{
	private $_conn;
	private $_dir;

	/*
	* @param string $host
	* @param string $user
	* @param string $pass
	*/
	public function __construct() 
	{
	}

	public function setParameters($host, $user, $pass, $directory) {
		if (isset($host) && isset($user) && isset($pass)) {
			$this->_conn = ssh2_connect($host, 22);  
	        ssh2_auth_password($this->_conn, $user, $pass);
	        $this->_dir = $directory;
		} else {
			$this->_conn = null;
		}
	}

	public function createFolder($user) {
		if ($this->_conn != null) {
			$folderpath = self::generateFullPath($user).$this->_dir;
	        $sftp = ssh2_sftp($this->_conn);
	  		if (ssh2_sftp_mkdir($sftp, $folderpath)) {
	  			return true;
	  		} else {
	  			return false;
	  		}
		} else {
			return false;
		}
	}

	public function checkFolder($user) {
		if ($this->_conn != null) {
			$folderpath = self::generateFullPath($user).$this->_dir;
			$sftp = ssh2_sftp($this->_conn);
			$fileExists = file_exists('ssh2.sftp://' . $sftp . $folderpath);
			return $fileExists;
		} else {
			return false;
		}
	}

	public function deleteFolder($user) {
		if ($this->_conn != null) {
			$folderpath = self::generateFullPath($user).$this->_dir;
	        $sftp = ssh2_sftp($this->_conn);
	  		if (ssh2_sftp_rmdir($sftp, $folderpath)) {
	  			return true;
	  		} else {
	  			return false;
	  		}
		} else {
			return false;
		}
	}

	public static function generateFullPath($user) {
		$deb = substr($user, 0, 1);
		$deb2 = substr($user, 0, 2);
		$folderpath = '/adhome/'.$deb.'/'.$deb2.'/'.$user.'/';
		return $folderpath;
	}

}