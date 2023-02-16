<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ServerRepository;
use App\Repository\WebsiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SidebarController extends AbstractController
{
    public function sidebar(ServerRepository $serverRepository, WebsiteRepository $sitesRepository): Response
    {
        if ($this->getUser()) {
            /** @var User */
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
                if (!in_array($server, $servers)) {
                    array_push($servers, $server);
                }
            }

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
