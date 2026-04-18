<?php

namespace App\Controller\Front_office;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $page  = max(1, $request->query->getInt('page', 1));
        $limit = 8;
        $offset = ($page - 1) * $limit;

        

        $categories = $categoryRepository->findAllMinContentPaginated($limit, $offset);

        $totalCategories = $categoryRepository->count([]);
        $totalPages  = (int) ceil($totalCategories / $limit);

        return $this->render('front_office/category/index.html.twig', [
            'categories' => $categories,
            'total_pages' => $totalPages,
            'current_page' => $page,
        ]);
    }


    #[Route('/category/{id}', name: 'category_show')]
    public function show(Category $category): Response
    {
        
        return $this->render('front_office/category/show.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category,
        ]);
    }
} 
