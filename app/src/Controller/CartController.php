<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route(name: 'index')]
    public function index(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['product']->getPrice() * $couple['quantity'];
        }

        return $this->render('cart/index.html.twig', [
            "items" => $panierWithData,
            "total" => $total
        ]);
    }

    #[Route('/{id}/add', name: 'add')]
    public function add($id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }

        $panier[$id]++;

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/{id}', name: 'remove', methods: ['POST'])]
    public function remove($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/checkout', name: 'checkout', methods: ['GET', 'POST'])]
    public function checkout(SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach ($panierWithData as $couple) {
            $total += $couple['product']->getPrice() * $couple['quantity'];
        }

        return $this->render('cart/checkout.html.twig', [
            "items" => $panierWithData,
            "total" => $total
        ]);
    }
}
