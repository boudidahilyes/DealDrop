<?php

namespace App\Controller;

use App\Repository\ProductForRentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Eckinox\PdfBundle\Pdf\FormatFactory;
use Eckinox\PdfBundle\Pdf\PdfGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\OrderController as OrderController;
class StripeController extends AbstractController
{
    #[Route('/stripe/create-charge/{type}/{price}', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request,$price,$type,OrderController $orderCon)
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
        if($type == 'ProductForRent')
        return $this->redirectToRoute('app_product_for_rent');
        else
        return $this->redirectToRoute('app_product_for_sale');
    }
}
