<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Repository\ProductsRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductsRepository $productsRepository): Response

    {
        $produits = $productsRepository->findAll();
        return $this->render('admin/products/index.html.twig', compact('produits'));
    }


    // creation des routes pour niveau admin le role_product_admin peut editer mais pas ajouter voir voter dans src/security

    #[Route('/ajout', name: 'add')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //on cré un nouveau prod
        $product = new Products();
        //on crée le form
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //on traite la requete du form
        $productForm->handleRequest($request);

        //on verifi si le form est soumi ET valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            //on recupere les images
            $images = $productForm->get('images')->getData();
            foreach ($images as $image) {
                //on def le doss de destination
                $folder = 'products';

                //on appelle le service d ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            //on genere le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            //on arrondit le prix
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            //on stocke
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            //on redirige
            return $this->redirectToRoute('admin_products_index');
        }

        // return $this->render('admin/products/add.html.twig', [
        //     'productForm' => $productForm->createView()
        // ]); ou 

        return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }




    #[Route('/edition/{id}', name: 'edit')]
    public function edit(
        Products $product,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        PictureService $pictureService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $prix = $product->getPrice() / 100;
        $product->setPrice($prix);
        //$this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $productForm = $this->createForm(ProductsFormType::class, $product);
        $productForm->handleRequest($request);


        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $images = $productForm->get('images')->getData();
            foreach ($images as $image) {
                $folder = 'products';

                $fichier = $pictureService->add($image, $folder, 300, 300);
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'La fiche produit à été modifié avec succès');

            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product' => $product
        ]);
    }



    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }



    #[Route('/suppression/image{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(
        Images $image,
        Request $request,
        EntityManagerInterface $em,
        PictureService $pictureService
    ): JsonResponse {
        //on recupere le contenu de la requete

        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            //le token csrf est valid,on recupere le nom de l image
            $nom = $image->getName();
            if ($pictureService->delete($nom, 'products', 300, 300)) {
                //on supprime l img de la bdd
                $em->remove($image);
                $em->flush();
                return new JsonResponse(['success' => true], 200);
            }
            //la suppression a echoué
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }
        return new JsonResponse(['error' => 'Token invalide', 400]);
    }
}
