<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    public function __construct(ProductRepository $productRepository, UserRepository $userRepository)
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'index')]
    public function index(SessionInterface $session): Response
    {
        $products = $this->productRepository->findBy([], null, 4, null);

        $cart = $session->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }
        
        $total = 0;
        
        foreach ($cartWithData as $item) {
            if($item['product']->getPriceSold() != null){
                $total += $item['product']->getPriceSold() * $item['quantity'];
            }else{
                $total += $item['product']->getPrice() * $item['quantity'];
            }
        }
        
        return $this->render('cart/index.html.twig', [
            "items" => $cartWithData,
            "total" => $total,
            'products' => $products
        ]);
    }

    #[Route('/{id}/add', name: 'add')]
    public function add($id, SessionInterface $session, Product $product, Request $request): Response
    {
        $quantityItem = $request->get('quantityItem');
        
        $cart = $session->get('cart', []);
        
        if (empty($cart[$id])) {
            $cart[$id] = 0;
        }
        
        $cart[$id] += $quantityItem;

        $session->set('cart', $cart);

        if ($id == $product->getId()) {
            return $this->redirectToRoute('app_product_details', ['slug' => $product->getSlug()]);
        }
    }

    #[Route('/{id}/delete', name: 'remove', methods: ['POST'])]
    public function remove($id, SessionInterface $session)
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/checkout', name: 'checkout',  methods: ['GET', 'POST'])]
    public function checkout(SessionInterface $session, Request $request)
    {
        $user = $this->getUser();
        
        $form = $this->createForm(UserType::class, $user);
        $form->remove('roles');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->add($user, true);

            return $this->redirectToRoute('app_cart_checkout');
        }

        $cart = $session->get('cart', []);
        
        $cartWithData = [];
        
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity,
            ];
        }
        
        $total = 0;

        foreach ($cartWithData as $item) {
            if($item['product']->getPriceSold() != null){
                $total += $item['product']->getPriceSold() * $item['quantity'];
            }else{
                $total += $item['product']->getPrice() * $item['quantity'];
            }
        }
        

        return $this->render('cart/checkout.html.twig', [
            "items" => $cartWithData,
            "total" => $total,
            'form' => $form->createView(),
        ]);
    }
}
