<?php

namespace App\Controller\LocationManagement;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\LocationManagement\Location;

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
     * @Rest\Post("/api/location/create", name="location_create")
     */
    public function createLocation(Request $request)
    {
        $location = new Location();
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
}
