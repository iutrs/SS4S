<?php

namespace Ss4s\CoreBundle\Controller\User;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DocumentationController extends Controller
{
    public function indexAction()
    {
        return $this->render('Ss4sCoreBundle:User\Documentation:index.html.twig');
    }
}
