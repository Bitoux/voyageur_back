<?php

namespace App\Controller\LocationManagement;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\LocationManagement\Location;
use App\Utils\UploadedBase64File;
use App\Utils\Base64FileExtractor;

class LocationController extends AbstractFOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/location/list", name="location_list")
     */
    public function index()
    {

        $locations = $this->getDoctrine()->getRepository(Location::class)->findAll();
        if($locations){
            return $locations;
        }else{
            return [
                'empty_data' => true
            ];
        }
        
    }

    /**
     * @Rest\View()
     * @Rest\Put("/api/location/create", name="location_create")
     */
    public function createLocation(Request $request, Base64FileExtractor $base64Extractor)
    {
        $location = new Location();

        $form = $this->createForm(Location::class, $location);

        $form->submit($request->request->all());

        if($form->isValid()){
            $base64Image = $request->request->get('file');
            $base64Image = $base64Extractor->extractBase64String($base64Image);
            $imageFile = new UploadedBase64File($base64Image, 'youhou');
            $location->setImageFile($imageFile);
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            return $location;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/location/{id}", name="location_single")
     */
    public function singleLocation(Request $request, $id)
    {
        $location = $this->getDoctrine()->getRepository(Location::class)->findOneBy([
            'id' => $id
        ]);
        
        return $location;

    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/location/nearest/{longitude}/{latitude}", name="location_nearest")
     */
    public function nearestLocation(Request $request, $longitude, $latitude)
    {
        $locations = $this->getDoctrine()->getRepository(Location::class)->findClosest($latitude, $longitude);

        return $locations;
    }
}
