<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // récupérer les entreprises de la BDD
        $entreprises = $entityManager->getRepository(Entreprise::class)->findby([], ["raisonSociale" => "ASC"]);
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);
    }

    #[Route('/entreprise/add', name: 'add_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function add(EntityManagerInterface $entityManager, Entreprise $entreprise = null, Request $request): Response{
        
        if(!$entreprise){
            $entreprise = new Entreprise();
        }

        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $entreprise = $form->getData();
            // prepare
            $entityManager->persist($entreprise);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute("app_entreprise");
        }

        // vue pour afficher le formulaire d'ajout
        return $this->render('entreprise/add.html.twig', [
            "formAddEntreprise" => $form->createView()
        ]);
    }

    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response{
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise
        ]);
    }

}
