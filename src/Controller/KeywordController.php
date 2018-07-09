<?php

namespace App\Controller;

use App\Entity\Keyword;
use App\Entity\Query;
use App\Entity\Site;
use App\Repository\KeywordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * @Route("/keyword")
 */
class KeywordController extends Controller
{
    /**
     * @Route("/", name="keyword_index", methods="GET")
     */
    public function index(KeywordRepository $keywordRepository): Response
    {
        return $this->render('keyword/index.html.twig', ['keywords' => $keywordRepository->findAll()]);
    }
    
    /**
     * @Route("/read", name="keyword_read", methods="GET")
     */
    public function readFile(): Response
    {
        $yaml = Yaml::parseFile('assets/sites.yml');
        
        foreach($yaml['websites'] as $site){
            $entityManager = $this->getDoctrine()->getManager();
            
            $findSite = $this->getDoctrine()->getRepository(Site::class)->findOneBy(
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
                $findKeyword = $this->getDoctrine()->getRepository(Keyword::class)->findOneBy(
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
        
        return $this->render('keyword/read.html.twig', array('yaml' => $yaml));
    }

    /**
     * @Route("/readApi", name="keyword_read_api", methods="GET")
     */
    public function readApi()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $sites = $this->getDoctrine()->getRepository(Site::class)->findAll();
        foreach ($sites as $site){
            $keywords = $this->getDoctrine()->getRepository(Keyword::class)->findBy(
                ['site' => $site]
            );
            foreach ($keywords as $keyword){
                $found = 0;
                $start = 1;
                while($found === 0){
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/customsearch/v1?q=' . urlencode($keyword->getKeyword()) . '&start=' . $start . '&key=' . $this->getParameter('googleapi.key') . '&cx=' . $this->getParameter('googleapi.cx'));
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                    $response = curl_exec($curl);
                    $data = json_decode($response);

                    foreach ($data->items as $item) {
                        if(!(strpos($item->link, $site->getName())) === false){
                            $query = new Query();
                            $query->setKeyword($keyword);
                            $query->setResponseCode('1');
                            $query->setPosition(key($item)+1);
                            $query->setQueryTime(new \DateTime());
                            $entityManager->persist($query);
                            $found = 1;
                        }
                    }

                }
            }
        }

        $entityManager->flush();

        return new Response(
            '<html><body>ok</body></html>'
        );
    }

    /**
     * @Route("/readApiTest", name="keyword_readtest_api", methods="GET")
     */
    public function readApiTest()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/customsearch/v1?q=wiadomosci&key=' . $this->getParameter('googleapi.key') . '&cx=' . $this->getParameter('googleapi.cx'));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($curl);

        $data = json_decode($response);
        dump($data);exit;
    }
}
