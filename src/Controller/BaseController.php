<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class BaseController extends Controller
{
    /**
     * @Route("/", name="base", methods="GET")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('query_index');
    }
}