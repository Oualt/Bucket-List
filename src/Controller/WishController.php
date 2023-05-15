<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishController extends AbstractController
{
    #[Route('/wish/list', name: 'wish_list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy([], ['dateCreated' => 'DESC']);

        return $this->render('wish/list.html.twig', [
            "wishes" => $wishes
        ]);
    }

    #[Route('/wish/details/{id}', name: 'wish_details')]
    public function details(WishRepository $wishRepository, $id): Response
    {
        $wishDetail = $wishRepository->find($id);

        if (!$wishDetail) {
            throw $this->createNotFoundException(
                'No wish found for id ' . $id
            );
        }

        return $this->render('wish/details.html.twig', [
            "wishDetail" => $wishDetail
        ]);
    }

    #[Route('/wish/create', name: 'wish_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $wish = new Wish();

        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted()) {

            $wish->setIsPublished(TRUE);
            $wish->setDateCreated(new \DateTime());

            $entityManager->persist($wish); //insert
            $entityManager->flush(); // sauver

            $this->addFlash('success', '✔ Idea successfully added!');

            return $this->redirectToRoute('wish_details', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'wishForm' => $wishForm->createView()
        ]);
    }

    #[Route('/wish/remove/{id}', name: 'wish_remove')]
    public function remove(EntityManagerInterface $entityManager, $id): Response
    {
        $wish = $entityManager->getRepository(Wish::class)->find($id);

        if (!$wish) {
            throw $this->createNotFoundException(
                'No wish found for id ' . $id
            );
        }

        $entityManager->remove($wish);
        $entityManager->flush();

        $this->addFlash('success', '🗑✔ Idea successfully removed!');

        return $this->redirectToRoute('wish_list');
    }



    /* -----------------------------------
        SAVE
        ----------------------------------
    #[Route('/wish/create', name: 'wish_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {

        $wish = new Wish();

        $wish->setTitle("Don't feed the mogwai ");
        $wish->setDescription("Don't feed the mogwai after midnight.");
        $wish->setAuthor("Joe Dante");
        $wish->setIsPublished(TRUE);
        $wish->setDateCreated(new \DateTime());

        dump($wish);

       $entityManager->persist($wish); //insert
       $entityManager->flush(); // sauver

        //$entityManager = $this->getDoctrine()->getManager(); sauvegarder dans BDD

        return $this->render('wish/create.html.twig', [
            
        ]);
    }

    */

    /*#[Route('/wish/modify/{id}', name: 'wish_modify')]
    public function modify(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $wish = $entityManager->getRepository(Wish::class)->find($id);

        if (!$wish) {
            throw $this->createNotFoundException(
                'No wish found for id '.$id
            );
        }

        $wish->setTitle($request->request->get('title'));
        $wish->setDescription($request->request->get('description'));
        $wish->setAuthor($request->request->get('author'));
        $wish->setIsPublished($request->request->get('is_published'));
        $wish->setDateModified(new \DateTime());

        $entityManager->flush();

        return $this->redirectToRoute('wish_details', ['id' => $id]);
    }
    */
}
