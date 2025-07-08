<?php

use PHPUnit\Framework\TestCase;
use IMSGlobal\LTI\LTI_Service_Connector;
use IMSGlobal\LTI\LTI_Registration;

class LTI_Service_ConnectorTest extends TestCase
{
    /**
     * @return LTI_Registration|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getMockRegistration()
    {
        $registration = $this->createMock(LTI_Registration::class);
        $registration->method('get_client_id')->willReturn('client_id');
        $registration->method('get_auth_server')->willReturn('https://auth.example.com');
        $registration->method('get_tool_private_key')->willReturn('test_private_key');
        $registration->method('get_kid')->willReturn('test_kid');
        $registration->method('get_auth_token_url')->willReturn('https://auth.example.com/token');
        return $registration;
    }

    public function testCanInstantiate()
    {
        $registration = $this->getMockRegistration();
        $connector = new LTI_Service_Connector($registration);
        $this->assertInstanceOf(LTI_Service_Connector::class, $connector);
    }

    public function testGetAccessTokenCachesToken()
    {
        $registration = $this->getMockRegistration();
        $connector = $this->getMockBuilder(LTI_Service_Connector::class)
            ->setConstructorArgs([$registration])
            ->onlyMethods(['make_service_request'])
            ->getMock();

        // NOTE: Mocking static methods like JWT::encode requires Patchwork or uopz, which is not natively supported by PHPUnit.
        // For now, just test that the method returns a string (integration test would require more setup)
        $this->markTestIncomplete('Integration test for get_access_token requires curl and JWT::encode mocking.');
    }

    public function testMakeServiceRequestReturnsArray()
    {
        $registration = $this->getMockRegistration();
        $connector = $this->getMockBuilder(LTI_Service_Connector::class)
            ->setConstructorArgs([$registration])
            ->onlyMethods(['get_access_token'])
            ->getMock();
        $connector->method('get_access_token')->willReturn('test_access_token');

        // Patch curl_exec and related functions if possible
        $this->markTestIncomplete('Integration test for make_service_request requires curl mocking.');
    }
}