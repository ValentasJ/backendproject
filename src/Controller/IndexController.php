<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contacts;
use App\Entity\Items;

class IndexController extends AbstractController
{
    #[Route('/', name: 'base')]
    public function homePage()
    {
        return $this->render('bodymyservices.html.twig');
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

    #[Route('/my-services', name: 'myservices')]
    public function myServicesPage()
    {
        return $this->render('bodymyservices.html.twig');
    }

    #[Route('/contacts', name: 'contacts')]
    function contactPage(ManagerRegistry $doctrine)
    {
        $request = Request::createFromGlobals();
        $clientName = $request->request->get('client-name');
        $clientEmail = $request->request->get('client-email');
        $message = $request->request->get('client-message');



        if ($request->isMethod('POST')) {
            $error = null;
            if (empty(trim($clientName)) || empty(trim($clientEmail)) || empty(trim($message))) {
                $error = 'Client name, email or message was not set';
            } else if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email is invalid';
            }
            if ($error) {
                return $this->render('bodycontacts.html.twig', [
                    'error' => $error,
                    'formValues' => [
                        'clientName' => trim($clientName),
                        'clientEmail' => trim($clientEmail),
                        'clientMessage' => trim($message),
                    ]
                ]);
            }

            $contact = new Contacts();

            $contact->setEmail($clientEmail);
            $contact->setName($clientName);
            $contact->setMessage($message);

            $manager = $doctrine->getManager();

            $manager->persist($contact);

            $manager->flush();

            $this->addFlash('success', true);

            return $this->redirectToRoute('contacts');
        }

        return $this->render('bodycontacts.html.twig');
    }

    #[Route('/admin', name: 'admin')]
    public function admin(ManagerRegistry $doctrine)
    {
        $contactRepository = $doctrine->getManager()->getRepository(Contacts::class);

        $contacts = $contactRepository->findAll();

        return $this->render('admin.html.twig', [
            'contacts' => $contacts
        ]);
    }

    #[Route('/admin/contact/{id}', name: 'view_contact')]
    public function viewContact(ManagerRegistry $doctrine, $id)
    {

        $contactRepository = $doctrine->getManager()->getRepository(Contacts::class);

        $contact = $contactRepository->findOneBy(['id' => $id]);

        return $this->render('single-contact.html.twig', [
            'contact' => $contact
        ]);
    }

    #[Route('/my-shop', name: 'myshop')]
    public function showItemList(ManagerRegistry $doctrine)
    {
        $itemList = new Items();

        $contactRepository = $doctrine->getManager()->getRepository(Items::class);

        $itemList = $contactRepository->findAll();

        $itemsArray = [];

        foreach ($itemList as $item) {
            $itemsArray[] = [
                'id' => $item->getId(),
                'photoString' => base64_encode(stream_get_contents($item->getPhoto())),
                'title' => $item->getTitle(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
            ];
        }
        return $this->render('myshop.html.twig', ['items' => $itemsArray]);
    }

    #[Route('/save-new-item ', name: 'upload_your_new_items')]
    public function saveNewItem(ManagerRegistry $doctrine)
    {
        $request = Request::createFromGlobals();
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $price = $request->request->get('price');
        $quantity = $request->request->get('quantity');

        if ($request->isMethod('POST')) {
            $error = null;
            if (empty(trim($title)) || empty(trim($description)) || empty(trim($price)) || empty(trim($quantity))) {
                $error = 'Title, description or price was not set';
            }
            if ($error) {
                return $this->render('admin.html.twig', [
                    'error' => $error,
                    'formValues' => [
                        'title' => trim($title),
                        'description' => trim($description),
                        'price' => trim($price),
                        'quantity' => trim($quantity),
                    ]
                ]);
            }
        }
        $newItem = new Items();

        $newItem->setTitle($title);
        $newItem->setDescription($description);
        $newItem->setPrice($price);
        $newItem->setQuantity($quantity);
        // $newItem->setPhoto('photo');

        $manager = $doctrine->getManager();

        $manager->persist($newItem);

        $manager->flush();

        return $this->redirectToRoute('admin');
    }
}