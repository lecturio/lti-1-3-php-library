<?php
namespace IMSGlobal\LTI;

/**
 * Handles OIDC login flow for LTI.
 * @package IMSGlobal\LTI
 */
class LTI_OIDC_Login
{
    /**
     * @var Database
     */
    private $db;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * Constructor.
     * @param Database $database
     * @param Cache|null $cache
     * @param Cookie|null $cookie
     */
    function __construct(Database $database, Cache $cache = null, Cookie $cookie = null)
    {
        $this->db = $database;
        if ($cache === null) {
            $cache = new Cache();
        }
        $this->cache = $cache;
        if ($cookie === null) {
            $cookie = new Cookie();
        }
        $this->cookie = $cookie;
    }

    /**
     * Create new instance (fluent).
     * @param Database $database
     * @param Cache|null $cache
     * @param Cookie|null $cookie
     * @return LTI_OIDC_Login
     */
    public static function new(Database $database, Cache $cache = null, Cookie $cookie = null)
    {
        return new LTI_OIDC_Login($database, $cache, $cookie);
    }

    /**
     * Calculate OIDC login redirect location.
     * @param string $launch_url
     * @param array|null $request
     * @return Redirect
     */
    public function do_oidc_login_redirect($launch_url, array $request = null)
    {
        if ($request === null) {
            $request = $_REQUEST;
        }
        if (empty($launch_url)) {
            throw new OIDC_Exception("No launch URL configured", 1);
        }
        $registration = $this->validate_oidc_login($request);
        $state = str_replace('.', '_', uniqid('state-', true));
        $this->cookie->set_cookie("lti1p3_$state", $state, 60);
        $nonce = uniqid('nonce-', true);
        $this->cache->cache_nonce($nonce);
        $auth_params = [
            'scope' => 'openid',
            'response_type' => 'id_token',
            'response_mode' => 'form_post',
            'prompt' => 'none',
            'client_id' => $registration->get_client_id(),
            'redirect_uri' => $launch_url,
            'state' => $state,
            'nonce' => $nonce,
            'login_hint' => $request['login_hint']
        ];
        if (isset($request['lti_message_hint'])) {
            $auth_params['lti_message_hint'] = $request['lti_message_hint'];
        }
        $auth_login_return_url = $registration->get_auth_login_url() . "?" . http_build_query($auth_params);
        return new Redirect($auth_login_return_url, http_build_query($request));
    }

    /**
     * Validate OIDC login request.
     * @param array $request
     * @return mixed
     */
    protected function validate_oidc_login($request)
    {
        if (empty($request['iss'])) {
            throw new OIDC_Exception("Could not find issuer", 1);
        }
        if (empty($request['login_hint'])) {
            throw new OIDC_Exception("Could not find login hint", 1);
        }
        $registration = $this->db->find_registration_by_issuer($request['iss']);
        if (empty($registration)) {
            throw new OIDC_Exception("Could not find registration details", 1);
        }
        return $registration;
    }
}