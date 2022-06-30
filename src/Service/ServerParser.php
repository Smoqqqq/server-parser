<?php

namespace App\Service;

use App\Entity\Server;
use App\Entity\Website;
use phpseclib3\Net\SFTP;
use App\Repository\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpseclib3\Exception\UnableToConnectException;

class ServerParser
{

    /** @var Array */
    private $sites;

    /** @var EntityManager */
    private $em;

    /** @var WebsiteRepository */
    private $websiteRepository;

    private $sftp;

    public function __construct(Server $server, ManagerRegistry $doctrine, WebsiteRepository $websiteRepository)
    {

        $this->em = $doctrine->getManager();
        $this->websiteRepository = $websiteRepository;
        $this->server = $server;
    }

    public function parse()
    {
        $this->sftp = new SFTP($this->server->getHost());
        $this->sftp->login($this->server->getUsername(), $this->server->getPassword());

        $rootPath = $this->server->getRootDirectory();

        $files = $this->sftp->nlist($rootPath);
        if (!$files) return "Identifiants et/ou dossier raçine incorrect (impossible de se connecter au serveur)";

        $this->sites = array_diff($files, [".", ".."]);
        $this->registerSites();

        return false;
    }

    public function registerSites()
    {
        $alreadyPresentSites = $this->server->getWebsites();
        $urls = [];

        foreach ($this->sites as $site) {
            $website = $this->websiteRepository->findBy(["url" => $site]);

            array_push($urls, $site);

            if ($website === []) {
                $website = new Website();
                $website->setUrl($site);
                $website->setServer($this->server);
                $this->em->persist($website);
            }
        }

        foreach ($alreadyPresentSites as $site) {
            if (!in_array($site->getUrl(), $urls)) {
                $this->em->remove($site);
            }
        }

        $this->em->flush();
    }
}
