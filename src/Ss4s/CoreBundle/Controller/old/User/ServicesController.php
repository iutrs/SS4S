<?php

namespace Ss4s\CoreBundle\Controller\User;
 
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
 
class ServicesController extends Controller
{
	/**
	 * @var string $_serviceRepository The services repository name.
	 */
	private $_serviceRepository = 'Ss4sCoreBundle:Service';

	/**
	 * Gets the list of services formatted in a json array.
	 */
	public function getAllJsonAction() {
		$services = $this->_getServices();

		echo json_encode($services);
		exit();
	}

	public function redirectIndexAction() 
	{
		return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
	}

	private function _getServices() {
		// Find all services.
		// $query = $this->getDoctrine()
		// 	->getManager()
		// 	->getRepository($this->_serviceRepository)
		// 	->createQueryBuilder('s')
		// 	->where('s.status !=')
		// 	->findAllOrderBy('status');

		// $securityContext = $this->container
		// 	->get('security.context');


	}

	public function indexAction()
	{
		if ($securityContext->getToken()->getUser() !== 'anon.')
			$userGroups = $securityContext->getToken()->getUser()->getGroups();
	  
		foreach($services as $s) {
			$access = false;
			for($i = 0; $i < count($userGroups) && !$access; $i++) {
				if(!is_null($s->getCollegeGroups())) {
					$collegeGroups = array();
					foreach($s->getCollegeGroups() as $cg) {
						$collegeGroups[] = $cg->getGroupName();
					}
					if(in_array($userGroups[$i], $collegeGroups) || $securityContext->isGranted('ROLE_ADMIN')) {
						$access = true;
					}
				}
			}
			if($access) {
				$s->setServicePath($s->getServicePath().'_index');
				$displayServices[] = $s;
		   }
		}

		return $this->render('Ss4sCoreBundle:User\Services:index.html.twig', array(
			'services' => $displayServices
		));
	}
}