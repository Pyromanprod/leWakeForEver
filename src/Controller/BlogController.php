<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CreateArticleFormType;
use App\Form\PostCommentFormType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/blog", name="blog_")
 *
 */
class BlogController extends AbstractController
{

    ///////////////////////////Création de nouvel article
    /**
     * @Route("/nouvelle-publication/", name="create_article")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function create_article(Request $request): Response
    {


        $newArticle = new Article();
        $form = $this->createForm(CreateArticleFormType::class, $newArticle);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            //la fonction marche mais phpstorm est débile
            $newArticle->setUser($this->getUser());
            $newArticle->setPublicationDate(new DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($newArticle);
            $em->flush();

            $this->addFlash('success', 'Arcticle ajouté avec succés');
            return $this->redirectToRoute('home');
        }

        return $this->render('blog/createArticle.html.twig', [
            'form' => $form->createView()
        ]);
    }
    //////////////////////////////LISTE DE TOUS LES ARTICLES

    /**
     * @Route("/liste-des-articles/", name="view_articles")
     */
    public function viewArticles(): Response
    {
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        $listeArtcile = $articleRepo->findBy([], ['id' => 'DESC']);

        return $this->render('blog/viewArticles.html.twig', [
            'listeArticle' => $listeArtcile,
        ]);
    }
    ////////////////////////////////VOIR UN SEUL ARTICLES

    /**
     * @Route("/article/{slug}/", name="view_article")
     */
    public function viewOneArticle(Article $article, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(PostCommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                if ($this->getUser()) {
                    $comment->setPublicationDate(new DateTime());
                    //la fonction marche mais phpstorm est débile
                    $comment->setUser($this->getUser());
                    $comment->setArticle($article);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($comment);
                    $em->flush();

                    $this->addFlash('success', 'Commentaire posté avec succés');

                    return $this->redirectToRoute('blog_view_articles');
                } else {
                    return $this->redirectToRoute('app_login');
                }
            } else {

                $this->addFlash('fail', $form->getErrors(true, true));

            }
        }

        return $this->render('blog/viewOneArticle.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);


    }
}
