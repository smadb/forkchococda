<?php


namespace App\Controller;

use App\Repository\ChocoblastRepository;
use App\Repository\CommentaryRepository;
use App\Service\ChocoblastService;
use App\Service\CommentaryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\RouterInterface;

class UserController extends AbstractController
{
    #[Route('/users/manage', name: 'app_user_manage')]
    public function showAllUsers(UserService $userService, ChocoblastRepository $chocoblastRepository, CommentaryRepository $commentaryRepository): Response
    {
        $users = $userService->findAll();
        $userChocoblasts = [];
            foreach ($users as $user) {

                $userChocoblasts[$user->getId()] = $chocoblastRepository->getChocoblastByUserId($user->getId());
            }

        return $this->render('users/showAllUsers.html.twig', [
            'users' => $users,
            'userChocoblasts'=> $userChocoblasts
        ]);
    }
    #[Route('/user/changerole/{id}', name: 'app_user_changerole')]
    public function changeUserRole (int $id,Request $request, UserService $userService,RouterInterface $router)
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

        $user = $userService->findOneBy($id);
        $roleList = $user->getRoles();

        if(array_search('ROLE_ADMIN', $roleList, true)){
            return $this->redirectToRoute('app_user_manage', $parameters);
        }
        else{
            array_push($roleList,'ROLE_ADMIN');
            $user->setRoles($roleList);
            $userService->update($user);
            return $this->redirectToRoute('app_user_manage', $parameters);
        }



    }
}

