<?php

/**
 * Author Thomas Beauchataud
 * From 15/03/2021
 */


namespace App\Controller;


use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="index")
 */
class Test extends AbstractController
{

    public function __invoke()
    {
        $this->getDoctrine()->getRepository(Order::class)->findOneBy([]);
        return new Response();
    }

}