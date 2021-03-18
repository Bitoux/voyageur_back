<?php

namespace App\Controller\UserManagement;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;

use App\Entity\UserManagement\User;
use App\Form\UserManagement\UserType;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

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
     * @Rest\Post("/api/user/edit", name="user_edit")
     */
    public function userEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => urldecode($request->request->get('email'))]);

        if($user){
            $em = $this->getDoctrine()->getManager();
            if($request->request->get('email') !== $request->request->get('new_email')){
                $findEmail = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => urldecode($request->request->get('new_email'))]);
                if(!$findEmail){
                    $user->setEmail($request->request->get('new_email'));
                    $refreshTokens = $this->getDoctrine()->getRepository(RefreshToken::class)->findBy(['username' => urldecode($request->request->get('email'))]);
                    foreach($refreshTokens as $refreshToken){
                        $refreshToken->setUsername($request->request->get('new_email'));
                        $em->flush();
                    }
                }else{
                    throw new HttpException(409, 'Email duppliqué');
                }
            }
            if($request->request->get('password')){
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                ));
            }
            $user->setFirstName($request->request->get('first_name'));
            $user->setLastName($request->request->get('last_name'));
            $user->setAddress($request->request->get('address'));
            $user->setCity($request->request->get('city'));
            
            $em->flush();

            return new JsonResponse([
                'status' => 200,
                'email' => $user->getEmail(),
                'message' => 'Votre profil a bien été modifié'
            ]);

        }else{
            throw new HttpException(404, 'Aucun email n\'a été trouvé');
        }
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

    /**
     * @Rest\View()
     * @Rest\Post("/api/forgoten_password", name="forgoten_password")
     */
    public function forgotenPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerInterface $mailer)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => urldecode($request->request->get('email'))]);

        if($user){
            $newPassword = \random_bytes(10);
            dump($newPassword);
            $newPassword = bin2hex($newPassword);
            dump($newPassword);
            $email = (new TemplatedEmail())
                ->from('ymiloux@gmail.com')
                ->to(new Address($user->getEmail()))
                ->subject('Voyager - Mot de passe oublié')
                ->htmlTemplate('emails/forgoten_password.html.twig')
                ->context([
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'new_password' => $newPassword
                ]);
            $mailer->send($email);
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $newPassword
            ));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse([
                'status' => 200,
                'message' => 'Un message vient de vous être envoyé avec votre nouveau mot de passe'
            ]);
        }else{
            throw new HttpException(400, 'Aucun email n\'a été trouvé');
        }
    }
}
