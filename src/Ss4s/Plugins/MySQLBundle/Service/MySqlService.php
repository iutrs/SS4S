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
namespace Ss4s\Plugins\MySQLBundle\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MySqlService
{
	private $_db;
	private $_pass_length;

	/*
	* @param string $host
	* @param string $user
	* @param string $pass
	* @param int $length
	*/
	public function __construct()
	{
		/*$this->_pass_length = $length;
		$this->_db = new \mysqli($host, $user, $pass);
		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}*/
	}

	public function setParams($host, $user, $pass, $length) {
		$this->_pass_length = $length;
		if (isset($host) && isset($user) && isset($pass)) {
			$this->_db = new \mysqli($host, $user, $pass);
			if (mysqli_connect_errno()) {
			    printf("Connect failed: %s\n", mysqli_connect_error());
			    exit();
			}
		} else {
			$this->_db = null;
		}
	}

	public function userExists($username) {
		if ($this->_db != null) {
			$stmt = $this->_db->prepare("SELECT * FROM mysql.user WHERE user=?");
			$stmt->bind_param('s', $username);
			$stmt->execute();
			if ($data = $stmt->fetch()) {
				$stmt->close();
				return true;
			} else {
				$stmt->close();
				return false;
			}
		} else {
			return false;
		}
	}

	public function databaseExists($db) {
		if ($this->_db != null) {
			$request = "SHOW databases like '".$db."'";
			$stmt = $this->_db->prepare($request);
			$stmt->execute();
			if ($data = $stmt->fetch()) {
				$stmt->close();
				return true;
			} else {
				$stmt->close();
				return false;
			}
		} else {
			return false;
		}
	}

	public function createUser($username) {
		$pass = $this->generatePass();
		$request = "CREATE USER '".$username."' IDENTIFIED BY '".$pass."'";
		$stmt= $this->_db->prepare($request);
		$stmt->execute();
		$stmt->close();
		return $pass;
	}

	public function createDb($name) {
		$bool = false;
		$request = "CREATE DATABASE ".$name;
		$stmt = $this->_db->prepare($request);
		if ($stmt->execute()) {
			$bool = true;
		}
		$stmt->close();
		return $bool;
	}

	public function grant($db, $user) {
		$bool = false;
		$request = "GRANT ALL PRIVILEGES ON ".$db.".* TO '".$user."'";
		$stmt = $this->_db->prepare($request);
		if ($stmt->execute()) {
			$bool = true;
		}
		$stmt->close();
		$this->_db->query("FLUSH PRIVILEGES");
		return $bool;
	}

	public function deleteDb($name) {
		$request = "DROP DATABASE ".$name;
		$stmt = $this->_db->prepare($request);
		$stmt->execute();
		$stmt->close();
	}

	public function changePass($username) {
		$pass = $this->generatePass();
		$request = "SET PASSWORD FOR '".$username."' = PASSWORD('".$pass."')";
		$stmt = $this->_db->prepare($request);
		$stmt->execute();
		$stmt->close();
		return $pass;
	}

	private function generatePass() {
		$word = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,1,2,3,4,5,6,7,8,9,0";  
		$array=explode(",",$word);  
		shuffle($array);  
		$newstring = implode($array,"");  
		return substr($newstring, 0, $this->_pass_length);
	}
}