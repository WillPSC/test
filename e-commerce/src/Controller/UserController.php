<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;


class UserController extends AbstractController
{
   
    /**
     * @Route("/api/user", name="user_show", methods={"GET"})
     */
    public function showUser(SerializerInterface $serialize)
    {
        try{
            $articles = $this->getDoctrine()->getRepository('App:User')->find(1);
            if($articles == null){
                $response = new Response();
                $response->setContent(json_encode([
                   'error' => "Vous n'etes pas authentifier !",
               ]));
               $response->headers->set('Content-Type', 'application/json');
               $response->setStatusCode(406);
               return $response;
            }
            $data = $serialize->serialize($articles, 'json');
    
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }catch(\Exception $e){
            $response = new Response();
            $response->setContent(json_encode([
                'error' => "Probleme interne Server !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
            return $response;
            }
        
    }

    /**
     * @Route("/api/user", name="users _update", methods={"PUT"})
     */
    public function UpdateUser(Request $request )
    {
        try{

            $em = $this->getDoctrine()->getManager();
            $parametersAsArray = [];
            if ($data = $request->getContent()) {
                $parametersAsArray = json_decode($data, true);
            }

            $post = $em->getRepository('App:User')->find(1);

            if($post == null){
                $response = new Response();
             $response->setContent(json_encode([
                'error' => "Vous n'etes pas authentifier !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(406);
            return $response;
            }

            if($parametersAsArray['firstname'] == null || $parametersAsArray['lastname'] == null  || $parametersAsArray['email'] == null|| $parametersAsArray['password'] == null){
                $response = new Response();
                $response->setContent(json_encode([
                    'error' => "Requete refuser: un parametre est null !",
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(400);
                return $response;
    
            }

            $post->setFirstname($parametersAsArray['firstname']);
            $post->setLastname($parametersAsArray['lastname']);
            $post->setEmail($parametersAsArray['email']);
            $post->setPassword($parametersAsArray['password'] );
            $em->persist($post);
            $em->flush();
            $response = new Response();
            $response->setContent(json_encode([
                'Success' => "Votre compte a ete modifie !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(200);
            return $response;

        }catch(\Exception $e){
            $response = new Response();
            $response->setContent(json_encode([
                'error' => "Probleme interne Server !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
            return $response;
            }
      
        
    }
      /**
     * @Route("/api/register", name="user_update", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serialize )
    {
        try{
            $data = $request->getContent();
            $article = $serialize->deserialize($data,'App\Entity\User', 'json');
    
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $response = new Response();
                $response->setContent(json_encode([
                    'Success' => "Votre compte a ete creer !",
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(201);
                return $response;

        }catch(\Exception $e){
            $response = new Response();
            $response->setContent(json_encode([
                'error' => "Probleme interne Server !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
            return $response;
            }
       
        
    }
    /**
     * @Route("/api/cart", name="cart_display", methods={"GET"})*
     * pas d'affichage a cause de jms dans l'entity qui cahche le shopping 
     */
    public function getPanier(SerializerInterface $serialize)
    {
        $product= $this->getDoctrine()->getRepository('App:User')->find(2);
        if(!$product){
            $response = new Response();
            $response->setContent(json_encode([
                'info' => "Votre panier est vide!",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(404);
            return $response;

        }

        $test=$product->getShopping()->getValues();
        
        $data = $serialize->serialize($test, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    /**
     * @Route("/api/cart/{id}", name="add_panier", methods={"POST"})
     */
    public function AjoutPanier($id)
    {
        try{
            $articles = $this->getDoctrine()->getRepository('App:Product')->find($id);
            if(!$articles){
                $response = new Response();
                $response->setContent(json_encode([
                    'error' => "Cet article n'est pas disponible !",
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(404);
                return $response;
            }
            $user=$this->getDoctrine()->getRepository('App:User')->find(2);
            $user->addShopping($articles);
           
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $response = new Response();
            $response->setContent(json_encode([
                'Success' => "Cet article a ete ajoute !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(200);
            return $response;
    
        }catch(\Exception $e){
            $response = new Response();
            $response->setContent(json_encode([
                'error' => "Probleme interne Server !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
            return $response;
            }
       
        
        
    }
     /**
     * @Route("/api/cart/{id}", name="remove_panier", methods={"DELETE"})
     */
    public function RemovePanier($id)
    {
        try{
            $articles = $this->getDoctrine()->getRepository('App:Product')->find($id);
            if(!$articles){
                $response = new Response();
                $response->setContent(json_encode([
                    'error' => "Cet article n'est pas present dans votre panier !",
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(404);
                return $response;

            }
            $user=$this->getDoctrine()->getRepository('App:User')->find(2);
            $user->removeShopping($articles);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $response = new Response();
            $response->setContent(json_encode([
                'Success' => "Cet article a ete supprime !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(200);
            return $response;
        }
        catch(\Exception $e){
            $response = new Response();
            $response->setContent(json_encode([
                'error' => "Probleme interne Server !",
            ]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(500);
            return $response;
            }
        
        
    }
}