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
namespace Ss4s\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Ss4s\CoreBundle\Controller\Service\PluginController;
use Ss4s\CoreBundle\Entity\Service;
use Ss4s\CoreBundle\Entity\ServiceRepository;

class ServiceAccessListener
{
    /** 
     * @var \Symfony\Component\Security\Core\SecurityContext 
     */
    private $securityContext;

    /** 
     * @var \Symfony\Component\Routing\Router 
     */
    private $router;

    /** 
     * @var \Ss4s\Bundle\Core\ServiceBundle\Entity\ServiceRepository 
     */
    private $serviceRepository;

    /** 
     * @var \Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $serviceContainer;

    /**
     * Constructor
     * 
     * @param SecurityContext $securityContext
     * @param Router $router
     * @param Doctrine $doctrine
     * @param Container $serviceContainer
     */
    public function __construct(SecurityContext $securityContext, Router $router, Doctrine $doctrine, Container $serviceContainer)
    {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->serviceRepository = $doctrine->getRepository('Ss4sCoreBundle:Service');
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        // L'interface PluginController doit impérativement être implémentée dans le controller du plugin.
        // Elle permet de contrôler l'accès au service. 
        if ($controller[0] instanceof PluginController) {
            $request = $event->getRequest();
            $sid = $request->attributes->get('_route_params')['sid'];
            // $_route = $request->attributes->get('_route');

            // // Les routes doivent toutes suivre la même synthaxe. 
            // // ex: Ss4sPluginsExampleBundle_index.
            // $_namespace = explode('_',$_route)[0];

            // On initialise l'accès au service à faux.
            $access = false;

            // On recherche dans la base de données le service dont l'id est donné
            if($service = $this->serviceRepository->findOneById($sid)){

                // On récupère tous les groupes qui ont accès au service.
                $serviceGroups = array();
                foreach($service->getCollegeGroups() as $sg){
                    $serviceGroups[] = $sg->getGroupName();
                }

                // Et si le service a des groupes. 
                if(!is_null($serviceGroups)) {
                    // On les compare a ceux de l'user courant.
                    $userGroups = $this->securityContext->getToken()->getUser()->getGroups();
                    
                    // Si l'utilisateur est un administrateur, il a accès au service.
                    if($this->securityContext->isGranted('ROLE_ADMIN')) 
                        $access = true;

                    // Sinon on regarde si les groupes qui ont accès au service 
                    // présentent des correspondances avec les groupes de l'utilisateur.
                    else {
                        // Si le service n'est pas en maintenance
                        if ($service->getStatus() != 1) {
                            for($i = 0; $i < count($userGroups) && !$access; $i++) {   
                                if(in_array($userGroups[$i], $serviceGroups)) {
                                    $access = true;
                                }
                            }
                        }
                    }
                }
                
                

                // Si l'accès au service est accordé, le service auquel on accède devient le service courant.
                if($access == true)
                    $this->serviceContainer->get('ss4s.current_service')->setCurrentService($service); 
            }
            
            // Si l'accès n'est pas autorisé, on passe à la requête un arguments de non accès. 
            if($access == false) {
                $event->getRequest()->attributes->set('access', 'false');
            
            } 

        // Si le controller auquel on tente d'accéder n'est pas un service,
        // on place le service courant à null. 
        // Le service courant contient les paramètres d'implémentation du service
        // courant.
        } else {
            $this->serviceContainer->get('ss4s.current_service')->setCurrentService(null);
        }

        $event->setController($controller);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequest()->attributes->get('access') == 'false') {
            $this->serviceContainer->get('session')->getFlashBag()->add(
                'error', 
                'Désolé, mais ce service n\'existe pas ou ne vous est pas accessible.'
            );
            
            $url = $this->router->generate('ss4s_core_user_services_index');
            
            $event->setResponse(new RedirectResponse($url));
        }


        // Ajout des connexions/déconnexions aux logs

        // On récupère la dernière page visitée
        $lastPage = $event->getRequest()->headers->get('referer');

        // Si elle est égale à la chaîne vide, alors la dernière page était une page externe : le portail
        if ($lastPage == '') {
            $security = $this->serviceContainer->get('security.context');
            $queryParams = $event->getRequest()->query->get('ticket');

            if($queryParams != '') {
                $token = $security->getToken();
                $fullname = $token->getUser()->getFullname();
                $username = $token->getUser()->getUsername();
                $ss4slogs = $this->serviceContainer->get('monolog.logger.ss4sloginout');
                $ss4slogs->info($fullname.' ('.$username.') s\'est connecté.');
            }   
        }

        // Si le controller qui est demandé est celui de la déconnexion, on log la déconnexion de l'utilisateur
        if ($event->getRequest()->get('_controller') == 'BeSimpleSsoAuthBundle:TrustedSso:logout') {
            $security = $this->serviceContainer->get('security.context');
            if($security->isGranted('ROLE_USER')) {
                $token = $security->getToken();
                $fullname = $token->getUser()->getFullname();
                $username = $token->getUser()->getUsername();
                $ss4slogs = $this->serviceContainer->get('monolog.logger.ss4sloginout');
                $ss4slogs->info($fullname.' ('.$username.') s\'est déconnecté.');
            }               
        }
    }
}