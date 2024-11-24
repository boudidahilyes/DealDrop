<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\DeleteType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{

  // private $requestStack;

  //   public function __construct(RequestStack $requestStack)
  //   {
  //       $this->requestStack = $requestStack;

  //       // Accessing the session in the constructor is *NOT* recommended, since
  //       // it might not be accessible yet or lead to unwanted side-effects
  //       // $this->session = $requestStack->getSession();
  //   }


  #[Route(path: '/admin/home', name: 'app_home_admin', methods: ['GET'])]
  public function index(Request $req,UsersRepository $userRepository): Response
  {
      $user=$this->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
          $users = $userRepository->findByRole("ROLE_USER");
          return $this->render('back/dashboard.html.twig', [
            'users' => $users,
          ]);
            

        }
        // $session = $req->getSession();
        // dump($session);
        // $foo = $session->get('Roles');
        // dump($foo);die;

        return $this->redirectToRoute('app_home');
    
      
  }
  #[Route('/admin/users/{role}', name: 'app_admin_users', methods: ['GET'])]
    public function displayMembers(UsersRepository $userRepository,$role): Response
    {
      $users = $userRepository->findByRole($role);

      /*$deleteForms = [];
      foreach ($users as $user) {
          $deleteForm = $this->createForm(DeleteType::class, null, [
              'csrf_token' => 'delete' . $user->getId(),
              
          ]);

          $deleteForms[$user->getId()] = $deleteForm->createView();
      }*/

      return $this->render('back/usersList.html.twig', [
          'users' => $users,
          //'deleteForms' => $deleteForms,
      ]);
    } 
    #[Route('/user/all/delete/{id}', name: 'app_user_edit_delete')]
    public function delete(Request $request, Users $user, UsersRepository $userRepository,$id,EntityManagerInterface $em): Response
    {
        
       /* if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            
            return $this->redirectToRoute('app_home');
        }
        
        return $this->redirectToRoute('app_user_edit_index');*/
        $user=$userRepository->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('app_admin_users');
    }

  
}
