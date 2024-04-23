<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\Reservation1Type;
use App\Repository\ReservationRepository;
use App\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;
use Symfony\Component\Routing\Annotation\Route;
use DateTimeImmutable;


class ReservationsController extends AbstractController
{
    #[Route('admin/reservation/', name: 'app_reservations_index', methods: ['GET', 'POST'])]
    public function index(StationRepository $StatRepo,ReservationRepository $reservationRepository): Response
    {   
        
        $station=$StatRepo->findAll();
        $resss=$reservationRepository->findAll();
        $res=$reservationRepository->findByValider();
       foreach ($res as $event)
       {
           $ress[]=[
                'title'=>"user",
              
               'start'=>$event->getDateDebut()->format("Y-m-d H:i:s"),
               'end'=>$event->getDateFin()->format("Y-m-d H:i:s"),
               'backgroundColor'=> '#0ec51',
               'borderColor'=> 'green',
               'textColor' => 'black',
              
           ];
       }
    
       $data = json_encode($ress);

        return $this->render('admin/reservation/reservations.html.twig', [
            'reservations' => $resss,
            'data'=>$data,
            'res'=>$res,
            'station'=>$station,
            'rrr'=>$resss

        ]);
    }

    #[Route('/new', name: 'app_reservations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(Reservation1Type::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         //   $sid = 'AC6322202e674e9c2ede42f41107988ea1';
           // $token = '15e6276ab8399af2689300b26419d795';
            //$client = new Client($sid, $token);
            //$message = $client->messages->create(
              //  "+21658404108", 
               // [
                 //   'from' => '+16076954758', 
                  //  'body' => "Vous avez une reservation pour le " .$reservation->getDateDebut()->format("Y-m-d à H:i")." jusqu'a".$reservation->getDateFin()->format("Y-m-d à H:i")." . "
                //]
           // );
            $reservation->setEtat(0);
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('app_reservations_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservations/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_reservations_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {    




        return $this->render('reservations/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createForm(Reservation1Type::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('app_reservations_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservations/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('reservations/{id}', name: 'app_reservations_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('reservations/valider/{id}', name: 'app_reservations_valider')]
    public function valider($id,Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {

        $res=$reservationRepository->find($id);
        $res->setEtat(1);
        $em=$this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('admin/reservation/trier/{id}', name: 'app_reservations_trie', methods: ['GET', 'POST'])]
    public function AfficheParStation(StationRepository $StatRepo,$id,ReservationRepository $reservationRepository): Response
    {
        $stat=$StatRepo->find($id);
        $resss=$reservationRepository->findByStation($stat);
        if ($resss == null){
            return  $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
        }
        $res=$reservationRepository->findByValideretStat($stat);
        $station=$StatRepo->findAll();
        $re=$reservationRepository->findAll();

       foreach ($res as $event)
       {
           $t[]=[
                'title'=>"user",
              
               'start'=>$event->getDateDebut()->format("Y-m-d H:i:s"),
               'end'=>$event->getDateFin()->format("Y-m-d H:i:s"),
               'backgroundColor'=> '#0ec51',
               'borderColor'=> 'green',
               'textColor' => 'black'
           ];
       }
       $data = json_encode($t);

        return $this->render('admin/reservation/reservations.html.twig', [
            'reservations' => $resss,
           'data'=>$data,
            'res'=>$res,
            'station'=>$station,
            'rrr'=>$re


        ]);
    }

    
}
