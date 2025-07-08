<?php

use PHPUnit\Framework\TestCase;
use IMSGlobal\LTI\LTI_Deep_Link;
use IMSGlobal\LTI\LTI_Registration;
use IMSGlobal\LTI\LTI_Deep_Link_Resource;

class LTI_Deep_LinkTest extends TestCase
{
    private function getMockRegistration($privateKey, $kid, $clientId = 'client_id', $issuer = 'issuer.example.com')
    {
        $registration = $this->createMock(LTI_Registration::class);
        $registration->method('get_tool_private_key')->willReturn($privateKey);
        $registration->method('get_kid')->willReturn($kid);
        $registration->method('get_client_id')->willReturn($clientId);
        $registration->method('get_issuer')->willReturn($issuer);
        return $registration;
    }

    private function getMockResource($type = 'ltiResourceLink', $title = 'Test Resource', $url = 'https://example.com/resource')
    {
        $resource = $this->createMock(LTI_Deep_Link_Resource::class);
        $resource->method('to_array')->willReturn([
            'type' => $type,
            'title' => $title,
            'url' => $url,
            'presentation' => ['documentTarget' => 'iframe'],
            'custom' => [],
        ]);
        return $resource;
    }

    private function generateRsaPrivateKey(): string
    {
        $res = openssl_pkey_new([
            'private_key_bits' => 512,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($res, $privKey);
        return $privKey;
    }

    public function testGetResponseJwtReturnsValidJwt()
    {
        $privateKey = $this->generateRsaPrivateKey();
        $kid = 'test-key-id';
        $registration = $this->getMockRegistration($privateKey, $kid);
        $deepLinkSettings = [
            'deep_link_return_url' => 'https://platform.example.com/return',
            'data' => 'test-data',
        ];
        $resource = $this->getMockResource();
        $deepLink = new LTI_Deep_Link($registration, 'deployment-123', $deepLinkSettings);
        $jwt = $deepLink->get_response_jwt([$resource]);
        $this->assertIsString($jwt);
        // Decode JWT header and payload for structure (do not verify signature here)
        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts);
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        $this->assertEquals('client_id', $payload['iss']);
        $this->assertEquals(['issuer.example.com'], $payload['aud']);
        $this->assertEquals('deployment-123', $payload['https://purl.imsglobal.org/spec/lti/claim/deployment_id']);
        $this->assertEquals('LtiDeepLinkingResponse', $payload['https://purl.imsglobal.org/spec/lti/claim/message_type']);
        $this->assertEquals('1.3.0', $payload['https://purl.imsglobal.org/spec/lti/claim/version']);
        $this->assertEquals('test-data', $payload['https://purl.imsglobal.org/spec/lti-dl/claim/data']);
        $this->assertIsArray($payload['https://purl.imsglobal.org/spec/lti-dl/claim/content_items']);
        $this->assertEquals('Test Resource', $payload['https://purl.imsglobal.org/spec/lti-dl/claim/content_items'][0]['title']);
    }

    public function testOutputResponseFormPrintsFormWithJwt()
    {
        $privateKey = $this->generateRsaPrivateKey();
        $kid = 'test-key-id';
        $registration = $this->getMockRegistration($privateKey, $kid);
        $deepLinkSettings = [
            'deep_link_return_url' => 'https://platform.example.com/return',
            'data' => 'test-data',
        ];
        $resource = $this->getMockResource();
        $deepLink = new LTI_Deep_Link($registration, 'deployment-123', $deepLinkSettings);
        ob_start();
        $deepLink->output_response_form([$resource]);
        $output = ob_get_clean();
        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('action="https://platform.example.com/return"', $output);
        $this->assertStringContainsString('name="JWT"', $output);
        $this->assertStringContainsString('<script>', $output);
    }
}