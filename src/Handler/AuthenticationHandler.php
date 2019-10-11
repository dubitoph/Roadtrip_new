<?php

namespace App\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationHandler implements AuthenticationFailureHandlerInterface, LogoutSuccessHandlerInterface
{

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    { 

        $session = $request->getSession();
        
        $referer = $request->headers->get('referer');       
        $session->getFlashBag()->add('error', $exception->getMessage());
        $session->set('errorFomAuthentication', true);

        return new RedirectResponse($referer);

    }

    public function onLogoutSuccess(Request $request) 
    {

        $referer = $request->headers->get('referer');

        return new RedirectResponse($referer);

    }

}