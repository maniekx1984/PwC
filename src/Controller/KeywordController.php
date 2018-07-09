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
        $yaml = Yaml::parseFile('public/assets/sites.yml');
        
        foreach($yaml['websites'] as $site){
            $entityManager = $this->container->get('doctrine')->getManager();
            
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
            
            $entityManager->flush();
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
            foreach ($keywords as $keyword){
                $start = 1;
                $url = 'https://www.googleapis.com/customsearch/v1?q=' . urlencode($keyword->getKeyword()) . '&start=' . $start . '&key=' . $this->getParameter('googleapi.key') . '&cx=' . $this->getParameter('googleapi.cx');
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $response = curl_exec($curl);
                $data = json_decode($response);

                $position = 1;

                if(count($data->items) > 0) {
                    foreach ($data->items as $item) {
                        $https = substr($site->getName(), 0, 5);
                        if($https == "https"){
                            $linkToCheck = substr($site->getName(), 8);
                        } else {
                            $linkToCheck = substr($site->getName(), 7);
                        }
                        if (strpos($item->displayLink, $linkToCheck) === 0) {
                            $query = new Query();
                            $query->setKeyword($keyword);
                            $query->setResponseCode(get_headers($url)[0]);
                            $query->setPosition($position);
                            $query->setQueryTime(new \DateTime());
                            $entityManager->persist($query);
                            break;
                        }
                        $position++;
                    }
                }
            }
        }

        $entityManager->flush();
    }
}
