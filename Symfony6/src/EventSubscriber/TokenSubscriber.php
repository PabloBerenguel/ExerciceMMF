<?php

namespace App\EventSubscriber;

use App\Controller\Controller;
use App\Controller\TokenAuthenticatedController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

class TokenSubscriber extends  controller implements EventSubscriberInterface
{

    private array $aMethodsWithToken = [ /// TODO: find a better way
        "get",
    ];

    public function onKernelController(ControllerEvent $event)
    {
        /// TODO: Setup bearer token middleware here
        /*
        $aController = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($aController)) {
            $oController = $aController[0];
            $sMethodName = $aController[1];
        }
        else
            return ;

        if ($oController instanceof TokenAuthenticatedController && in_array($sMethodName, $this->aMethodsWithToken)) {
            $aAuthorization = $event->getRequest()->headers->all("Authorization");

            if (!$aAuthorization || empty($aAuthorization))
                return $this->jsonResponse(json_encode(["authentication" => "authentication failed"]), Response::HTTP_BAD_REQUEST);

            $token = $event->getRequest();
            /*
            if (!in_array($token, $this->tokens)) {
                throw new AccessDeniedHttpException('This action needs a valid token!');
            }
        }
        */
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
