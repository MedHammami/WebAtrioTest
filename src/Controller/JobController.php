<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;

class JobController extends AbstractController
{
    /**
     * @Route("/user/{id}/job/new", name="user_job_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function addJob(Request $request): Response
    {
        $job = new Job();
        //$job->setUser($user); // Associe l'emploi à l'utilisateur

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();

            return $this->redirectToRoute('home'); // Remplacez "home" par le nom de votre route de page d'accueil
        }

        return $this->render('job/create_job.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
