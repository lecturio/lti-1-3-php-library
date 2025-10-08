<?php

use PHPUnit\Framework\TestCase;
use IMSGlobal\LTI\JWKS_Endpoint;
use IMSGlobal\LTI\LTI_Registration;
use IMSGlobal\LTI\Database;

class JWKS_EndpointTest extends TestCase
{
    private function generateRsaPrivateKey(): string
    {
        // Generate a 512-bit RSA key for testing (insecure, but fine for unit tests)
        $res = openssl_pkey_new([
            'private_key_bits' => 512,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($res, $privKey);
        return $privKey;
    }

    public function testGetPublicJwksReturnsValidJwks()
    {
        $privateKey = $this->generateRsaPrivateKey();
        $kid = 'test-key-id';
        $endpoint = new JWKS_Endpoint([$kid => $privateKey]);
        $jwks = $endpoint->get_public_jwks();
        $this->assertArrayHasKey('keys', $jwks);
        $this->assertIsArray($jwks['keys']);
        $this->assertNotEmpty($jwks['keys']);
        $key = $jwks['keys'][0];
        $this->assertEquals('RSA', $key['kty']);
        $this->assertEquals('RS256', $key['alg']);
        $this->assertEquals('sig', $key['use']);
        $this->assertEquals($kid, $key['kid']);
        $this->assertArrayHasKey('e', $key);
        $this->assertArrayHasKey('n', $key);
    }

    public function testFromRegistrationCreatesEndpoint()
    {
        $privateKey = $this->generateRsaPrivateKey();
        $kid = 'reg-key-id';
        /** @var LTI_Registration&\PHPUnit\Framework\MockObject\MockObject $registration */
        $registration = $this->createMock(LTI_Registration::class);
        $registration->method('get_kid')->willReturn($kid);
        $registration->method('get_tool_private_key')->willReturn($privateKey);
        $endpoint = JWKS_Endpoint::from_registration($registration);
        $jwks = $endpoint->get_public_jwks();
        $this->assertEquals($kid, $jwks['keys'][0]['kid']);
    }

    public function testFromIssuerCreatesEndpoint()
    {
        $privateKey = $this->generateRsaPrivateKey();
        $kid = 'issuer-key-id';
        $issuer = 'issuer.example.com';
        /** @var LTI_Registration&\PHPUnit\Framework\MockObject\MockObject $registration */
        $registration = $this->createMock(LTI_Registration::class);
        $registration->method('get_kid')->willReturn($kid);
        $registration->method('get_tool_private_key')->willReturn($privateKey);
        /** @var Database&\PHPUnit\Framework\MockObject\MockObject $database */
        $database = $this->createMock(Database::class);
        $database->method('find_registration_by_issuer')->with($issuer)->willReturn($registration);
        $endpoint = JWKS_Endpoint::from_issuer($database, $issuer);
        $jwks = $endpoint->get_public_jwks();
        $this->assertEquals($kid, $jwks['keys'][0]['kid']);
    }
}