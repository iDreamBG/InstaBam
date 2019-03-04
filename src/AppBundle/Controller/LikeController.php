<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Insta;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends Controller
{
    /**
     * @Route("/instabam/like/{id}", name="insta_like")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     *
     * This is the method which I use to like posts
     */
    public function likeAction(Request $request, $id)
    {
        $currentInsta = $this->getDoctrine()
            ->getRepository(Insta::class)
            ->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $isLikeExist = $currentUser->isExistInstaLike($currentInsta);

        if ($isLikeExist)
        {
            $currentInsta->decrementLikesCounter();
            $currentUser->removeInstaLike($currentInsta);
        }
        else
        {
            $currentInsta->incrementLikesCounter();
            $currentUser->setInstaLike($currentInsta);
            $em->persist($currentUser);
            $em->persist($currentInsta);
        }

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/instabam/listLikes/{instaId}", name="insta_list_likes")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $instaId
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * By this method I take all the likes of a post
     */
    public function listLikesAction(Request $request, $instaId)
    {
        $currentInsta = $this->getDoctrine()
            ->getRepository(Insta::class)
            ->find($instaId);

        $likesUsers = $currentInsta->getUserLikes();

        return $this->render('insta/likes.html.twig', ['likes' => $likesUsers]);
    }
}