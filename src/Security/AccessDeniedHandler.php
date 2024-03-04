<?php 
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        if ($this->security->isGranted('ROLE_USER')) {
            
            return new RedirectResponse('/home');
        } else {
            
            return new RedirectResponse('/login');
        }
    }
}