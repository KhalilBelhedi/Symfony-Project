<?php

namespace App\Controller;

use App\Entity\Station;
use App\Form\StationType;
use App\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/station')]
class StationController extends AbstractController
{
    #[Route('/', name: 'app_station_index')]
    public function index(StationRepository $stationRepository): Response
    {
        $station=$stationRepository->findAll();
        return $this->render('admin/station/index.html.twig', [
            'stations' => $station,
        ]);
    }

    #[Route('/new', name: 'app_station_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StationRepository $stationRepository): Response
    {
        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stationRepository->save($station, true);

            return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/station/new.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        $reservations = $station->getReservations();
        return $this->render('admin/station/show.html.twig', [
            'station' => $station,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_station_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, StationRepository $stationRepository): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stationRepository->save($station, true);

            return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/station/edit.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, StationRepository $stationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->request->get('_token'))) {
            $stationRepository->remove($station, true);
        }

        return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/searchstationajax', name:'ajaxstation' ,  methods: ['POST'])]
    public function searchajax(Request $request ,StationRepository $repository)
    {
        $repository = $this->getDoctrine()->getRepository(Station::class);
        $requestString=$request->get('searchValue');
        $station = $repository->findByLoc($requestString);
        
        
        return $this->render('admin/station/ajax.html.twig', [
            'stations'=>$station,
        ]);
    }
}
