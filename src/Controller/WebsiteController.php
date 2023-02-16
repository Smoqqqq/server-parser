<?php

namespace App\Controller;

use App\Entity\Website;
use App\Form\WebsiteType;
use App\Repository\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebsiteController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/website/create", name="app_website_create")
     */
    public function createWebsite(Request $request, ManagerRegistry $doctrine, WebsiteRepository $websiteRepository): Response
    {
        $website = new Website();

        $user = $this->getUser();

        $groups = $user->getUserGroups();

        $servers = [];

        foreach ($groups as $group) {
            $groupServers = $group->getServers();
            foreach ($groupServers as $server) {
                array_push($servers, $server);
            }
        }

        $createdServers = $user->getServers();

        foreach ($createdServers as $server) {
            array_push($servers, $server);
        }

        $form = $this->createForm(WebsiteType::class, $website, ["servers" => $servers]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($website->getServer() == null) {
                $this->addFlash("danger", "Veuillez ajouter un server.");
                return $this->redirectToRoute("app_website_create");
            }

            $url = $website->getUrl();

            if (count($websiteRepository->findBy(["url" => $url])) > 0) {
                $this->addFlash("danger", "Le site <b>$url</b> existe déja !");
                return $this->render("website/create.html.twig", [
                    "form" => $form->createView()
                ]);
            }

            $em = $doctrine->getManager();
            $em->persist($website);
            $em->flush();

            $this->addFlash("success", "Site ajouté au serveur !");
            return $this->redirectToRoute("app_server_read", ["id" => $website->getServer()->getId()]);
        }

        return $this->render('website/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/websites", name="app_websites")
     */
    public function websites()
    {
        $groups = $this->getUser()->getUserGroups();

        $sites = [];

        foreach ($groups as $group) {
            $groupServers = $group->getServers();
            foreach ($groupServers as $server) {
                foreach ($server->getWebsites() as $site) {
                    array_push($sites, $site);
                }
            }
        }

        $createdServers = $this->getUser()->getServers();

        foreach ($createdServers as $server) {
            $websites = $server->getWebsites();
            foreach ($websites as $site) {
                if (!in_array($site, $sites)) {
                    array_push($sites, $site);
                }
            }
        }

        return $this->render("website/list.html.twig", [
            "websites" => $sites
        ]);
    }
}
