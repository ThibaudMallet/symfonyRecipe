<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Faker\Guesser\Name;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * this method allow us to modify informations profile
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('/user/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {

        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword())) {       
                $user = $form->getData();
    
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées.'
                );

                return $this->redirectToRoute('app_recipe');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }

        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * this method allow us to edit user password
     *
     * @param User $choosenUser
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    #[Route('user/edit-password/{id}', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(User $choosenUser, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword'])) {  

                $choosenUser->setUpdatedAt(new \DateTimeImmutable());
                $choosenUser->setPlainPassword($form->getData()['newPassword']);
    
                $manager->persist($choosenUser);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié.'
                );

                return $this->redirectToRoute('app_recipe');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
