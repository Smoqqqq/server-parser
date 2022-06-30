<?php

namespace App\Controller;

use Throwable;
use App\Entity\Server;
use App\Form\ServerType;
use App\Service\ServerParser;
use App\Repository\ServerRepository;
use App\Repository\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServersController extends AbstractController
{
    /**
     * @Route("/server/create", name="app_server_create")
     */
    public function createServer(Request $request, ManagerRegistry $doctrine, WebsiteRepository $websiteRepository, ServerRepository $serverRepository): Response
    {

        $server = new Server();

        $form = $this->createForm(ServerType::class, $server);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $created = $this->getUser()->getServers();

            $exists = false;

            foreach ($created as $src) {
                if ($src->getHost() === $server->getHost() && $src->getUsername() === $server->getUsername()) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                $this->addFlash("warning", "Un serveur avec le même Hôte et le même identifiant existe déja.");
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            // Parses the server
            $parser = new ServerParser($server, $doctrine, $websiteRepository);
            $state = $parser->parse();
            if ($state) {
                $this->addFlash("warning", $state);
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            $em = $doctrine->getManager();

            $server->setCreatedBy($this->getUser());

            $em->persist($server);
            $em->flush();

            $this->addFlash("success", "Server ajouté !");
            return $this->redirectToRoute("app_server_read", ["id" => $server->getId()]);
        }

        return $this->render('servers/create.html.twig', [
            'controller_name' => 'ServersController',
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/server/{id}/edit", name="app_server_edit")
     */
    public function editServer($id, ServerRepository $serverRepository, Request $request, ManagerRegistry $doctrine)
    {
        $server = $serverRepository->find($id);

        $form = $this->createForm(ServerType::class, $server);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();

            $em->persist($server);
            $em->flush();

            $this->addFlash("success", "Serveur modifié !");
            return $this->redirectToRoute("app_server_read", ["id" => $id]);
        }

        return $this->render("/servers/edit.html.twig", [
            "form" => $form->createView(),
            "server" => $server
        ]);
    }

    /**
     * @Route("/servers", name="app_servers_read")
     */
    public function readServers(): Response
    {
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

        return $this->render("servers/list.html.twig", [
            "servers" => $servers
        ]);
    }

    /**
     * @Route("/server/{id}", name="app_server_read")
     */
    public function readServer($id, ServerRepository $serverRepository)
    {
        $server = $serverRepository->find($id);

        return $this->render("servers/read.html.twig", [
            "server" => $server
        ]);
    }

    /**
     * @Route("/server/{id}/parse", name="app_server_parse")
     */
    public function parseServer($id, ServerRepository $serverRepository, ManagerRegistry $doctrine, WebsiteRepository $websiteRepository, Request $request)
    {
        $server = $serverRepository->find($id);

        $em = $doctrine->getManager();
        $server->setUpdatedAt(new \DateTime("now"));
        $em->persist($server);
        $em->flush();

        new ServerParser($server, $doctrine, $websiteRepository);

        $this->addFlash("success", "Sites du serveur mis à jour !");
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * @Route("/servers/parse", name="app_servers_parse")
     */
    public function parseAllServers(ServerRepository $serverRepository, ManagerRegistry $doctrine, WebsiteRepository $websiteRepository)
    {
        $servers = $serverRepository->findAllServersNotUpdated($this->getUser()->getEmail());

        foreach ($servers as $server) {
            new ServerParser($server, $doctrine, $websiteRepository);
        }

        $this->addFlash("success", "Sites du serveur mis à jour !");
    }

    /**
     * @Route("/server/{id}/delete", name="app_server_delete")
     */
    public function deleteServer($id, ServerRepository $serverRepository, ManagerRegistry $doctrine, Request $request)
    {
        $server = $serverRepository->find($id);

        if ($this->getUser() !== $server->getCreatedBy() && !$this->isGranted("ROLE_ADMIN")) {
            $this->addFlash("danger", "Vous ne pouvez supprimer que les serveurs que vous avez crée.");
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        $em = $doctrine->getManager();
        $em->remove($server);
        $em->flush();
        $this->addFlash("success", "Serveur supprimé");
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}
