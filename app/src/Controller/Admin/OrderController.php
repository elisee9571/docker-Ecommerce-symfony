<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/order', name: 'admin_order_')]
class OrderController extends AbstractController
{    
    public function __construct(OrderRepository $orderRepository, OrderDetailsRepository $orderDetailsRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailsRepository = $orderDetailsRepository;
    }

    #[Route(name: 'index')]
    public function index(): Response
    {
        $orders = $this->orderRepository->findAll();

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        $arrDetails = $this->orderDetailsRepository->findBy(['order' => $order->getId()]);
        foreach ($arrDetails as $order) {
            $arr[] = $order->getPrice();
        }
        $total = array_sum($arr);

        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
            'orderDetails' => $arrDetails,
            'total' => $total
        ]);
    }
}
