<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        UserRepository $userRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/admin', name: 'admin_dashboard_index')]
    public function index(): Response
    {
        $categoryCount = $this->categoryRepository->count([]);
        $productCount = $this->productRepository->count([]);
        $orderCount = $this->orderRepository->count([]);
        $userCount = $this->userRepository->count([]);

        $lastProducts = $this->productRepository->findBy([], ['id' => 'DESC'], 5, null);

        return $this->render('admin/dashboard/index.html.twig', [
            'categoryCount' => $categoryCount,
            'productCount' => $productCount,
            'orderCount' => $orderCount,
            'userCount' => $userCount,
            'lastProducts' => $lastProducts,
        ]);
    }
}
