<?php

namespace Ss4s\CoreBundle\Controller\Administrator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ss4s\CoreBundle\Form\Administrator\AdministratorType;
use Ss4s\CoreBundle\Entity\Administrator;

class AdministratorsController extends Controller
{
    public function indexAction()
    {
        $administrators = $this->getDoctrine()->getRepository('Ss4sCoreBundle:Administrator')->findAll();

    	$administrator = new Administrator();
    	$form = $this->createForm(new AdministratorType(), $administrator);

    	$request = $this->get('request');
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		if($form->isValid()) {
    			$em = $this->getDoctrine()->getManager();
                $username = $form->getData()->getUsername();
                if($fullname = $this->get('ss4s.ldap_check')->getFullname($username)) {
        			if(!$em->getRepository('Ss4sCoreBundle:Administrator')->findOneByUsername($username)) {
                        $em->persist($administrator);
            			$em->flush();
                        $this->get('session')->getFlashBag()->add(
                            'success',
                            'L\'administrateur '.$fullname.'('.$username.') à bien été ajouté.'
                        );

                        return $this->redirect($this->generateUrl('ss4s_core_administrator_administrators_index'));
        			} else {
                        $this->get('session')->getFlashBag()->add(
                            'error',
                            'L\'utilisateur '.$fullname.'('.$username.') est déjà administrateur.'
                        );
        			}
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'error',
                        'L\'utilisateur '.$username.' n\'a pas été trouvé dans l\'annuaire de l\'Unistra.'
                    );                    
                }
    		}
    	}

        return $this->render('Ss4sCoreBundle:Administrator\Administrators:index.html.twig', array(
        	'form' => $form->createView(),
            'administrators' => $administrators
        ));
    }

    /**
     * @param string $expression
     */
    public function getAjaxUsersLikeAction($expression)
    {
        $users = $this->get('ss4s.ldap_check')->getUsersLike($expression, 8);
        echo json_encode($users);
        exit();
    }

    /**
     * @param interger $id
     * @param string $username
     */
    public function grantSuperAdministratorAction($username)
    {
        $em = $this->getDoctrine()->getManager();

        $administrator = $em->getRepository('Ss4sCoreBundle:Administrator')->findOneBy(array(
            'username' => $username
        ));
                
        if($administrator) {
            $administrator->setIsSuperAdmin(true);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                    'success',
                    'L\'administrateur "'.$username.'" est maintenant super administrateur'
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                    'error',
                    'L\'administrateur "'.$username.'" n\'existait pas'
            );
        }

        return $this->redirect($this->generateUrl('ss4s_core_administrator_administrators_index'));
    }

    /**
     * @param interger $id
     * @param string $username
     */
    public function revokeSuperAdministratorAction($username)
    {
        $em = $this->getDoctrine()->getManager();

        $administrator = $em->getRepository('Ss4sCoreBundle:Administrator')->findOneBy(array(
            'username' => $username
        ));

        if($administrator){
            $userRoles = $this->get('security.context')->getToken()->getUser()->getRoles();

            if($administrator->getIsSuperAdmin() && !in_array('ROLE_FATHER_OF_ALL',$userRoles)){
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Vous ne pouvez pas révoquer les droits de super administrateur de "'.$username.'", vous et lui êtes super administrateurs'
                );
            } else {
                $administrator->setIsSuperAdmin(false);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Le super administrateur "'.$username.'" ne possède à présent plus les droits de super administrateur'
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'L\'administrateur "'.$username.'" n\'existe pas'
            );            
        }

        return $this->redirect($this->generateUrl('ss4s_core_administrator_administrators_index'));
    }

    /**
     * @param interger $id
     * @param string $username
     */
    public function deleteAction($username)
    {
        $em = $this->getDoctrine()->getManager();

        $administrator = $em->getRepository('Ss4sCoreBundle:Administrator')->findOneBy(array(
            'username' => $username,
        ));

        if($administrator) {
            $userRoles = $this->get('security.context')->getToken()->getUser()->getRoles();
            
            if($administrator->getIsSuperAdmin() && !in_array('ROLE_FATHER_OF_ALL', $userRoles)) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'L\'administrateur "'.$username.'" n\'a pas pu être supprimé, vous et lui êtes super administrateurs'
                );
            } else {
                $em->remove($administrator);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'L\'administrateur "'.$username.'" à bien été supprimé'
                );
            }
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'L\'administrateur "'.$username.'" n\'existe pas'
            );
        }


        return $this->redirect($this->generateUrl('ss4s_core_administrator_administrators_index'));
    }
}
