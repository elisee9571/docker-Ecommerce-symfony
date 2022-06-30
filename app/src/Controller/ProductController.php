<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product', name: 'app_product_')]
class ProductController extends AbstractController
{
    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }
    
    #[Route(name: 'index')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $this->productRepository->findAll(),
        ]);
    }
    
    #[Route('/details/{slug}', name: 'details')]
    public function details(Product $product): Response
    {
        $products = $this->productRepository->findByCategory($product->getCategory(), $product);

        return $this->render('product/detail.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}
