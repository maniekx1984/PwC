<?php

namespace App\Controller;

use App\Entity\Query;
use App\Form\QueryType;
use App\Repository\QueryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/query")
 */
class QueryController extends Controller
{
    /**
     * @Route("/", name="query_index", methods="GET")
     */
    public function index(QueryRepository $queryRepository): Response
    {
        return $this->render('query/index.html.twig', ['queries' => $queryRepository->findAll()]);
    }
}
