<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializerInterface;
use App\Exception\MyException;


class ProductController extends AbstractController
{
   
    /**
     * @Route("/api/products/{id}", name="products_show",methods={"GET"})
     */
    public function showProductId(SerializerInterface $serialize, $id)
    {
        try{

            $articles = $this->getDoctrine()->getRepository('App:Product')->find($id);
            if(!$articles){
                $response= new Response();
                $message='Aucun article ne correspond a votre recherche.';
                $response->setContent(json_encode(['error'=>$message]));
                $response->setStatusCode(404); 
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
     * @Route("/api/products", name="products_create", methods={"POST"})
     */
    public function createProduct(Request $request, SerializerInterface $serialize )
    {
        try{

        $data = $request->getContent();
        $article = $serialize->deserialize($data,'App\Entity\Product', 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        $response = new Response();
        $response->setContent(json_encode([
            'Success' => "Creation du produit effectuer !",
        ]));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(201);
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

    /**
     * @Route("/api/products", name="products_list", methods={"GET"})
     */
    public function listAllProduct(SerializerInterface $serialize)
    {
        try{
            $articles = $this->getDoctrine()->getRepository('App:Product')->findAll();
            if(!$articles){
                $response= new Response();
                $message='Aucun article ne correspond a votre recherche.';
                $response->setContent(json_encode(['error'=>$message]));
                $response->setStatusCode(404); 
                return $response; 
                
            }
            $data = $serialize->serialize($articles, 'json');
            $response = new Response($data);
            $response->headers->set('Content-Type', 'app/lication/json');
    
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
     * @Route("/api/product/{id}", methods={"DELETE"},name="delProduct")
     */
    function delProduct($id):Response
    {

        try{
            $em = $this->getDoctrine()->getManager();

            $post = $em->getRepository('App:Product')->find($id);   
            if(!$post){
                $response = new Response();
                $response->setContent(json_encode([
                    'error' => "L'article referencer n'est pas disponible!",
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(404);
                return $response;
            } 
            $em->remove($post);
            $em->flush();

            $response = new Response();
            $response->setContent(json_encode([
                'Success' => "Suppression du produit effectuer !",
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
     * @Route("/api/product/{id}", methods={"PUT"},name="ModifProduct")
     */
    function ModifieProduct(Request $request,$id,SerializerInterface $serialize)
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $parametersAsArray = [];
            if ($data = $request->getContent()) {
                $parametersAsArray = json_decode($data, true);
            }
    
            $post = $em->getRepository('App:Product')->find($id);
     
              if($parametersAsArray['description'] == null || $parametersAsArray['photo'] == null || $parametersAsArray['name'] == null  || $parametersAsArray['price'] == null   ){
                $response = new Response();
                $response->setContent(json_encode([
                    'error' => "Requete refuser: un parametre est null !",
                ]));
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode(400);
                return $response;
    
            }
            
                $post->setName($parametersAsArray['name']);
                $post->setDescription($parametersAsArray['description']);
                $post->setPrice($parametersAsArray['price']);
                $post->setPhoto($parametersAsArray['photo']);
                $em->persist($post);
                $em->flush();
                $response = new Response();
                $response->setContent(json_encode([
                    'Success' => "La modification a ete effectue !",
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
        
            
    
}