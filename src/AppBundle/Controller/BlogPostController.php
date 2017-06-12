<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\BlogPostType;
use AppBundle\Entity\BlogPost;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class BlogPostController
 * @package AppBundle\Controller
 *
 * @RouteResource("post")
 */
class BlogPostController extends Controller
{


    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:BlogPost')->find($id);
    }

    public function listAction()
    {

        $manager = $this->getDoctrine()->getManager();
        $posts = $manager->getRepository('AppBundle:BlogPost')->findAll();

        return $this->render('BlogPosts/list.html.twig',[
            'posts' => $posts
        ]);

    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(BlogPostType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $blogPost = $form->getData();

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($blogPost);
            $manager->flush();

            return $this->redirectToRoute('edit', [
                'blogPost' => $blogPost->getId()
            ]);
        }

        return $this->render('BlogPosts/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function editAction(Request $request, BlogPost $blogPost)
    {
        $form = $this->createForm(BlogPostType::class, $blogPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            return $this->redirectToRoute('edit', [
                'blogPost' => $blogPost->getId()
            ]);
        }

        return $this->render('BlogPosts/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function deleteAction(Request $request, BlogPost $blogPost)
    {
        if ($blogPost === null)
            return $this->redirectToRoute('list');

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($blogPost);
        $manager->flush();

        return $this->redirectToRoute('list');
    }

}
