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
        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy([
            'active' => true
        ]);
        if($categories){
            return $categories;
        }else{
            throw HttpException(404, 'No data found');
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
            $category->setTag(\mb_strtolower(preg_replace('/\s+/', '-', $category->getName()), 'UTF-8'));
            $em->persist($category);
            $em->flush();

            return $category;
        }

        throw new HttpException(500, 'Erreur survenue');
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/category/{id}", name="category_single")
     */
    public function singleCategory(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        if($category){
            return $category;
        }else{
            throw HttpException(404, 'No data found');
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/api/category/edit", name="category_edit")
     */
    public function editCategory(Request $request)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'id' => $request->request->get('id')
        ]);

        dump($category);
    }
}