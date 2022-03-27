<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

abstract class Controller extends AbstractController
{
    protected array $aError = [
        "success" => false,
        "message" => null,
        "code" => null,
    ];

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
