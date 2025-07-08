<?php
namespace IMSGlobal\LTI;

use Firebase\JWT\JWT;

/**
 * Proxy class for Firebase JWT static methods and properties.
 * Allows for easier refactoring, testing, and future replacement.
 */
class JWT_Proxy
{
    /**
     * Proxy for JWT::encode
     * @see JWT::encode
     */
    public static function encode(
        $payload,
        $key,
        $alg = 'RS256',
        $keyId = null,
        $head = null
    ) {
        return JWT::encode($payload, $key, $alg, $keyId, $head);
    }

    /**
     * Proxy for JWT::decode
     * @see JWT::decode
     */
    public static function decode($jwt, $key, $algo = 'RS256')
    {
        $keyobject = new \Firebase\JWT\Key($key, $algo);
        return JWT::decode($jwt, $keyobject);
    }

    /**
     * Proxy for JWT::urlsafeB64Encode
     * @see JWT::urlsafeB64Encode
     */
    public static function urlsafeB64Encode($input)
    {
        return JWT::urlsafeB64Encode($input);
    }

    /**
     * Proxy for JWT::urlsafeB64Decode
     * @see JWT::urlsafeB64Decode
     */
    public static function urlsafeB64Decode($input)
    {
        return JWT::urlsafeB64Decode($input);
    }

    /**
     * Proxy for JWT::$leeway static property
     * @see JWT::$leeway
     */
    public static function setLeeway($leeway)
    {
        JWT::$leeway = $leeway;
    }

    /**
     * Proxy for getting JWT::$leeway static property
     */
    public static function getLeeway()
    {
        return JWT::$leeway;
    }
}