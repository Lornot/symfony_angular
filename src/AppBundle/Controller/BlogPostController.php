<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\BlogPostType;


class BlogPostController extends Controller
{

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

            return $this->redirectToRoute('list');
        }

        return $this->render('BlogPosts/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
