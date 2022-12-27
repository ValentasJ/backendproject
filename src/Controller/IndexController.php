<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    #[Route('/', name: 'base')]
    public function homePage()
    {
        return $this->render('bodyeyelashes.html.twig');
    }

    #[Route('/eyelashes', name: 'eyelashes')]
    public function eyelashesPage()
    {
        return $this->render('bodyeyelashes.html.twig');
    }

    #[Route('/about-me', name: 'aboutme')]
    public function aboutMePage()
    {
        return $this->render('bodyaboutme.html.twig');
    }

    #[Route('/contacts', name: 'contacts')]
    public function contactsPage()
    {
        return $this->render('bodycontacts.html.twig');
    }

    #[Route('/my-services', name: 'myservices')]
    public function myServicesPage()
    {
        return $this->render('bodymyservices.html.twig');
    }

}