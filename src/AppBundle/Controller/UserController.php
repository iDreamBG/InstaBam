<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Insta;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\InstaType;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * Register
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $userNameForm = $form->getData()->getUsername();

            $userForm = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['username' => $userNameForm]);

            if (null !== $userForm) {
                $this->addFlash('info', "That username " . $userNameForm . " already taken!");
                return $this->render('user/register.html.twig');
            }

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $role = $this
                ->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($role);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig');
    }


    /**
     * @Route("/feed", name="user_feed")
     *
     * This method shows the posts of people who user following
     */
    public function feedAction() {
        $userId = $this->getUser()->getId();
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        $follewedPeople = $this
            ->getDoctrine()
            ->getRepository(Insta::class)
            ->getAllInstaByFollowedUsers($userId);

        return $this->render('user/feed.html.twig',
            [
                'user' => $user,
                'People' => $follewedPeople
            ]);
    }

    /**
     * @Route("/discover", name="user_discover")
     */
    public function discoverAction()
    {
        $currentUser = $this->getUser();
        $currentUserId = $currentUser->getId();

        $users = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->getAllUsersExceptCurrentLogged($currentUserId);

        return $this->render('user/discover.html.twig', ['users' => $users]);
    }


    /**
     * This is function for return a profile of one user
     *
     *
     * @Route("/profile/{userId}", name="user_foreign_profile", requirements={"userId": "\d+"})
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     *
     *
     * Open user profile
     */
    public function foreignProfileAction(Request $request, $userId)
    {
        /** @var User $currentLoggedUser */
        $currentLoggedUser = $this->getUser();
        $currentLoggedUserId = $currentLoggedUser->getId();

        if ($userId == $currentLoggedUserId) {
            return $this->redirectToRoute('user_profile');
        }

        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        if ($user === null) {
            return $this->redirectToRoute('user_discover');
        }

        $insta = $this
            ->getDoctrine()
            ->getRepository(Insta::class)
            ->getAllInstaByUserId($userId);

        if ($insta === null) {
            return $this->redirectToRoute('user_discover');
        }

        $instaCount = $user->getInstaCounter();
        $followingCount = $user->getFollowingCounter();
        $followersCount = $user->getFollowersCounter();
        $isFollowed = $currentLoggedUser->isUserFollowed($user);

        return $this->render('user/foreign_profile.html.twig',
            [
                'user' => $user,
                'insta' => $insta,
                'instaCount' => $instaCount,
                'followingCount' => $followingCount,
                'followersCount' => $followersCount,
                'isFollowed' => $isFollowed
            ]
        );
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profileAction()
    {
        $userId = $this->getUser()->getId();
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        $insta = $this
            ->getDoctrine()
            ->getRepository(Insta::class)
            ->getAllInstaByUserId($userId);

        $insta = new Insta();
        $form = $this->createForm(InstaType::class, $insta);

        $instaCount = $user->getInstaCounter();
        $followingCount = $user->getFollowingCounter();
        $followersCount = $user->getFollowersCounter();

        return $this->render('user/profile.html.twig',
            [
                'user' => $user,
                'insta' => $insta,
                'form' => $form->createView(),
                'instaCount' => $instaCount,
                'followingCount' => $followingCount,
                'followersCount' => $followersCount
            ]
        );
    }

}
