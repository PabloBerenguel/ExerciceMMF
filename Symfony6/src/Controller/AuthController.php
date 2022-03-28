<?php

namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\User;
use App\Enum\TokenType;
use App\Repository\AuthTokenRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/// Todo all routes from this class are not in the Api Platform due to time limitation
class AuthController extends Controller
{
    /**
     * @Route("/api/auth/login", name="AuthController.login", methods={"POST"})
     */
    public function login(Request $oRequest, UserRepository $oUserRepository, ManagerRegistry $oDoctrine): Response
    {
        $oEntityManager = $oDoctrine->getManager();

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


        /// Create refresh token
        $oAuthRefreshToken = new AuthToken();
        $oAuthRefreshToken->setType(TokenType::REFRESH);
        $oAuthRefreshToken->setToken(md5(random_bytes(10)));
        $oAuthRefreshToken->setUser($oUser);
        $oAuthRefreshToken->setValidUntil((new DateTimeImmutable('now'))->modify('+2 day')); /// TODO: Set durations in the .env
        $oEntityManager->persist($oAuthRefreshToken);
        $oEntityManager->flush();

        /// Create access token
        $oAuthAccessToken = new AuthToken();
        $oAuthAccessToken->setType(TokenType::ACCESS);
        $oAuthAccessToken->setToken(md5(random_bytes(10)));
        $oAuthAccessToken->setUser($oUser);
        $oAuthAccessToken->setValidUntil((new DateTimeImmutable('now'))->modify('+1 day')); /// TODO: Set durations in the .env
        $oAuthAccessToken->setRefreshToken($oAuthRefreshToken);


        $oEntityManager->persist($oAuthAccessToken);
        $oEntityManager->flush();

        return $this->jsonResponse(json_encode(
            [
                "access_token" => [
                    "token" => $oAuthAccessToken->getToken(),
                    "valid_until" => $oAuthAccessToken->getValidUntil()
                ],
                "refresh_token" => [
                    "token" => $oAuthRefreshToken->getToken(),
                    "valid_until" => $oAuthRefreshToken->getValidUntil()
                ],
            ],
        ), Response::HTTP_OK);
    }

    public function refreshAccessToken(Request $oRequest): Response | null
    {
        /// TODO: Delete access token and create a new one linked to the refresh_token
        return null;
    }

    /**
     * @Route("/api/auth/logout", name="AuthController.logout", methods={"POST"})
     */
    public function logout(Request $oRequest, AuthTokenRepository $oAuthTokenRepository, ManagerRegistry $oDoctrine) : Response {
        $aLoggedUserData = $this->authenticate($oRequest->headers, $oAuthTokenRepository);
        if (!$aLoggedUserData["success"])
            return $this->jsonResponse($aLoggedUserData["message"], $aLoggedUserData["code"]);
        $oAccessToken = $aLoggedUserData["accessToken"];

        $oEntityManager = $oDoctrine->getManager();
        $oEntityManager->remove($oAccessToken->getRefreshToken());
        $oEntityManager->flush();
        return $this->jsonResponse("", Response::HTTP_OK);
    }

    /**
     * @Route("/api/auth/me", name="AuthController.me", methods={"POST"})
     */
    public function me(Request $oRequest, AuthTokenRepository $oAuthTokenRepository) : Response {
        $aLoggedUserData = $this->authenticate($oRequest->headers, $oAuthTokenRepository);
        if (!$aLoggedUserData["success"])
            return $this->jsonResponse($aLoggedUserData["message"], $aLoggedUserData["code"]);
        $oAccessToken = $aLoggedUserData["accessToken"];
        $oUser = $oAccessToken->getUser();
        return $this->jsonResponse(json_encode(["id" => $oUser->getId(), "email" => $oUser->getEmail(), "firstName" => $oUser->getFirstName(), "lastName" => $oUser->getLastName()]), Response::HTTP_OK);
    }
}
