<?php
namespace IMSGlobal\LTI;

use phpseclib\Crypt\RSA;
use \Firebase\JWT\JWT;
use IMSGlobal\LTI\JWT_Proxy;

/**
 * JWKS endpoint for public key distribution.
 * @package IMSGlobal\LTI
 */
class JWKS_Endpoint
{
    /**
     * @var array<string, string> Key ID to private key
     */
    private $keys;

    /**
     * @param array<string, string> $keys
     */
    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    /**
     * Create new JWKS_Endpoint instance.
     * @param array<string, string> $keys
     * @return JWKS_Endpoint
     */
    public static function new($keys)
    {
        return new JWKS_Endpoint($keys);
    }

    /**
     * Create from issuer.
     * @param Database $database
     * @param string $issuer
     * @return JWKS_Endpoint
     */
    public static function from_issuer(Database $database, $issuer)
    {
        $registration = $database->find_registration_by_issuer($issuer);
        return new JWKS_Endpoint([$registration->get_kid() => $registration->get_tool_private_key()]);
    }

    /**
     * Create from registration.
     * @param LTI_Registration $registration
     * @return JWKS_Endpoint
     */
    public static function from_registration(LTI_Registration $registration)
    {
        return new JWKS_Endpoint([$registration->get_kid() => $registration->get_tool_private_key()]);
    }

    /**
     * Get public JWKS array.
     * @return array<string, array<string, string>>
     */
    public function get_public_jwks()
    {
        $jwks = [];
        foreach ($this->keys as $kid => $private_key) {
            $key = new RSA();
            $key->setHash("sha256");
            $key->loadKey($private_key);
            $key->setPublicKey(false, RSA::PUBLIC_FORMAT_PKCS8);
            if (!$key->publicExponent) {
                continue;
            }
            $components = array(
                'kty' => 'RSA',
                'alg' => 'RS256',
                'use' => 'sig',
                'e' => JWT_Proxy::urlsafeB64Encode($key->publicExponent->toBytes()),
                'n' => JWT_Proxy::urlsafeB64Encode($key->modulus->toBytes()),
                'kid' => $kid,
            );
            $jwks[] = $components;
        }
        return ['keys' => $jwks];
    }

    /**
     * Output JWKS as JSON.
     * @return void
     */
    public function output_jwks()
    {
        echo json_encode($this->get_public_jwks());
    }

}