<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Reservation;
use App\Form\Reservation1Type;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use DateTimeImmutable;
use Twilio\Rest\Client;

class JSONController extends AbstractController
{
    #[Route('/j/s/o/n', name: 'app_j_s_o_n')]
    public function index(): Response
    {
        return $this->render('json/index.html.twig', [
            'controller_name' => 'JSONController',
        ]);
    }


    //json

    #[Route('/JSON/getAll', name: 'app_reservation_JSON', methods: ['GET'])]
    public function index_JSON(SerializerInterface $serializer,ReservationRepository $reservationRepository)
    {
        $reservations = $reservationRepository->findAll();
        $data = [];
    foreach ($reservations as $res) {
        $data[] = [
            'id' => $res->getId(),
            'date_debut' => $res->getDateDebut()->format("Y-m-d"),
            'date_fin' => $res->getDateFin()->format("Y-m-d"),
        ];
    }

    $json = $serializer->serialize($data, 'json');

    return new Response($json);
    }

    #[Route('/JSON/delete', name: 'app_reservation_delete_JSON', methods: ['GET'])]
    public function delete_JSON(Request $request, ReservationRepository $reservationRepository): Response
    {
        $id = $request->get("id");
        
        $reservation = $reservationRepository->find($id);
       
            $reservationRepository->remove($reservation,true);

            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize("reservation has been deleted successfully.");
            return new JsonResponse($formatted);
        

       
    }


    #[Route('/JSON/new', name: 'create_reservation', methods: ['GET'])]
    public function createReservationAction(Request $request, ValidatorInterface $validator, NormalizerInterface $Normalizer, ReservationRepository $reservationRepository)
    {
        
        $res = new Reservation();
        $date_d = DateTimeImmutable::createFromFormat("Y-m-d", $request->query->get("date_debut"));
        $date_f = DateTimeImmutable::createFromFormat("Y-m-d", $request->query->get("date_fin"));
        $em = $this->getDoctrine()->getManager();
        $res->setDateDebut($date_d);
        $res->setDateFin($date_f);
        $em->persist($res);
        $em->flush();
         $sid = 'AC6322202e674e9c2ede42f41107988ea1';
         $token = '557d75a1d4337b813c545e9c0ada4bbf';
            $client = new Client($sid, $token);
            $message = $client->messages->create(
                "+21658404108", 
                [
                    'from' => '+16076954758', 
                    'body' => "Vous avez une reservation pour le " .$res->getDateDebut()->format("Y-m-d à H:i")." jusqu'a".$res->getDateFin()->format("Y-m-d à H:i")." . "
                ]
            );
        
        $jsonContent = $Normalizer->normalize($res, 'json');
        return new Response(json_encode($jsonContent));
    }

    #[Route('/JSON/edit', name: 'edit_reservation', methods: ['GET'])]
    public function editReservationAction( Request $request, ValidatorInterface $validator, SerializerInterface $serializer, ReservationRepository $reservationRepository): JsonResponse
    {
        // Find the Velo entity to edit
        $reservation = $reservationRepository->find($request->get('id'));
        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }

        // Decode the JSON data into a PHP array
        //$date_debut = $request->get('date_debut');
       // $date_fin = $request->get('date_fin');
       $date_debut = DateTimeImmutable::createFromFormat("Y-m-d", $request->query->get("date_debut"));
       $date_fin = DateTimeImmutable::createFromFormat("Y-m-d", $request->query->get("date_fin"));

        // Update the Velo entity with the form data
      
        $reservation->setDateDebut($date_debut);
        $reservation->setDateFin($date_fin);
        

        // Validate the entity
        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Save the entity to the database
        $reservationRepository->save($reservation,true);

        // Return a JSON response with the serialized entity data
        $jsonContent = $serializer->serialize($reservation, 'json');
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}
