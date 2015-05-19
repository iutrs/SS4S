<?php

namespace Ss4s\CoreBundle\Controller\User;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
 
class AboutController extends Controller
{
    public function creditsAction()
    {
   	    return $this->render('Ss4sCoreBundle:User\About:credits.html.twig');
    }

    public function legalAction()
    {
    	return $this->render('Ss4sCoreBundle:User\About:legal.html.twig');
    }
}