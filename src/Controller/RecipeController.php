<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * Render the list of recipes
     *
     * @param RecipeRepository $recipeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recipe', name: 'app_recipe', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findAll(), 
            $request->query->getInt('page', 1), 
            10 
        );
        
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * Render the form to add a new recipe and post it
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recipe/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();

        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a bien été ajouté !'
            );

            return $this->redirectToRoute('app_recipe');
        }


        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Render the form to edit a recipe and post it
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('recipe/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a bien été modifié !'
            );

            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a recipe
     *
     * @param EntityManagerInterface $manager
     * @param Recipe $recipe
     * @return Response
     */
    #[Route('recipe/delete/{id}', name:'app_recipe_delete', methods: ['GET'])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a bien été supprimé !'
        );

        return $this->redirectToRoute('app_recipe');
    }
}
