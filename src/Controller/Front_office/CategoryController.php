<?php

namespace App\Controller\Front_office;

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
        $page = $request->query->getInt('page', 1);
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $queryBuilder = $categoryRepository->createQueryBuilder('c')
            ->select('c.id, c.label, c.image, COUNT(e.id) as eventCount')
            ->leftJoin('c.events', 'e')
            ->groupBy('c.id');

        $categories = (clone $queryBuilder)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalCategories = count($categoryRepository->findAll());
        $totalPages = ceil($totalCategories / $limit);

        return $this->render('front_office/category/index.html.twig', [
            'categories' => $categories,
            'total_pages' => $totalPages,
            'current_page' => $page,
        ]);
    }


    #[Route('/category/{id}', name: 'category_show')]
    public function show(CategoryRepository $categoryRepository, int $id): Response
    {
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        return $this->render('front_office/category/show.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category,
        ]);
    }
} 
