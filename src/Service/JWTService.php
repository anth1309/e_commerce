<?php

namespace App\Service;

use DateTime;
use DateTimeImmutable;

class JWTService
{
    //genere le token $validity duree de validité en seconde
    /**
     * Undocumented function
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param integer $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 900): string
    {
        if ($validity > 0) {


            $now = new DateTimeImmutable();
            $expiration = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['expiration'] = $expiration;
        }

        //on encode en base 64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        //on nettoie les valeurs encoder (retrait + / =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        //generer la signature
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        //creation du token
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;



        return $jwt;
    }
    //on verify token bien formé
    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token

        ) === 1;
    }
    //expiration du token donc recupere Payload
    public function getPayload(string $token): array
    {
        //demonte le token
        $array = explode('.', $token);
        //on decode le paylode deuxieme parti du token
        $payload = json_decode(base64_decode($array[1]), true);
        return $payload;
    }


    //on recupere le header
    public function getHeader(string $token): array
    {
        //demonte le token
        $array = explode('.', $token);
        //on decode le header premiére partie du token
        $header = json_decode(base64_decode($array[0]), true);
        return $header;
    }



    //on verifie l expiration
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable();
        return $payload['expiration'] < $now->getTimestamp();
    }
    //on verifie la signature du token
    public function check(string $token, string $secret)
    {
        //on recupere le header et payload
        $header = $this->getHeader(($token));
        $payload = $this->getPayload(($token));
        //on regenere un token pour comparer la signature
        $verifToken = $this->generate($header, $payload, $secret, 0);
        return $token === $verifToken;
    }
}
