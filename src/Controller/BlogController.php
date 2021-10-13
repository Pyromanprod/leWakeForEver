<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CreateArticleFormType;
use App\Form\PostCommentFormType;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\This;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        if ($form->isSubmitted() && $form->isValid()) {

            //la fonction marche mais phpstorm est débile
            $newArticle->setUser($this->getUser());
            $newArticle->setPublicationDate(new DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($newArticle);
            $em->flush();

            $this->addFlash('success', 'Arcticle ajouté avec succés');
            return $this->redirectToRoute('blog_view_article', ['slug' => $newArticle->getSlug()]);
        }

        return $this->render('blog/createArticle.html.twig', [
            'form' => $form->createView()
        ]);
    }
    //////////////////////////////LISTE DE TOUS LES ARTICLES

    /**
     * @Route("/liste-des-articles/", name="view_articles")
     */
    public function viewArticles(Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a ORDER BY a.publicationDate DESC');
        //récupération des Articles
        $listArticle = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );

        return $this->render('blog/viewArticles.html.twig', [
            'listeArticle' => $listArticle,
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

    /**
     * @Route("/search", name="search_article")
     */
    public function blog_search(Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page', 1);

        if ($requestedPage < 1) {
            throw new NotFoundHttpException();
        }
        $requete = $request->query->get('search');
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery("SELECT a FROM App\Entity\Article a WHERE a.title LIKE :search OR a.content LIKE :search ORDER BY a.publicationDate DESC")
            ->setParameter('search', '%' . $requete . '%');
        //récupération des Articles
        $listArticle = $paginator->paginate(
            $query,
            $requestedPage,
            10
        );

        return $this->render('blog/search_article.html.twig', [
            'listeArticle' => $listArticle,
        ]);

    }

    /**
     * @Route("/publication/suppression/{id}", name="delete_article")
     */
    public function delete(Article $article, Request $request): Response
    {

        if (!$this->isCsrfTokenValid('blog_delete_article_'.$article->getId(),$request->query->get('csrf_token'))){
            $this->addFlash('fail', 'Error de suppression on ce la refait ?');
        }else{

        $em = $this->getDoctrine()->getManager();

        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimer sans problème');
        }

        return $this->redirectToRoute('blog_view_articles');
    }

    /**
     * @Route("/modification/{id}", name="article_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function articleEdit(Article $article, Request $request): Response
    {

        $form = $this->createForm(CreateArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Article modifié');
            return $this->redirectToRoute('blog_view_article',[
                'slug'=>$article->getSlug()
            ]);
        }

        return $this->render('blog/update_article.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
