<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Message;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository,ParticipantRepository $participant): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
            'participantRepo'=> $participant,
            'sortieRepo'=> $sortieRepository,
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SortieRepository $sortieRepository): Response
    {

        $user =  $this->getUser();
        $sortie = new Sortie();
        $sortie->setOrganisateur($user);

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);


            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annuler/{id}', name: 'app_sortie_annuler')]
    public function annulerSortie(Int $id, Sortie $sortie, EntityManagerInterface $entityManager, SortieRepository $sortieRepository)
    {
        $user = $this->getUser();
        if($user === $sortie->getOrganisateur()){
            $sortie->setEtat(6);
            //$this->addFlash('success', vous avez annule cette sortie avec success !');
        } else {
            //$this->addFlash('error', 'Vous ne pouvez pas annule cette sortie, vous n'etes pas l'organisateur, cependant vous pouvez vous desinscrire si vous le souhaiter.');
        }
    }

    #[Route('/inscription/{id}', name: 'app_sortie_inscription')]
    public function inscription( Int $id, Sortie $sortie, EntityManagerInterface $entityManager,SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {

            $user = $participantRepository->find($this->getUser());

        $sortie = $sortieRepository->find($id);
            if (count($sortie->getParticipants()) < $sortie->getMbInscriptionMax()) {
                if ($sortie->getEtat()->getId() === 2 || $sortie->getEtat()->getId() === 1) {
                    $sortie->addParticipant($user);
                    $entityManager->flush();
                    //$this->addFlash('success', 'Votre inscription a bien été enregistrée');
                }
                else {

                    //$this->addFlash('error', 'Les inscriptions ne sont pas accessible pour cette sortie. Vous ne pouvez pas vous inscrire');
                }
            }
            else {

                //$this->addFlash('error', 'La sortie est complète. Vous ne pouvez pas vous inscrire');
            }

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);


    }

    #[Route('/desinscription/{id}', name: 'app_sortie_desinscription')]
        public function desincription(Int $id, Sortie $sortie, EntityManagerInterface $entityManager,SortieRepository $sortieRepository, ParticipantRepository $participantRepository)
    {
        $user = $participantRepository->find($this->getUser());
        $sortie = $sortieRepository->find($id);
        if ($sortie->getEtat()->getId() === 1 || $sortie->getEtat()->getId() === 2) {
            $sortie->removeParticipant($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            //$this->addFlash('success', 'Vous n'etes plus sur cette sortie');
        }
        else {
            //$this->addFlash('error', 'Cette sortie n'est plus active');
        }

        return $this->redirectToRoute('app_sortie_show', [
            'id' => $id,
            'user'=> $user,
            'sortie'=> $sortie

        ]);
    }

}
