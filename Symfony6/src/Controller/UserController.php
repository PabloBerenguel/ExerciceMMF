<?php

namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\AuthTokenRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Annotation\ApiResource;

class UserController extends Controller
{
    protected ?UserRepository $oUserRepository = null;
    protected ?ManagerRegistry $oDoctrine = null;
    protected ?AuthTokenRepository $oAuthTokenRepository = null;

    public function __construct(UserRepository $oUserRepository, ManagerRegistry $oDoctrine, AuthTokenRepository $oAuthTokenRepository)
    {
        $this->oUserRepository = $oUserRepository;
        $this->oDoctrine = $oDoctrine;
        $this->oAuthTokenRepository = $oAuthTokenRepository;
    }

    /**
     * @Route("/api/users", name="UserController.create", methods={"POST"})
     */
    public function create(Request $oRequest): Response
    {
        $oEntityManager = $this->oDoctrine->getManager();

        $aKeys = [
            "email",
            "firstName",
            "lastName",
            "password",
            /// "passwordConfirm"
        ];

        $aCleanData = $this->parseParameters($oRequest->toArray(), $aKeys);

        if (!$aCleanData["success"])
            return $this->jsonResponse($aCleanData["message"], $aCleanData["code"]);

        /* TODO: Find a way to add body param to Api Platform
        if ($aCleanData["password"] != $aCleanData["passwordConfirm"])
            return $this->jsonResponse(json_encode(["passwordConfirm" => "password confirmation don't match."]), Response::HTTP_BAD_REQUEST);
        */

        $aTestPasswordComplexity = $this->passwordComplexity($aCleanData["password"]);
        if (!$aTestPasswordComplexity["success"])
            return $this->jsonResponse($aTestPasswordComplexity["message"], $aTestPasswordComplexity["code"]);

        if (!filter_var($aCleanData["email"], FILTER_VALIDATE_EMAIL))
            return $this->jsonResponse(json_encode(["email" => "email format is not valid."]), Response::HTTP_BAD_REQUEST);

        $oUser = $this->oUserRepository->findOneBy(["email" => $aCleanData["email"]]);

        if ($oUser)
            return $this->jsonResponse(json_encode(["email" => "email is already used"]), Response::HTTP_BAD_REQUEST);

        $oUser = new User();
        $oUser->setEmail($aCleanData["email"]);
        $oUser->setPassword($this::hashPassword($aCleanData["password"]));
        $oUser->setFirstName($aCleanData["firstName"]);
        $oUser->setLastName($aCleanData["lastName"]);
        $oUser->setRole(Role::USER);

        $oEntityManager->persist($oUser);
        $oEntityManager->flush();

        return $this->jsonResponse(json_encode([
            "id" => $oUser->getId(),
            "email" => $oUser->getEmail(),
            "first_name" => $oUser->getFirstName(),
            "last_name" => $oUser->getLastName(),
        ]), Response::HTTP_OK);
    }

    private function passwordComplexity(string $sPassword): array {
        $aResult = ["success" => true];

        if (strlen($sPassword) < User::PASSWORD_LENGTH)
            return $this->error("password", "password length is too short, min: " . User::PASSWORD_LENGTH, Response::HTTP_BAD_REQUEST);

        if(!preg_match("/[a-zA-Z]/i", $sPassword)){
            return $this->error("password", "password must contains alpha characters", Response::HTTP_BAD_REQUEST);
        }

        if(!preg_match("/[0-9]/i", $sPassword)){
            return $this->error("password", "password must contains numeric characters", Response::HTTP_BAD_REQUEST);
        }

        if (!preg_match('/[\'!^£$%&*()}{@#~?><>,|=_+¬-]/', $sPassword)){
            return $this->error("password", "password must contains special characters", Response::HTTP_BAD_REQUEST);
        }

        return $aResult;
    }


    public static function hashPassword($sPassword): string {
        ///$sSalt = $this->getParameter("app.salt"); /// TODO: Set up in .env
        $sSalt = "SALTSALTSALT";
        return md5($sPassword.$sSalt);
    }

    /**
     * @Route("/api/users", name="UserController.get", methods={"GET"})
     */
    public function get(Request $oRequest) : Response {
        $aLoggedUserData = $this->authenticate($oRequest->headers, $this->oAuthTokenRepository);
        if (!$aLoggedUserData["success"])
            return $this->jsonResponse($aLoggedUserData["message"], $aLoggedUserData["code"]);
        /// TODO: Missing pagination
        $aUsers = $this->oUserRepository->findAll();
        $aUsersData = [];
        foreach ($aUsers as $oUser){
            $aUsersData[] = [
                "id" => $oUser->getId(),
                "email" => $oUser->getEmail(),
                "first_name" => $oUser->getFirstName(),
                "last_name" => $oUser->getLastName(),
            ];
        }
        return $this->jsonResponse(json_encode($aUsersData), Response::HTTP_OK);
    }
}
