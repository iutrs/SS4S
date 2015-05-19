<?php

namespace Ss4s\CoreBundle\Controller\Administrator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Doctrine\Common\Collections\ArrayCollection;
use Ss4s\CoreBundle\Entity\Service;
use Ss4s\CoreBundle\Entity\CollegeGroup;
use Ss4s\CoreBundle\Form\Administrator\ServiceType;
use Ss4s\CoreBundle\Form\Administrator\ServiceParametersType;

class ServicesController extends Controller
{
	public function enableAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$service = $em->getRepository('Ss4sCoreBundle:Service')->find($id);

		if (!$service)
			throw $this->createNotFoundException('Aucun service ne portait l\'id '.$id);

		$service->setStatus(0);

		$em->flush();

		return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
	}

	public function disableAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$service = $em->getRepository('Ss4sCoreBundle:Service')->find($id);

		if (!$service) 
			throw $this->createNotFoundException('Aucun service ne portait l\'id '.$id);

		$service->setStatus(1);

		$em->flush();

		return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
	}

	public function editAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$service = $em->getRepository('Ss4sCoreBundle:Service')->find($id);

		if (!$service) {
			throw $this->createNotFoundException('Aucun service ne portait l\'id '.$id);
		}

		$collegeGroups = array();
		foreach ($service->getCollegeGroups() as $cg){
			$collegeGroups[] = $cg;
		}

		$form = $this->createForm(new ServiceType(), $service);

		$request = $this->get('request');

		if($request->getMethod() == 'POST') {
			$form->bind($this->get('request'));
			if($form->isValid()) {
				foreach ($service->getCollegeGroups() as $cg) {
					foreach ($collegeGroups as $key => $toDel) {
						if ($toDel->getId() === $cg->getId()) {
							unset($collegeGroups[$key]);
						}
					}
				}
				foreach ($collegeGroups as $cg) {
					$em->persist($cg);
				}

				$em->persist($service);
				$em->flush();
				$this->get('session')->getFlashBag()->add(
					'success',
					'Le service "'.$form->getData()->getName().'" a bien été édité.'
				);
				return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
			} else {
				$this->get('session')->getFlashBag()->add(
					'error',
					'Le service "'.$form->getData()->getName().'" n\'a pas pu être édité.'
				);
			}
		}

		return $this->render('Ss4sCoreBundle:Administrator\Services:edit.html.twig', array(
			'form' => $form->createView(),
			'serviceName' => $service->getName()
		));     
	}

	public function editParametersAction($id)
	{
		$request = $this->get('request');

		$em = $this
			->getDoctrine()
			->getManager();

		$service = $em
			->getRepository('Ss4sCoreBundle:Service')
			->find($id);

		$kernel = $this->get('kernel');
		$path = $kernel->locateResource('@'.$service->getServicePath().'/Resources/config/args.yml');
		$yaml = new Parser();
		$args = $yaml->parse(file_get_contents($path));

		$defaultData = $args[$service->getId()];
		if ($defaultData != null) {
			$formu = $this->createFormBuilder($defaultData);

			foreach ($defaultData as $key => $value) {
				$formtype = gettype($value);
				if ($formtype == 'string') {
					$formtype = 'text';
				}
				if ($key == 'pass') {
					$formu->add($key, 'repeated', array(
						'type' => 'text',
						'invalid_message' => 'Les mots de passe doivent correspondre',
						'options' => array('required' => true),
						'first_options'  => array('label' => 'Mot de passe'),
						'second_options' => array('label' => 'Mot de passe (validation)'),
					));
				} else {
					$formu->add($key, $formtype);
				}
			}
			$form = $formu->getForm();

			if($request->getMethod() == 'POST') {
				$form->bind($this->get('request'));
				if ($form->isValid()) {
					$data = $form->getData();
					$array = array($service->getId() => $data);
					$dumper = new Dumper();
					$parameters = $dumper->dump($array, 2);
					file_put_contents($path, $parameters, FILE_APPEND | LOCK_EX);

					return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
				} else {
					$this->get('session')->getFlashBag()->add(
						'error',
						'Le service "'.$service->getName().'" n\'a pas pu être édité.'
					);
				}
			} 

			return $this->render('Ss4sCoreBundle:Administrator\Services:edit_parameters.html.twig', array(
				'form' => $form->createView(),
				'serviceName' => $service->getName()
			)); 
		} else {
			return $this->render('Ss4sCoreBundle:Administrator\Services:edit_parameters.html.twig', array(
				'serviceName' => $service->getName()
			));
		}
	}



	public function addAction()
	{
		$reqStatus = 0;

		// Formulaire d'ajout de service
		$service = new Service();
		$form = $this->createForm(new ServiceType(), $service);

		$request = $this->get('request');
		if($request->getMethod() == 'POST') {

			$form->bind($request);
			if($form->isValid()) {

				$kernel = $this->get('kernel');
				$yaml = new Parser();
				$routes = $kernel->locateResource('@'.$service->getServicePath().'/Resources/config/routing.yml');
				$routes = $yaml->parse(file_get_contents($routes));
				$service->setServiceRoute(key($routes));
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($service);
				$em->flush();


				$path = $kernel->locateResource('@'.$service->getServicePath().'/Resources/config/args.yml');
				$args = $yaml->parse(file_get_contents($path));
				$default = $args['default'];

				$array = array($service->getId() => $default);
				$dumper = new Dumper();
				$data = $dumper->dump($array, 2);
				file_put_contents($path, $data, FILE_APPEND | LOCK_EX);

				$this->get('session')->getFlashBag()->add(
					'success',
					'Le service "'.$form->getData()->getName().'" à bien été ajouté.'
				);
				return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
			} else {
				$this->get('session')->getFlashBag()->add(
					'error',
					'Une erreur s\'est produite, le service n\' pas pu être ajouté'
				);
			}
		}


		return $this->render('Ss4sCoreBundle:Administrator\Services:add.html.twig', array(
			'form' => $form->createView()
		));  
	}

	public function deleteAction($id)
	{
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();

		$service = $em->getRepository('Ss4sCoreBundle:Service')->find($id); 

		if($request->getMethod() == 'POST'){             
			if (!$service) {
				throw $this->createNotFoundException('Aucun service ne portait l\'id '.$id);
			}

			try {
				$kernel = $this->get('kernel');
				$path = $kernel->locateResource('@'.$service->getServicePath().'/Resources/config/args.yml');
				$yaml = new Parser();
				$args = $yaml->parse(file_get_contents($path));
				unset($args[$service->getId()]);
				$dumper = new Dumper();
				$data = $dumper->dump($args, 2);
				file_put_contents($path, $data);

				$serviceName = $service->getName();
				$em->remove($service); 
				$em->flush();

				$this->get('session')->getFlashBag()->add(
					'success',
					'Le service "'.$serviceName.'" à bien été supprimé.'
				);
			} catch (Exception $e) {
				$this->get('session')->getFlashBag()->add(
					'error',
					'Une erreur est survenue et le service "'.$serviceName.'" n\'a pas pu être supprimé'
				);
			}
		}
		return $this->redirect($this->generateUrl('ss4s_core_user_services_index'));
	}
}
