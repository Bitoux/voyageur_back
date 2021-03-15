<?php

namespace App\Controller\LocationManagement;

use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Form\LocationManagement\CategoryType;
use App\Entity\LocationManagement\Category;

class CategoryController extends AbstractFOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/category/list", name="category_list")
     */
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        if($categories){
            return $categories;
        }else{
            return [
                'empty_data' => true
            ];
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/api/category/create", name="category_create")
     */
    public function createCategory(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->submit($request->request->all());

        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $category->setTag(\strtolower(preg_replace('/\s+/', '-', $category->getName())));
            $category->setIcon('ez');
            $em->persist($category);
            $em->flush();

            return $category;
        }

        throw new HttpException(500, 'Erreur survenue');
    }
}