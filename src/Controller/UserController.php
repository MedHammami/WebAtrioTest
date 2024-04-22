<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;

class UserController extends AbstractController
{
    /**
     * @Route("/user/new", name="user_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $personne = new User();
        $form = $this->createForm(UserType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'âge
            if ($personne->getBirthday()->diff(new \DateTime())->y >= 150) {
                throw $this->createNotFoundException('Seules les personnes de moins de 150 ans peuvent être enregistrées.');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('user_new');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/users", name="users_list")
     */
    public function listUsers(): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $users = $userRepository->findBy([], ['last_name' => 'ASC']); // Récupère toutes les personnes et les trie par nom

        // Calcul de l'âge et récupération des emplois actuels pour chaque personne
        $usersInfo = [];
        foreach ($users as $user) {
            $age = $user->getBirthday()->diff(new \DateTime())->y; // Calcul de l'âge
            $emploisActuels = $user->getJobs();
            $usersInfo[] = [
                'nom' => $user->getLastName(),
                'age' => $age,
                'actual_job' => $emploisActuels,
            ];
        }

        return $this->render('user/list_users.html.twig', [
            'usersInfo' => $usersInfo,
        ]);
    }

    /**
     * @Route("/users/{society}", name="users_by_enterprise")
     */
    public function usersByEnterprise(Request $request, string $society): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $users = $userRepository->findByEnterprise($society);

        return $this->render('user/users_by_enterprise.html.twig', [
            'users' => $users,
            'society' => $society,
        ]);
    }

    /**
     * @Route("/user/{id}/jobs", name="user_jobs_between_dates")
     * @throws \Exception
     */
    public function getJobsBetweenDates(Request $request, $id): Response
    {
        $startDate = new \DateTime($request->query->get('start_date'));
        $endDate   = new \DateTime($request->query->get('end_date'));

        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $jobs = $user->getJobs()->filter(function ($job) use ($startDate, $endDate) {
            $startDateMatches = $job->getDateStart() >= $startDate && $job->getDateStart() <= $endDate;
            $endDateMatches = $job->getDateEnd() && $job->getDateEnd() >= $startDate && $job->getDateEnd() <= $endDate;
            return $startDateMatches || $endDateMatches;
        });

        return $this->json($jobs, 200, [], ['groups' => 'public']);
    }
}
