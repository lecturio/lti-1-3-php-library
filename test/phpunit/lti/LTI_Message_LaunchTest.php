<?php

use PHPUnit\Framework\TestCase;
use IMSGlobal\LTI\LTI_Message_Launch;
use IMSGlobal\LTI\Database;
use IMSGlobal\LTI\Cache;
use IMSGlobal\LTI\Cookie;
use IMSGlobal\LTI\LTI_Registration;
use IMSGlobal\LTI\LTI_Exception;

class LTI_Message_LaunchTest extends TestCase
{
    /**
     * @return Database|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockDatabase()
    {
        return $this->createMock(Database::class);
    }

    /**
     * @return Cache|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockCache()
    {
        $cache = $this->createMock(Cache::class);
        $cache->method('check_nonce')->willReturn(true);
        $cache->method('get_launch_data')->willReturn([
            'iss' => 'issuer.example.com',
            'aud' => 'client_id',
            'nonce' => 'nonce',
            'https://purl.imsglobal.org/spec/lti/claim/message_type' => 'LtiResourceLinkRequest',
            'https://purl.imsglobal.org/spec/lti/claim/deployment_id' => 'deployment-123',
        ]);
        return $cache;
    }

    /**
     * @return Cookie|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockCookie()
    {
        $cookie = $this->createMock(Cookie::class);
        $cookie->method('get_cookie')->willReturn('state123');
        return $cookie;
    }

    /**
     * @return LTI_Registration|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockRegistration()
    {
        $registration = $this->createMock(LTI_Registration::class);
        $registration->method('get_client_id')->willReturn('client_id');
        $registration->method('get_key_set_url')->willReturn('https://example.com/jwks');
        return $registration;
    }

    public function testCanInstantiate()
    {
        $db = $this->getMockDatabase();
        $cache = $this->getMockCache();
        $cookie = $this->getMockCookie();
        $launch = new LTI_Message_Launch($db, $cache, $cookie);
        $this->assertInstanceOf(LTI_Message_Launch::class, $launch);
    }

    public function testFromCacheReturnsInstance()
    {
        $db = $this->getMockDatabase();
        $cache = $this->getMockCache();
        $db->method('find_registration_by_issuer')->willReturn($this->getMockRegistration());
        $launch = LTI_Message_Launch::from_cache('launchid123', $db, $cache);
        $this->assertInstanceOf(LTI_Message_Launch::class, $launch);
    }

    public function testGetLaunchIdReturnsString()
    {
        $db = $this->getMockDatabase();
        $cache = $this->getMockCache();
        $cookie = $this->getMockCookie();
        $launch = new LTI_Message_Launch($db, $cache, $cookie);
        $this->assertIsString($launch->get_launch_id());
    }

    public function testHasAgsReturnsFalseIfNoClaim()
    {
        $db = $this->getMockDatabase();
        $cache = $this->getMockCache();
        $cookie = $this->getMockCookie();
        $launch = new LTI_Message_Launch($db, $cache, $cookie);
        $reflection = new \ReflectionClass($launch);
        $prop = $reflection->getProperty('jwt');
        $prop->setAccessible(true);
        $prop->setValue($launch, ['body' => []]);
        $this->assertFalse($launch->has_ags());
    }

    public function testIsResourceLaunchReturnsTrue()
    {
        $db = $this->getMockDatabase();
        $cache = $this->getMockCache();
        $cookie = $this->getMockCookie();
        $launch = new LTI_Message_Launch($db, $cache, $cookie);
        $reflection = new \ReflectionClass($launch);
        $prop = $reflection->getProperty('jwt');
        $prop->setAccessible(true);
        $prop->setValue($launch, [
            'body' => [
                'https://purl.imsglobal.org/spec/lti/claim/message_type' => 'LtiResourceLinkRequest',
            ]
        ]);
        $this->assertTrue($launch->is_resource_launch());
    }

    public function testValidateJwtFormatThrowsOnInvalidToken()
    {
        $db = $this->getMockDatabase();
        $cache = $this->getMockCache();
        $cookie = $this->getMockCookie();
        $launch = new LTI_Message_Launch($db, $cache, $cookie);

        // Set an invalid JWT (not 3 parts)
        $reflection = new \ReflectionClass($launch);
        $requestProp = $reflection->getProperty('request');
        $requestProp->setAccessible(true);
        $requestProp->setValue($launch, ['id_token' => 'invalid.jwt']);

        $method = $reflection->getMethod('validate_jwt_format');
        $method->setAccessible(true);

        $this->expectException(LTI_Exception::class);
        $method->invoke($launch);
    }
}