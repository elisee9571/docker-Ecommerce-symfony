<?php

namespace App\Controller\Admin;

use App\Entity\Order;
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
        $arrOrders = $this->orderRepository->findAll();

        foreach ($arrOrders as $order) {
            $arrDetails = $this->orderDetailsRepository->findBy(['order' => $order->getId()]);

            $this->orderTotalPrice($arrDetails, $order);
        }

        return $this->render('admin/order/index.html.twig', [
            'orders' => $arrOrders
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        $arrDetails = $this->orderDetailsRepository->findBy(['order' => $order->getId()]);

        $this->orderTotalPrice($arrDetails, $order);

        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
            'orderDetails' => $arrDetails
        ]);
    }

    public function orderTotalPrice($array, $order)
    {
        foreach ($array as $d) {
                $arr[] = $d->getPrice() * $d->getQuantity();
        }

        return $order->setTotal(array_sum($arr));
    }
}
