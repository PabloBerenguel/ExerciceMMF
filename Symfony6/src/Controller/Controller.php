<?php

namespace App\Controller;

use App\Enum\TokenType;
use App\Repository\AuthTokenRepository;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

abstract class Controller extends AbstractController implements TokenAuthenticatedController
{
    protected array $aError = [
        "success" => false,
        "message" => null,
        "code" => null,
    ];

    public function authenticate($oHeaders, AuthTokenRepository $oTokenRepository) : array {
        $aAuthorization = $oHeaders->all("Authorization");

        if (!$aAuthorization || empty($aAuthorization) || !str_contains($aAuthorization[0], "Bearer ")){
            $aError = $this->aError;
            $aError["message"] = json_encode(["authentication" => "authentication failed"]);
            $aError["code"] = Response::HTTP_BAD_REQUEST;
            return $aError;
        }

        $sBearer = substr($aAuthorization[0], 7);

        $oAccessToken = $oTokenRepository->findOneBy(["token" => $sBearer, "type" => TokenType::ACCESS]);

        if (!$oAccessToken || $oAccessToken->getValidUntil() < (new DateTimeImmutable('now'))){
            $aError = $this->aError;
            $aError["message"] = json_encode(["authentication" => "authentication failed"]);
            $aError["code"] = Response::HTTP_BAD_REQUEST;
            return $aError;
        }

        $aSuccess = [
            "success" => true,
            "accessToken" => $oAccessToken,
        ];

        return $aSuccess;
    }

    public function jsonResponse($sMessage, $iCode) : Response {
        $oResponse = new Response($sMessage, $iCode);
        $oResponse->headers->set('Content-Type', 'application/json');
        return $oResponse;
    }

    public function error(string $sKey, string $sMessage, int $iCode) : array {
        $this->aError["message"] = json_encode([$sKey => $sMessage]);
        $this->aError["code"] = $iCode;
        return $this->aError;
    }

    public function parseParameters(array $aData, array $aKeys) : array {
        $aResult = [
            "success" => true,
        ];

        foreach ($aKeys as $sKey){
            if (!isset($aData[$sKey]) || $aData[$sKey] == "" || !$aData[$sKey])
                return $this->error($sKey, "$sKey is missing.", Response::HTTP_BAD_REQUEST);
            $aResult[$sKey] = $aData[$sKey];
        }

        return $aResult;
    }
}
