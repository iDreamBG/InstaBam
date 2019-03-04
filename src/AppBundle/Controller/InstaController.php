<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Insta;
use AppBundle\Entity\User;
use AppBundle\Form\InstaType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InstaController extends Controller
{
    /**
     * @Route("/instabam/create", name="insta_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Create Post.
     */
    public function createAction(Request $request)
    {
        $insta = new Insta();
        $form = $this->createForm(InstaType::class, $insta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            $insta->setAuthor($currentUser);
            $currentUser->incrementInstaCounter();

            $em = $this->getDoctrine()->getManager();
            $em->persist($insta);
            $em->flush();

            $this->addFlash('edit', "Successful post.");

            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/instabam/edit/{id}", name="insta_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Edit Post.
     */
    public function editAction(Request $request, $id)
    {
        $insta = $this
            ->getDoctrine()
            ->getRepository(Insta::class)
            ->find($id);

        if ($insta === null) {
            return $this->redirectToRoute('homepage');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($insta) && !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(InstaType::class, $insta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $insta->setDateAdded(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->merge($insta);
            $em->flush();

            $this->addFlash('edit', "Successful edit.");

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('insta/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $insta->getAuthor(),
                'insta' => $insta
            ]
        );
    }

    /**
     * @Route("/instabam/delete/{id}", name="insta_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Delete Post
     */
    public function deleteAction(Request $request, $id)
    {
        $insta = $this
            ->getDoctrine()
            ->getRepository(Insta::class)
            ->find($id);

        if ($insta === null) {
            return $this->redirectToRoute('homepage');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$currentUser->isAuthor($insta) && !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $currentUser = $this->getUser();
        $insta->setAuthor($currentUser);
        $currentUser->removeInstaLike($insta);
        $insta->removeUserLike($currentUser);
        $currentUser->decrementInstaCounter();

        $em = $this->getDoctrine()->getManager();
        $em->remove($insta);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
