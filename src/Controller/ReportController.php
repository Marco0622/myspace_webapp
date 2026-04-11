<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/report', name: 'app_report_')]
final class ReportController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $objReport = new Report();

        $reportForm = $this->createForm(ReportFormType::class, $objReport);

        $reportForm->handleRequest($request);
        
        if($reportForm->isSubmitted() && $reportForm->isValid()) {

            $objReport->setAuthor($this->getUser());
            $objReport->setSendAt(new DateTimeImmutable('now'));
            
            $entityManager->persist($objReport);
            $entityManager->flush();

            $this->addFlash('success', "Le signalement a été envoyé !");

            return $this->redirectToRoute('app_user_home');
        }

        return $this->render('report/form.html.twig', [
            'reportForm' => $reportForm,
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'delete')]
    public function delete(Request $request ,Report $report, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_report', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $entityManager->remove($report);
        $entityManager->flush();
         $this->addFlash('success', "Le signalement a été supprimé !");

        return $this->redirectToRoute('app_user_home');
    }

    #[Route('/edit/{id<\d+>}', name: 'edit')]
    public function edit(Request $request ,Report $report, EntityManagerInterface $entityManager): Response
    {
        

        $reportForm = $this->createForm(ReportFormType::class, $report);

        $reportForm->handleRequest($request);
        
        if($reportForm->isSubmitted() && $reportForm->isValid()) {

            $report->setSendAt(new DateTimeImmutable('now'));
            
            $entityManager->persist($report);
            $entityManager->flush();

            $this->addFlash('success', "Le signalement a été modifié !");

            return $this->redirectToRoute('app_user_home');
        }

        return $this->render('report/form.html.twig', [
            'reportForm' => $reportForm,
        ]);
    }


    #[Route('/responce/{id<\d+>}', name: 'responce')]
    public function responce(): Response
    {
        return $this->render('report/index.html.twig', [
            
        ]);
    }
}
