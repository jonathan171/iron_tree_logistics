<?php

namespace App\Controller;

use App\Entity\UserDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DriverController extends AbstractController
{   
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    
    #[Route('/driver', name: 'app_driver')]
    public function index(): Response
    {
        $user = $this->getUser();

        $documents = $this->manager->getRepository(UserDocument::class)->findOneBy(['user' => $user->getId()]);

        return $this->render('driver/index.html.twig', [
            'documents' => $documents
        ]);
    }
}
