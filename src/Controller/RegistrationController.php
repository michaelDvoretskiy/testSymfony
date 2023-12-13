<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager, UserAuthenticatorInterface $authenticatorManager, 
        LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $authenticatorManager->authenticateUser($user, $authenticator, $request, [new RememberMeBadge()]);            

            // return $this->redirectToRoute('app_lista');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), 'errors' => $this->collectEErrors($form)
        ]);
    }

    private function collectEErrors($form) 
    {
        $res = [];
        $formFields = $form->all();

        foreach ($formFields as $fieldName => $field) {
            $res[$fieldName] = $field->getErrors(true, false);
        }
        return $res;
    }
}
