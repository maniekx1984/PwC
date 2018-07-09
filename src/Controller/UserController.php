<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $encoder)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $user = new User();
        $plainPassword = 'Haslohaslo1';
        $encoded = $encoder->encodePassword($user, $plainPassword);

        return $this->render('user/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'pass' => $encoded
        ));
    }
}
