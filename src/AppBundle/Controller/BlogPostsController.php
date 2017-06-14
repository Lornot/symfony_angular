<?php

namespace AppBundle\Controller;


use AppBundle\Entity\BlogPost;
use AppBundle\Repository\BlogPostRepository;
use AppBundle\Form\BlogPostType;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * Class BlogPostsController
 * @package AppBundle\Controller
 *
 * @RouteResource("post")
 */
class BlogPostsController extends FOSRestController implements ClassResourceInterface
{

    /**
     * Gets an individual Blog Post
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\BlogPost",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction(int $id)
    {



        $blogPost = $this->getBlogPostRepository()->createFindOneByIdQuery($id)->getSingleResult();

        if ($blogPost === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        return $blogPost;
    }

    /**
     * Gets a collection of BlogPosts
     *
     * @return array
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\BlogPost",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getBlogPostRepository()->createFindAllQuery()->getResult();
    }

    /**
     * Gets a collection of BlogPosts
     *
     * @return array
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\BlogPostType",
     *     output="AppBundle\Entity\BlogPost",
     *     statusCodes={
     *         201 = "Returned when a new blog post has been successfully created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(BlogPostType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $blogPost BlogPost
         */
        $blogPost = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        $routeOptions = [
            'id' => $blogPost->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get', $routeOptions, Response::HTTP_CREATED);
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


    private function getBlogPostRepository()
    {
        return $this->get('crv.doctrine_entity_repository.blog_post');
    }

}