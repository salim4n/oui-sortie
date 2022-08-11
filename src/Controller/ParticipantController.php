<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ParticipantType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    #[Route('/', name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/edit', name: 'app_participant_editme', methods: ['GET', 'POST'])]
    public function editme(Request $request, UserPasswordHasherInterface $userPasswordHasher ,ParticipantRepository $participantRepository, CampusRepository $campusRepository): Response
    {

         $participant =  $this->getUser();

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($participant->getRoles() != 'ROLE_ADMIN'){
                $user = $participantRepository->find($participant);
                $participant->setActif($user->isActif());
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $participantRepository->add($participant, true);

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
            'campuss' => $campusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, ParticipantRepository $participantRepository, CampusRepository $campusRepository  ): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $admin = $request->request->get('is_admin', 'false');

            if ($admin == 'true'){
                $participant->setRoles((array)"ROLE_ADMIN");
            }
            else{
                $participant->setRoles((array)"ROLE_USER");
            }
            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );
            $dataCampus = $request->request->get('campus', 'false');;
            $campus = $campusRepository->find($dataCampus);
            $participant->setEstRattacheA($campus);
            $participantRepository->add($participant, true);
            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/new.html.twig', [
            'participant' => $participant,
            'campuss' => $campusRepository->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participant_show', methods: ['GET'])]
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_participant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participant $participant, ParticipantRepository $participantRepository, CampusRepository $campusRepository): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);
        dump($participant);
        if ($form->isSubmitted() && $form->isValid()) {
            $participantRepository->add($participant, true);

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
            'campuss' => $campusRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_participant_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
