<?php

namespace App\Controller;

use App\Entity\Keyword;
use App\Entity\Query;
use App\Entity\Site;
use App\Repository\KeywordRepository;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class KeywordController extends Controller
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function readFile()
    {
        try {
            $yaml = Yaml::parseFile('public/assets/sites.yml');

            foreach($yaml['websites'] as $site){
                $entityManager = $this->container->get('doctrine')->getManager();

                $findSite = $this->readFileFindSite($site, $entityManager);

                $this->readFileSaveNewKeywords($entityManager, $site, $findSite);

                $entityManager->flush();
            }
        } catch (ParseException $exception) {
            printf('Unable to parse the YAML string: %s', $exception->getMessage());
        } catch (Exception $exception) {
            echo "Błąd podczas odczytu pliku - brak pliku i/lub niewłaściwa struktura.";
        }
        
        
    }
    
    private function readFileFindSite($site, $entityManager): Site
    {
        $findSite = $this->container->get('doctrine')->getRepository(Site::class)->findOneBy(
                ['name' => key($site)]
                );
        if(!$findSite){
            $newSite = new Site();
            $newSite->setName(key($site));
            $entityManager->persist($newSite);
        } else {
            $newSite = $findSite;
        }
        return $newSite;
    }
    
    private function readFileSaveNewKeywords($entityManager, $site, $newSite)
    {
        foreach($site[key($site)] as $keyword){
            $findKeyword = $this->container->get('doctrine')->getRepository(Keyword::class)->findOneBy(
                ['keyword' => $keyword,
                    'site' => $newSite]
                );
            if(!$findKeyword){
                $newKeyword = new Keyword();
                $newKeyword->setKeyword($keyword);
                $newKeyword->setSite($newSite);
                $entityManager->persist($newKeyword);
            }
        }
    }

    public function readApi()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();
        foreach ($sites as $site){
            $keywords = $this->getDoctrine()->getRepository(Keyword::class)->findBy(
                ['site' => $site]
            );
            $this->readApiSearchForKeywords($entityManager, $keywords, $site);
        }

        $entityManager->flush();
    }
    
    private function readApiSearchForKeywords($entityManager, $keywords, $site)
    {
        foreach ($keywords as $keyword){
            $url = 'https://www.googleapis.com/customsearch/v1?q=' . urlencode($keyword->getKeyword()) . '&key=' . $this->getParameter('googleapi.key') . '&cx=' . $this->getParameter('googleapi.cx');
            
            $data = json_decode($this->getCurlResponse($url));
            $responseCode = get_headers($url)[0];

            $position = 1;
            $found = false;

            if(count($data->items) > 0) {
                $this->readApiGetAndSaveItems(
                        $data,
                        $entityManager,
                        $site,
                        $keyword,
                        $position,
                        $found,
                        $responseCode);
            }
            
            $this->saveIfNotFound($found, $entityManager, $keyword, $responseCode);
        }
    }
    
    private function getCurlResponse($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);
        
        return $response;
    }
    
    private function readApiGetAndSaveItems($data, $entityManager, $site, $keyword, $position, &$found, $responseCode)
    {
        foreach ($data->items as $item) {
            $linkToCheck = $this->getLinkToCheck($site);
            $orLinkToCheck = $this->getOrLinkToCheck($linkToCheck);
            
            if (strpos($item->displayLink, $linkToCheck) === 0 || strpos($item->displayLink, $orLinkToCheck) === 0) {
                $query = new Query();
                $query->setKeyword($keyword);
                $query->setResponseCode($responseCode);
                $query->setPosition($position);
                $query->setQueryTime(new \DateTime());
                $entityManager->persist($query);
                $found = true;
                break;
            }
            $position++;
        }
    }
    
    private function saveIfNotFound($found, $entityManager, $keyword, $responseCode){
        if($found === false){
            $query = new Query();
            $query->setKeyword($keyword);
            $query->setResponseCode($responseCode);
            $query->setPosition(0);
            $query->setQueryTime(new \DateTime());
            $entityManager->persist($query);
        }
    }
    
    private function getLinkToCheck($site)
    {
        $https = substr($site->getName(), 0, 5);
        if($https == "https"){
            $linkToCheck = substr($site->getName(), 8);
        } else {
            $linkToCheck = substr($site->getName(), 7);
        }
        return $linkToCheck;
    }
    
    private function getOrLinkToCheck($linkToCheck)
    {
        if(strpos($linkToCheck, 'www.') === false){
            $orLinkToCheck = 'www.' . $linkToCheck;
        } else {
            $orLinkToCheck = str_replace('www.', '', $linkToCheck);
        }
        return $orLinkToCheck;
    }
}
