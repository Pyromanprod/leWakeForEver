<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\CreateArticleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        $listeArtcile = $articleRepo->findBy([],['id'=>'DESC'],5);

        return $this->render('main/home.html.twig',[
            'listeArticle'=>$listeArtcile,
        ]);
    }

    /**
     * @Route("/mon-profil/", name="profil")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function profil(): Response
    {


        return $this->render('main/profil.html.twig');
    }


}
