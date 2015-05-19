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

class LDAPCheck
{
  /**
   * @var string
   */
  protected $_ldapServer;

  /**
   * @var string
   */
  protected $_ldapUser;

  /**
   * @var string
   */
  protected $_ldapPass;
 
  /**
   * Constructeur
   *
   * @param string $server
   * @param string $user
   * @param string $pass
   */
  public function __construct($server, $user, $pass)
  {
    $this->_ldapServer = $server;
    $this->_ldapUser = $user;
    $this->_ldapPass = $pass;
  }
 
  /**
   * @param string $username
   *
   * @return array|null
   */
  public function getInfos($username)
  {
    return $returnData = null;
    
    if($ldapConnection = ldap_connect($this->_ldapServer)){
      if(ldap_bind($ldapConnection, $this->_ldapUser, $this->_ldapPass)){
        $ldapTree = "OU=people,DC=ad,DC=unistra,DC=fr";

        $searchLimit = array('memberof', 'displayname');
        $result = ldap_search($ldapConnection, $ldapTree, '(cn='.$username.')', $searchLimit);
        if(count($data = ldap_get_entries($ldapConnection, $result)) > 1){
          $groups = array();
          for($i=0; $i<count($data[0]['memberof'])-1; $i++){
            $groups[$i] = $data[0]['memberof'][$i];
          }

          $returnData['fullname'] = utf8_encode($data[0]['displayname'][0]);
          foreach($groups as $g){
            $res = explode(',',$g);
            foreach ($res as $r) {
              if(substr($r,0,3) == 'CN='){
                $returnData['groups'][] = utf8_encode(substr($r,3));
              }
            }
          }
        }
      }
      ldap_close($ldapConnection);
    }

    return $returnData;
  }

  /**
   * @param string $username
   *
   * @return string|null
   */
  public function getFullname($username)
  {
    $fullname = null;
    if($ldapConnection = ldap_connect($this->_ldapServer)){
      if(ldap_bind($ldapConnection, $this->_ldapUser, $this->_ldapPass)){
        $ldapTree = "OU=people,DC=ad,DC=unistra,DC=fr";

        $searchLimit = array('displayname');
        $result = ldap_search($ldapConnection, $ldapTree, '(cn='.$username.')', $searchLimit);
        if(count($data = ldap_get_entries($ldapConnection, $result)) > 1){
          $fullname = utf8_encode($data[0]['displayname'][0]);
        }
      }
      ldap_close($ldapConnection);
    }

    return $fullname;
  }

  /**
   * @param string $username
   *
   * @return array|null
   */
  public function getGroups($username)
  {
    $groups = null;
    if($ldapConnection = ldap_connect($this->_ldapServer)){
      if(ldap_bind($ldapConnection, $this->_ldapUser, $this->_ldapPass)){
        $ldapTree = "OU=people,DC=ad,DC=unistra,DC=fr";

        $searchLimit = array('memberof');
        $result = ldap_search($ldapConnection, $ldapTree, '(cn='.$username.')', $searchLimit);
        if(count($data = ldap_get_entries($ldapConnection, $result)) > 1){        
          for($i=0; $i<count($data[0]['memberof'])-1; $i++){
            $res = explode(',', $data[0]['memberof'][$i]);
            foreach ($res as $r) {
              if(substr($r,0,3) == 'CN='){
                $groups[] = utf8_encode(substr($r,3));
              }
            }
          }
        }
      }
      ldap_close($ldapConnection);
    }

    return $groups;
  }

  /**
   * @param string $expression
   * @param integer $limit
   *
   * @return array|null
   */
  public function getUsersLike($expression, $limit)
  {
    $returnData = null;
    
    if($ldapConnection = ldap_connect($this->_ldapServer)){
      if(ldap_bind($ldapConnection, $this->_ldapUser, $this->_ldapPass)){
        $ldapTree = "OU=people,DC=ad,DC=unistra,DC=fr";

        $searchLimit = array('cn','displayname');
        error_reporting(E_ERROR | E_PARSE);
        $result = ldap_search($ldapConnection, $ldapTree, '(displayname='.$expression.'*)', $searchLimit, 0, $limit);
        for($i = 0; $i < count($data = ldap_get_entries($ldapConnection, $result)) - 1; $i++){
          $returnData[$i]['username'] = utf8_encode($data[$i]['cn'][0]);
          $returnData[$i]['fullname'] = utf8_encode($data[$i]['displayname'][0]);
        }
      }
      ldap_close($ldapConnection);
    }

    return $returnData;    
  }
}
