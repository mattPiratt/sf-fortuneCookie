<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(
        CategoryRepository $categoryRepository,
        Request $request,
    ): Response {
        $searchQuery = $request->get('q');
        if ($searchQuery) {
            $categories = $categoryRepository->findBySearch($searchQuery);
        } else {
            $categories = $categoryRepository->findAllOrdered();
        }

        return $this->render('fortune/homepage.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->getCategoryWithFortunes($id);

        if (null === $category) {
            throw new NotFoundHttpException('this category does not exist');
        }

        return $this->render('fortune/showCategory.html.twig', [
            'category' => $category
        ]);
    }
}
