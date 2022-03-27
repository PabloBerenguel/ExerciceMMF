<?php

namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends Controller
{
    /**
     * @Route("/api/login", name="AuthController.login", methods={"POST"})
     */
    public function login(Request $oRequest, UserRepository $oUserRepository, ManagerRegistry $oDoctrine): Response
    {
        $aKeys = [
            "email",
            "password"
        ];

        $aCleanData = $this->parseParameters($oRequest->toArray(), $aKeys);

        if (!$aCleanData["success"])
            return $this->jsonResponse($aCleanData["message"], $aCleanData["code"]);

        $oUser = $oUserRepository->findOneBy(["email" => $aCleanData["email"]]);
        if (!$oUser)
            return $this->jsonResponse(json_encode(["auth" => "authentication failed"]), Response::HTTP_BAD_REQUEST);

        if (UserController::hashPassword($aCleanData["password"]) != $oUser->getPassword())
            return $this->jsonResponse(json_encode(["auth" => "authentication failed"]), Response::HTTP_BAD_REQUEST);

        dd($oUser->getAuthToken());
    }
}
