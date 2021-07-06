<?php

namespace App\Controller;

use App\Entity\Argonaute;
use App\Form\ArgonauteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArgoController extends AbstractController
{
    /**
     * @Route("/", name="argo_home")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // On récupère la liste de tous les argonautes
        $argonautes = $em->getRepository(Argonaute::class)->findAll();
        // On crée un nouvel objet argonaute
        $argonaute = new Argonaute();

        // On crée le formulaire
        $form = $this->createForm(ArgonauteType::class, $argonaute);
        // On traite la requête du formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire est submit et valide
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($argonaute);
            $em->flush();

            // On envoi un message de succes
            $this->addFlash(
                'success',
                'Un nouvel Argonaute a été ajouté à la liste !'
            );

            // On redirige l'utilisateur
            return $this->redirectToRoute('argo_home');;
        }

        return $this->render('argo/index.html.twig', [
            'argonautes'=> $argonautes,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/argonaute/supprimer/{id}", name="argo_delete")
     */
    public function delete(EntityManagerInterface $em, $id): Response
    {

        // On récupère un argonaute via son id
        $argonaute = $em->getRepository(Argonaute::class)->find($id);

        // Si il existe alors on le supprime
        if($argonaute){
           $em->remove($argonaute);
           $em->flush();

           // On envoi un message de succes
           $this->addFlash(
            'success-delete',
            'L\'argonaute a bien été supprimé !'
        );
        }
        // On redirige l'utilisateur
        return $this->redirectToRoute('argo_home');
    }

}
