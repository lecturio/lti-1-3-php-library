<?php

use PHPUnit\Framework\TestCase;
use IMSGlobal\LTI\JWT_Proxy;
use Firebase\JWT\JWT;

class JWT_ProxyTest extends TestCase
{
    private $privateKey;
    private $publicKey;

    protected function setUp(): void
    {
        // Generate a 512-bit RSA key pair for testing
        $res = openssl_pkey_new([
            'private_key_bits' => 512,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($res, $this->privateKey);
        $pubKeyDetails = openssl_pkey_get_details($res);
        $this->publicKey = $pubKeyDetails['key'];
    }

    public function testEncodeAndDecode()
    {
        $payload = ['foo' => 'bar', 'iat' => time()];
        $jwt = JWT_Proxy::encode($payload, $this->privateKey, 'RS256');
        $decoded = JWT_Proxy::decode($jwt, $this->publicKey, ['RS256']);
        $this->assertEquals($payload['foo'], $decoded->foo);
    }

    public function testUrlSafeB64EncodeDecode()
    {
        $data = 'test-string-123';
        $encoded = JWT_Proxy::urlsafeB64Encode($data);
        $decoded = JWT_Proxy::urlsafeB64Decode($encoded);
        $this->assertEquals($data, $decoded);
    }

    public function testSetAndGetLeeway()
    {
        $oldLeeway = JWT_Proxy::getLeeway();
        JWT_Proxy::setLeeway(42);
        $this->assertEquals(42, JWT_Proxy::getLeeway());
        JWT_Proxy::setLeeway($oldLeeway); // restore
    }

    public function testProxyMatchesJWTStaticMethods()
    {
        $payload = ['baz' => 'qux', 'iat' => time()];
        $jwt1 = JWT_Proxy::encode($payload, $this->privateKey, 'RS256');
        $jwt2 = JWT::encode($payload, $this->privateKey, 'RS256');
        $this->assertEquals($jwt2, $jwt1);

        $decoded1 = JWT_Proxy::decode($jwt1, $this->publicKey, ['RS256']);
        $decoded2 = JWT::decode($jwt2, $this->publicKey, ['RS256']);
        $this->assertEquals($decoded2->baz, $decoded1->baz);

        $data = 'abc-xyz';
        $enc1 = JWT_Proxy::urlsafeB64Encode($data);
        $enc2 = JWT::urlsafeB64Encode($data);
        $this->assertEquals($enc2, $enc1);
        $dec1 = JWT_Proxy::urlsafeB64Decode($enc1);
        $dec2 = JWT::urlsafeB64Decode($enc2);
        $this->assertEquals($dec2, $dec1);
    }
}