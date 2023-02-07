<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{

    #[Route('/{slug}', name: 'list')]
    public function details(Categories $category): Response
    {


        //on va chercher la liste de la categories

        $products = $category->getProducts();
        return $this->render(
            'categories/list.html.twig',
            compact('category', 'products')
        );
        //meme chose
        // return $this->render(
        //     'categories/list.html.twig',[
        //         'category'=> $category,
        //         'products'=> $products
        //     ]
        // );


    }
}
