<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class SortieController extends AbstractController
{
    #[Route('/', name: 'app_accueil', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository,ParticipantRepository $participant): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
            'participantRepo'=> $participant,
            'sortieRepo'=> $sortieRepository,
        ]);
    }

    #[Route('/sortie/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository): Response
    {

        $user =  $this->getUser();
        $sortie = new Sortie();
        $sortie->setOrganisateur($user);
        $sortie->addParticipant($user);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/sortie/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie, Participant $participants  ): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'participants' => $participants

        ]);
    }

    #[Route('/sortie/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);


            return $this->redirectToRoute('app_accueil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/sortie/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_accueil', [], Response::HTTP_SEE_OTHER);
    }

    /*#[Route('/sortie/annuler/{id}', name: 'app_sortie_annuler')]
    public function annulerSortie(Int $id, Sortie $sortie, EntityManagerInterface $entityManager,
                                  SortieRepository $sortieRepository, Etat $etat)
    {

        if($sortie->getOrganisateur() === $this->getUser()){
            $sortie->setEtat($etat->setId(6));
            $entityManager->persist($sortie);
            $entityManager->flush();
           // $this->addFlash('success','votre sortie a bien ete annulee');
        } else {
            // $this->addFlash('error','un probleme est survenue lors de l'annulation de cette sortie');
        }

        return $this->redirectToRoute('app_accueil', [
            'etat'=> $sortie->getEtat()
        ], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/sortie/inscription/{id}', name: 'app_sortie_inscription')]
    public function inscription( Int $id, Sortie $sortie, EntityManagerInterface $entityManager,SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {

            $user = $participantRepository->find($this->getUser());

        $sortie = $sortieRepository->find($id);
            if (count($sortie->getParticipants()) < $sortie->getMbInscriptionMax()) {
                if ($sortie->getEtat()->getId() === 2 || $sortie->getEtat()->getId() === 1) {
                    $sortie->addParticipant($user);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre inscription a bien ??t?? enregistr??e');
                }
                else {

                    $this->addFlash('error', 'Les inscriptions ne sont pas accessible pour cette sortie. Vous ne pouvez pas vous inscrire');
                }
            }
            else {

                $this->addFlash('error', 'La sortie est compl??te. Vous ne pouvez pas vous inscrire');
            }

            return $this->redirectToRoute('app_accueil', [], Response::HTTP_SEE_OTHER);


    }

    #[Route('/sortie/desinscription/{id}', name: 'app_sortie_desinscription')]
        public function desincription(Int $id, Sortie $sortie, EntityManagerInterface $entityManager,SortieRepository $sortieRepository, ParticipantRepository $participantRepository)
    {
        $user = $participantRepository->find($this->getUser());
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getId() === 1 || $sortie->getEtat()->getId() === 2) {
            $sortie->removeParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success','vous ne participez plus a cette sortie');
        }
        else {
            $this->addFlash('error','cette sortie est annulee');
        }

        return $this->redirectToRoute('app_sortie_show', [
            'id' => $id,
            'user'=> $user,
            'sortie'=> $sortie

        ]);
    }

}
