<?php

namespace Ss4s\CoreBundle\Controller\Administrator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class LogsController extends Controller
{
    public function indexAction()
    {
        $data_array = array();
        $file = $this->get('kernel')->getLogDir().'/'.$this->get('kernel')->getEnvironment().'.ss4s.log';
        $fileSystem = new Filesystem();
        if($fileSystem->exists($file)) {
            $data = file_get_contents($file);
            $data_array = explode('²', $data);
            $data_array = array_reverse($data_array);

            $nowAndOne = new \DateTime('NOW');
            $nowAndOne->add(date_interval_create_from_date_string('1 hour'));
            $searchForm = $this->createFormBuilder()
                ->add('begin', 'datetime', array(
                    'data' => new \DateTime('NOW')
                ))
                ->add('end', 'datetime', array(
                    'data' => $nowAndOne
                ))
            ;
            $searchForm = $searchForm->getForm();

            if ($this->get('request')->getMethod() == 'POST') {
                $searchForm->bind($this->get('request'));
                $data = $searchForm->getData();
                
                $beginDate = $data['begin'];
                $endDate = $data['end'];

                $new_data_array = array();

                foreach ($data_array as $log) {
                    $date = new \DateTime(substr($log, 0, 19));
                    if($date >= $beginDate && $date <= $endDate) {
                        $new_data_array[] = $log;
                    }
                }
                echo json_encode($new_data_array);
                exit();
            }

            return $this->render('Ss4sCoreBundle:Administrator\Logs:index.html.twig', array(
                'logs' => $data_array,
                'search_form' => $searchForm->createView()
            ));
        }
        
        return $this->render('Ss4sCoreBundle:Administrator\Logs:index.html.twig');
    }
}
