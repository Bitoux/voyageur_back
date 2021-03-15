<?php

namespace App\Controller\LocationManagement;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\LocationManagement\Color;

class ColorController extends AbstractFOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/color/list", name="color_list")
     */
    public function index()
    {
        $color = $this->getDoctrine()->getRepository(Color::class)->findByUnused();
        if($color){
            return $color;
        }else{
            return [
                'empty_data' => true
            ];
        }
    }
}