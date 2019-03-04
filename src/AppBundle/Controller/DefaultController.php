<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     *
     *
     * Verifying logic if there is a user. Only the user has access to the posts.
     *
     */
    public function indexAction()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('user_feed');
        } else {
            return $this->redirectToRoute('security_login');
        }
    }
}
