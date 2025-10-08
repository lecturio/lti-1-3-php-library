<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Registration
 * Handles LTI registration details and configuration.
 * @package IMSGlobal\LTI
 */
class LTI_Registration
{

    /**
     * @var string|null Issuer identifier
     */
    private $issuer;
    /**
     * @var string|null Client ID
     */
    private $client_id;
    /**
     * @var string|null JWKS key set URL
     */
    private $key_set_url;
    /**
     * @var string|null Auth token URL
     */
    private $auth_token_url;
    /**
     * @var string|null Auth login URL
     */
    private $auth_login_url;
    /**
     * @var string|null Auth server URL
     */
    private $auth_server;
    /**
     * @var string|null Tool private key
     */
    private $tool_private_key;
    /**
     * @var string|null Key ID (kid)
     */
    private $kid;

    /**
     * Create a new LTI_Registration instance.
     * @return LTI_Registration
     */
    public static function new()
    {
        return new LTI_Registration();
    }

    /**
     * Get the issuer identifier.
     * @return string|null
     */
    public function get_issuer()
    {
        return $this->issuer;
    }

    /**
     * Set the issuer identifier.
     * @param string $issuer
     * @return $this
     */
    public function set_issuer($issuer)
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * Get the client ID.
     * @return string|null
     */
    public function get_client_id()
    {
        return $this->client_id;
    }

    /**
     * Set the client ID.
     * @param string $client_id
     * @return $this
     */
    public function set_client_id($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    /**
     * Get the JWKS key set URL.
     * @return string|null
     */
    public function get_key_set_url()
    {
        return $this->key_set_url;
    }

    /**
     * Set the JWKS key set URL.
     * @param string $key_set_url
     * @return $this
     */
    public function set_key_set_url($key_set_url)
    {
        $this->key_set_url = $key_set_url;
        return $this;
    }

    /**
     * Get the auth token URL.
     * @return string|null
     */
    public function get_auth_token_url()
    {
        return $this->auth_token_url;
    }

    /**
     * Set the auth token URL.
     * @param string $auth_token_url
     * @return $this
     */
    public function set_auth_token_url($auth_token_url)
    {
        $this->auth_token_url = $auth_token_url;
        return $this;
    }

    /**
     * Get the auth login URL.
     * @return string|null
     */
    public function get_auth_login_url()
    {
        return $this->auth_login_url;
    }

    /**
     * Set the auth login URL.
     * @param string $auth_login_url
     * @return $this
     */
    public function set_auth_login_url($auth_login_url)
    {
        $this->auth_login_url = $auth_login_url;
        return $this;
    }

    /**
     * Get the auth server URL. Defaults to auth_token_url if not set.
     * @return string|null
     */
    public function get_auth_server()
    {
        return empty($this->auth_server) ? $this->auth_token_url : $this->auth_server;
    }

    /**
     * Set the auth server URL.
     * @param string $auth_server
     * @return $this
     */
    public function set_auth_server($auth_server)
    {
        $this->auth_server = $auth_server;
        return $this;
    }

    /**
     * Get the tool private key.
     * @return string|null
     */
    public function get_tool_private_key()
    {
        return $this->tool_private_key;
    }

    /**
     * Set the tool private key.
     * @param string $tool_private_key
     * @return $this
     */
    public function set_tool_private_key($tool_private_key)
    {
        $this->tool_private_key = $tool_private_key;
        return $this;
    }

    /**
     * Get the key ID (kid). Defaults to a hash of issuer and client_id if not set.
     * @return string
     */
    public function get_kid()
    {
        return empty($this->kid) ? hash('sha256', trim($this->issuer . $this->client_id)) : $this->kid;
    }

    /**
     * Set the key ID (kid).
     * @param string $kid
     * @return $this
     */
    public function set_kid($kid)
    {
        $this->kid = $kid;
        return $this;
    }

}

?>