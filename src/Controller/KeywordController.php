<?php

namespace App\Controller;

use App\Entity\Keyword;
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
                    ['keyword' => $keyword]
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
}
