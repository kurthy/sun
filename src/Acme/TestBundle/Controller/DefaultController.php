<?php

namespace Acme\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="acm")
     */
    public function indexAction(Request $request)
    {
            $defaultData = array( 'lat' => 'kliknutím na mapu');
	    $form = $this->createFormBuilder($defaultData)
		    ->setAction($this->generateUrl('acm'))
		    ->add('lat' , TextType::class)
                    ->add('lng' , TextType::class)
                    ->add('save' , SubmitType:: class, array('label' => 'Vypočítaj západy a východy Slnka..' ))
                    ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
           // ... perform some action, such as saving the task to the database
		    
		    /* vyskusal som takyto zvlastny kod ci je kliknute
		     $nextAction = $form->get('save')->isClicked()
		     ? 'homepage'
		     : 'acm';
	    return $this->redirectToRoute($nextAction);
            */
	    $aPom[] = $form->getData();
           return $this->redirectToRoute('jedenbod',array('lat'=>$aPom[0]['lat'],'lng'=>$aPom[0]['lng']) );
           }

        return $this->render('AcmeTestBundle:Default:index.html.twig',['id' => 1, 'map_width' => '100%', 'map_height' => 490, 'form' => $form->createView(), 'default_lat' => '49.4', 'default_lng' => '18.149', 'lat_name' => 'lat','lng_name' => 'lng', 'lat' => '48.7', 'lng' => '19.6', 'default_zoom' => '13', 'zoom' => '13']);
    }

        public function setLatLng($latlng)
        {
            $this->setLat($latlng['lat']);
            $this->setLng($latlng['lng']);
            return $this;
        }

        /**
         * @Assert\NotBlank()
         * @OhAssert\LatLng()
         */
        public function getLatLng()
        {
            return array('lat'=>$this->getLat(),'lng'=>$this->getLng());
        }
 
    
}
