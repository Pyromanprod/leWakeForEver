<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\EditPhotoType;
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
        $listeArtcile = $articleRepo->findBy([], ['publicationDate' => 'DESC'], $this->getParameter('app.nb_article_home'));

        return $this->render('main/home.html.twig', [
            'listeArticle' => $listeArtcile,
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

    /**
     * @Route("/edit-photo/", name="edit_photo_profil")
     * @Security("is_granted('ROLE_USER')")
     */
    public function editPhoto(Request $request): Response
    {

        //SUPPRESSION DE LA PHOTO
        $form = $this->createForm(EditPhotoType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $photoUser = $this->getUser()->getPhoto();
            if (
                $photoUser != null
                &&
                file_exists($this->getParameter('app.user.photo.directory') . $photoUser)

            ) {
                unlink($this->getParameter('app.user.photo.directory') . $photoUser);
            }
            $file = $form->get('photo')->getData();

            //Création du nom de photo aléatoir
            do {

                $newFileName = md5(random_bytes(100)) . '.' . $file->guessExtension();

            } while (file_exists($this->getParameter('app.user.photo.directory') . $newFileName));


            // on récup l'utilisateur co et on change sa photo
            $this->getUser()->setPhoto($newFileName);


            //mise a jour bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            //envoie de photo
            $file->move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName
            );
            $this->addFlash('success', 'Nouvelle photo trop stylé !');


            return $this->redirectToRoute('profil');

        }
        return $this->render('main/edit-photo.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
