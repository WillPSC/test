<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;
use DateTime;


class OrderController extends AbstractController
{


    /**
     * @Route("/api/order/{orderId}", name="order_specifique", methods={"GET"})
     */
    public function getSpecificOrder(SerializerInterface $serialize,$orderId)
    {
        try{
            $test= $this->getDoctrine()->getRepository('App:Order')->find($orderId);
            if(!$test){
                $response = new Response();
                $response->setContent(json_encode([
                    'INFO'=>"Aucune commande avec l'id " .$orderId,
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(400);
                return $response;
            }else{

                $data = $serialize->serialize($test, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }catch(\Exception $e){
            $response = new Response();
            $response->setContent(json_encode([
                'Error'=>"Probleme interne du serveur",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
            return $response;
        }


    }
   
    /**
     * @Route("/api/order", name="order_show",methods={"GET"})
     */
    public function showOrder(SerializerInterface $serialize)
    {
        $user=$this->getDoctrine()->getRepository('App:User')->find(2);
        if($user->getOrderId()!=false) {
            $orders = $user->getOrderId();
            foreach ($orders as $items) {
                $array[] = $this->getDoctrine()->getRepository('App:Order')->find($items);
            }
            $data = $serialize->serialize($array, 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
            //$response->setStatusCode(402,"Page not found");
            return $response;

        }else{
            $empty=new \ArrayObject();
            return $this->json($empty);
        }
    
    }


    /**
     * @Route("/api/cart/validate", name="article_list", methods={"GET"})
     */
    public function validate(SerializerInterface $serialize): Response
    {

        $user= $this->getDoctrine()->getRepository('App:User')->find(2);

        $tabProd=$user->getShopping()->getValues();
    

        $order= new Order();
        $order->setCreationDate(new DateTime());
        $totalPrice=0;
        foreach ($tabProd as $item) {
                $order->addProduct($item);
                $totalPrice= $totalPrice+$item->getPrice();
        }

        $order->setPrice($totalPrice);
        $em=$this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        if($user->getOrderId()!=false){
            $test= $user->getOrderId();
            $test[]=$order->getId();
        }else{
            $test[]=$order->getId();
        }
        $user->setOrderId($test);
        $em1=$this->getDoctrine()->getManager();
        $em1->persist($user);
        $em1->flush();
    return $this->json(["data"=>"validé"]);
    }

}
