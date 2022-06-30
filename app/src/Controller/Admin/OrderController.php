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

        foreach ($arrOrders as $o) {
            $arrDetails = $this->orderDetailsRepository->findBy(['order' => $o->getId()]);

            $o->setTotal(array_sum($this->orderTotalPrice($arrDetails)));
        }

        return $this->render('admin/order/index.html.twig', [
            'orders' => $arrOrders
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        $arrDetails = $this->orderDetailsRepository->findBy(['order' => $order->getId()]);

        $order->setTotal(array_sum($this->orderTotalPrice($arrDetails)));

        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
            'orderDetails' => $arrDetails
        ]);
    }

    public function orderTotalPrice($array)
    {
        foreach ($array as $d) {
            if ($d->getProduct()->getPriceSold() != null) {
                $arr[] = $d->getProduct()->getPriceSold() * $d->getQuantity();
            } else {
                $arr[] = $d->getProduct()->getPrice() * $d->getQuantity();
            }
        }

        return $arr;
    }
}
