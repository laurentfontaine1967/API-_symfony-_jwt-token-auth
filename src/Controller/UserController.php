<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $manager;
    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }
    //creer utilisateur
    #[Route('/userCreate', name: 'app_create', methods: "POST")]
    public function userCreate(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];

        //verifier si email existe
        $email_exist = $this->user->findOneByEmail($email);
        if ($email_exist) {
            return new JsonResponse(
                [
                    'status' => false,
                    'message' => "email existe deja, veuillez le changer"

                ]
            );
        } else {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(sha1($password));
            $this->manager->persist($user);
            $this->manager->flush();

            return new JsonResponse(
                [
                    'status' => true,
                    'message' => "utilisateur crée avec succes"

                ]
            );
        }


        // return $this->render('user/index.html.twig', [
        //     'controller_name' => 'UserController',
        // ]);
    }


    //récupérer la liste des utilisateurs
    #[Route('/getAllUsers', name: 'get_allusers', methods: "GET")]
    public function getAllUsers(): Response
    {

        $users = $this->user->findAll();

        return $this->json($users, 200);
    }
}
