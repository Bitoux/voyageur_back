<?php

namespace App\Controller\UserManagement;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;

use App\Entity\UserManagement\User;
use App\Form\UserManagement\UserType;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/users", name="user_list")
     */
    public function list()
    {

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $view = View::create($users);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/user/{email}", name="user_get")
     */
    public function user(Request $request, $email)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => urldecode($email)]);

        return $user;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/api/register", name="user_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if($form->isValid()){
            $plainPassord = $user->getPlainPassword();
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $plainPassord
            ));
            $user->eraseCredentials();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $user;
        }else{
            return $form;
        }
    }
}
