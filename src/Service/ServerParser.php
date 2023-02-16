<?php

namespace App\Service;

use App\Entity\Server;
use App\Entity\Website;
use phpseclib3\Net\SFTP;
use Doctrine\ORM\EntityManager;
use App\Repository\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpseclib3\Exception\UnableToConnectException;

class ServerParser
{

    private array $sites;

    private EntityManager $em;

    private WebsiteRepository $websiteRepository;

    private $sftp;

    private Server $server;

    public function __construct(Server $server, ManagerRegistry $doctrine, WebsiteRepository $websiteRepository)
    {
        $this->em = $doctrine->getManager();
        $this->websiteRepository = $websiteRepository;
        $this->server = $server;
    }

    public function parse()
    {
        $this->sftp = new SFTP($this->server->getHost());
        $loginState = $this->sftp->login($this->server->getUsername(), $this->server->getPassword());

        if(!$loginState) return "Identifiants incorrects.";

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
