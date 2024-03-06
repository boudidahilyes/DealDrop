<?php

namespace App\Controller;

use App\Repository\ProductForRentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends AbstractController
{
    #[Route('/stripe/rent/{price}', name: 'app_stripe_rent')]
    public function index($price): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'price' =>$price
        ]);
    }
    #[Route('/stripe/create-charge/rent/{price}', name: 'app_stripe_charge_rent', methods: ['POST'])]
    public function createCharge(Request $request,$price)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create ([
                "amount" => $price,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "API Paymenet goes well"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_product_for_rent');
    }
    #[Route('/stripe/sale/{price}', name: 'app_stripe_sale')]
    public function index2($price): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            'price' =>$price
        ]);
    }
    #[Route('/stripe/create-charge/sale/{price}', name: 'app_stripe_charge_sale', methods: ['POST'])]
    public function createCharge2(Request $request,$price)
    {
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create ([
                "amount" => $price,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "API Paymenet goes well"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_product_for_rent');
    }
}
