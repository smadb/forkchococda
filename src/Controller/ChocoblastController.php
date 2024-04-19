<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ChocoblastService;
use App\Form\ChocoblastType;
use App\Entity\Chocoblast;
use App\Repository\ChocoblastRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChocoblastController extends AbstractController
{
    public function __construct(
        private readonly ChocoblastService $chocoblastService
    ) {
    }

    #[Route('/chocoblast/add', name: 'app_chocoblast_add')]
    public function create(
        Request $request,
        ChocoblastService $chocoblastService
    ): Response {

        $chocoblast = new Chocoblast();
        //création du formulaire
        $form = $this->createForm(ChocoblastType::class, $chocoblast);
        //récupération de la requête
        $form->handleRequest($request);
        //test si le formulaire est submit
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                //ajout du chocoblast en BDD
                $chocoblast->setStatus(false);
                $chocoblast->setAuthor($this->getUser());
                $this->chocoblastService->create($chocoblast);
                $msg = "le chocoblast a ete ajoute";
                $type = "success";
            } catch (\Throwable $th) {
                $this->addFlash("danger", $th->getMessage());
                echo $th->getMessage();
                $msg = $th->getMessage();
                $type = "danger";
            }
            $this->addFlash($type, $msg);
        }
        return $this->render('chocoblast/addChocoblast.html.twig', [
            'formulaire' => $form,
        ]);
    }

    #[Route('/chocoblast/all', name:'app_chocoblast_all')]
    public function showAllChocoblast(ChocoblastService $chocoblastService):Response
    {
        $chocoblasts = $chocoblastService->findAll();

        return $this->render('chocoblast/showAllChocoblast.html.twig', [
            'chocoblasts' => $chocoblasts,
        ]);
    }
    #[Route] #[Route('/chocoblast/showOne/{id}', name: 'app_chocoblast_show')]
    public function showChocoblast(ChocoblastService $chocoblastService, int $id): Response
    {
        $chocoblast = $chocoblastService->findOneBy($id);
        return $this->render('chocoblast/showChocoblast.html.twig', [
            'chocoblast' => $chocoblast,
        ]);
    }

    #[Route] #[Route('/chocoblast/manage', name: 'app_chocoblast_manage')]
    public function edit(
        ChocoblastService $chocoblastService,
    ): Response
    {
        $chocoblasts = $chocoblastService->findAll();

        return $this->render('chocoblast/manageChocoblast.html.twig', [
            'chocoblasts' => $chocoblasts,
        ]);
    }

    #[Route] #[Route('/chocoblast/manage/{id}', name: 'app_chocoblast_manageOne')]
    public function editOne(
        ChocoblastService $chocoblastService, int $id
    ): Response
    {
        $chocoblasts = [$chocoblastService->findOneBy($id)];

        return $this->render('chocoblast/manageChocoblast.html.twig', [
            'chocoblasts' => $chocoblasts,
        ]);
    }

    #[Route('/chocoblast/active/{id}', name:'app_chocoblast_active')]
    public function active(ChocoblastService $chocoblastService , int $id , Request $request,RouterInterface $router): Response
    {
        $lastUrl = $request->headers->get('referer');
        $lastRoute = 'unknown';
        $parameters = array();

        if ($lastUrl) {
            $lastPath = parse_url($lastUrl, PHP_URL_PATH);
            $routeInfo = $router->match($lastPath);
            $lastRoute = $routeInfo['_route'];
            // Remove _route and _controller items
            unset($routeInfo['_route']);
            unset($routeInfo['_controller']);
            $parameters = $routeInfo;
        }

        $chocoblast = $chocoblastService->findOneBy($id);
        $chocoblast->setStatus(true);
        $chocoblastService->update($chocoblast);
        $this->addFlash("success", "Chocoblast activated successfully");
            return $this->redirectToRoute($lastRoute,$parameters);
    }
    #[Route('/chocoblast/inactive/{id}', name:'app_chocoblast_inactive')]
    public function inactive(ChocoblastService $chocoblastService , int $id, Request $request ,RouterInterface $router): Response
    {
        $lastUrl = $request->headers->get('referer');
        $lastRoute = 'unknown';
        $parameters = array();

        if ($lastUrl) {
            $lastPath = parse_url($lastUrl, PHP_URL_PATH);
            $routeInfo = $router->match($lastPath);
            $lastRoute = $routeInfo['_route'];
            // Remove _route and _controller items
            unset($routeInfo['_route']);
            unset($routeInfo['_controller']);
            $parameters = $routeInfo;
        }

        $chocoblast = $chocoblastService->findOneBy($id);
        $chocoblast->setStatus(false);
        $chocoblastService->update($chocoblast);
        $this->addFlash("success", "Chocoblast deactivated successfully");
        return $this->redirectToRoute($lastRoute,$parameters);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/chocoblast/all/inactive', name: 'app_chocoblast_all_inactive')]
    public function showAllChocoblastInactive(): Response
    {
        $chocoblasts = $this->chocoblastService->findActiveOrNot(false);

        return $this->render('chocoblast/showAllChocoblastInactive.html.twig', [
            'chocoblasts' => $chocoblasts,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/chocoblast/active/{id}', name: 'app_chocoblast_active')]
    public function activeChocoblast($id): Response
    {
        $chocoblast = $this->chocoblastService->findOneBy($id);
        $chocoblast->setStatus(true);
        $this->chocoblastService->update($chocoblast);
        return $this->redirectToRoute('app_chocoblast_all_inactive');
    }

    #[Route('/chocoblast/top/auteur', name:'app_chocoblast_top_auteur')]
    public function topAuthor():Response
    {
        $topAuthor = $this->chocoblastService->getCountChocoblastAuthor();
        $json = $this->json($topAuthor);
        return $this->render('chocoblast/topAuteur.html.twig', [
            'topAuthor'=> $json->getContent(),
        ]);
    }

    #[Route('/chocoblast/top/cible', name:'app_chocoblast_top_cible')]
    public function topTarget():Response
    {
        $topTarget = $this->chocoblastService->getCountChocoblastTarget();
        $json = $this->json($topTarget);
        return $this->render('chocoblast/topTarget.html.twig', [
            'topTarget'=> $json->getContent(),
        ]);
    }
    #[Route('/chocoblast/top', name:'app_chocoblast_top')]
    public function top():Response
    {
        return $this->render('chocoblast/topGraph.html.twig');
    }
}
