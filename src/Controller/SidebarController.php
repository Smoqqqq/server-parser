<?php

namespace App\Controller;

use App\Repository\ServerRepository;
use App\Repository\WebsiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SidebarController extends AbstractController
{
    public function sidebar(ServerRepository $serverRepository, WebsiteRepository $sitesRepository): Response
    {
        if ($this->getUser()) {
            $servers = $this->getUser()->getServers();

            $sites = 0;

            foreach ($servers as $server) {
                $sites += count($server->getWebsites());
            }

            $servers = count($servers);

            return $this->render('blocks/sidebar.html.twig', [
                "servers" => $servers,
                "sites" => $sites
            ]);
        } else {
            return $this->render('blocks/sidebar.html.twig');
        }
    }
}
