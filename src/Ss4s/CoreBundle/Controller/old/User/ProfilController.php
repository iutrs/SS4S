<?php

namespace Ss4s\CoreBundle\Controller\User;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
 
class ProfilController extends Controller
{
    public function indexAction()
    {
        $groups = $this->get('security.context')->getToken()->getUser()->getGroups();

   	    return $this->render('Ss4sCoreBundle:User\Profil:index.html.twig', array(
            'groups' => $groups
        ));
    }
}