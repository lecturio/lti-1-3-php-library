<?php
namespace IMSGlobal\LTI;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

JWT::$leeway = 5;

/**
 * Class LTI_Message_Launch
 * Handles LTI 1.3 message launch validation and service access.
 * @package IMSGlobal\LTI
 */
class LTI_Message_Launch
{
    /**
     * @var Database Database interface for registrations and deployments.
     */
    private $db;
    /**
     * @var Cache Cache interface for launch/session data.
     */
    private $cache;
    /**
     * @var array Request parameters (usually POST).
     */
    private $request;
    /**
     * @var Cookie Cookie interface for state/nonce.
     */
    private $cookie;
    /**
     * @var array JWT header/body for the launch.
     * @phpstan-var array{header?: array, body?: array}
     */
    private $jwt;
    /**
     * @var object Registration object for the launch.
     */
    private $registration;
    /**
     * @var string Unique launch identifier.
     */
    private $launch_id;

    /**
     * Constructor
     * @param Database $database Database interface
     * @param Cache|null $cache Cache interface (optional)
     * @param Cookie|null $cookie Cookie interface (optional)
     */
    function __construct(Database $database, Cache $cache = null, Cookie $cookie = null)
    {
        $this->db = $database;
        $this->launch_id = uniqid("lti1p3_launch_", true);
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
     * Static constructor for chaining.
     * @param Database $database
     * @param Cache|null $cache
     * @param Cookie|null $cookie
     * @return LTI_Message_Launch
     */
    public static function new(Database $database, Cache $cache = null, Cookie $cookie = null)
    {
        return new LTI_Message_Launch($database, $cache, $cookie);
    }

    /**
     * Load an LTI_Message_Launch from cache by launch id.
     * @param string $launch_id
     * @param Database $database
     * @param Cache|null $cache
     * @throws LTI_Exception
     * @return LTI_Message_Launch
     */
    public static function from_cache($launch_id, Database $database, Cache $cache = null)
    {
        $new = new LTI_Message_Launch($database, $cache, null);
        $new->launch_id = $launch_id;
        $new->jwt = ['body' => $new->cache->get_launch_data($launch_id)];
        return $new->validate_registration();
    }

    /**
     * Validates all aspects of an incoming LTI message launch and caches the launch if successful.
     * @param array|null $request POST parameters (default: $_POST)
     * @throws LTI_Exception
     * @return LTI_Message_Launch
     */
    public function validate(array $request = null)
    {
        if ($request === null) {
            $request = $_POST;
        }
        $this->request = $request;
        return $this->validate_state()
            ->validate_jwt_format()
            ->validate_nonce()
            ->validate_registration()
            ->validate_jwt_signature()
            ->validate_deployment()
            ->validate_message()
            ->cache_launch_data();
    }

    /**
     * Returns whether the current launch can use the names and roles service.
     * @return bool
     */
    public function has_nrps()
    {
        return !empty($this->jwt['body']['https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice']['context_memberships_url']);
    }

    /**
     * Get the names and roles service for the current launch.
     * @return LTI_Names_Roles_Provisioning_Service
     */
    public function get_nrps()
    {
        return new LTI_Names_Roles_Provisioning_Service(
            new LTI_Service_Connector($this->registration),
            $this->jwt['body']['https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice']
        );
    }

    /**
     * Returns whether the current launch can use the groups service.
     * @return bool
     */
    public function has_gs()
    {
        return !empty($this->jwt['body']['https://purl.imsglobal.org/spec/lti-gs/claim/groupsservice']['context_groups_url']);
    }

    /**
     * Get the groups service for the current launch.
     * @return LTI_Course_Groups_Service
     */
    public function get_gs()
    {
        return new LTI_Course_Groups_Service(
            new LTI_Service_Connector($this->registration),
            $this->jwt['body']['https://purl.imsglobal.org/spec/lti-gs/claim/groupsservice']
        );
    }

    /**
     * Returns whether the current launch can use the assignments and grades service.
     * @return bool
     */
    public function has_ags()
    {
        return !empty($this->jwt['body']['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']);
    }

    /**
     * Get the assignments and grades service for the current launch.
     * @return LTI_Assignments_Grades_Service
     */
    public function get_ags()
    {
        return new LTI_Assignments_Grades_Service(
            new LTI_Service_Connector($this->registration),
            $this->jwt['body']['https://purl.imsglobal.org/spec/lti-ags/claim/endpoint']
        );
    }

    /**
     * Get a deep link for constructing a deep linking response.
     * @return LTI_Deep_Link
     */
    public function get_deep_link()
    {
        return new LTI_Deep_Link(
            $this->registration,
            $this->jwt['body']['https://purl.imsglobal.org/spec/lti/claim/deployment_id'],
            $this->jwt['body']['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings']
        );
    }

    /**
     * Returns true if the current launch is a deep linking launch.
     * @return bool
     */
    public function is_deep_link_launch()
    {
        return $this->jwt['body']['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiDeepLinkingRequest';
    }

    /**
     * Returns true if the current launch is a submission review launch.
     * @return bool
     */
    public function is_submission_review_launch()
    {
        return $this->jwt['body']['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiSubmissionReviewRequest';
    }

    /**
     * Returns true if the current launch is a resource launch.
     * @return bool
     */
    public function is_resource_launch()
    {
        return $this->jwt['body']['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiResourceLinkRequest';
    }

    /**
     * Get the decoded JWT body for the launch.
     * @return array Decoded JWT body.
     */
    public function get_launch_data()
    {
        return $this->jwt['body'];
    }

    /**
     * Get the unique launch id for the current launch.
     * @return string
     */
    public function get_launch_id()
    {
        return $this->launch_id;
    }

    /**
     * Get the public key for JWT signature validation.
     * @throws LTI_Exception
     * @return array{key: resource|string, ...}|
     *         false
     */
    private function get_public_key()
    {
        $key_set_url = $this->registration->get_key_set_url();
        $public_key_set = json_decode(file_get_contents($key_set_url), true);
        if (empty($public_key_set)) {
            throw new LTI_Exception("Failed to fetch public key", 1);
        }
        foreach ($public_key_set['keys'] as $index => $key) {
            if ($key['kid'] != $this->jwt['header']['kid']) {
                unset($public_key_set['keys'][$index]);
            }
        }
        $keys = JWK::parseKeySet($public_key_set);
        foreach ($keys as $kid => $key) {
            if ($kid == $this->jwt['header']['kid']) {
                try {
                    return openssl_pkey_get_details($key);
                } catch (\Exception $e) {
                    return false;
                }
            }
        }
        throw new LTI_Exception("Unable to find public key", 1);
    }

    /**
     * Cache the launch data.
     * @return $this
     */
    private function cache_launch_data()
    {
        $this->cache->cache_launch_data($this->launch_id, $this->jwt['body']);
        return $this;
    }

    /**
     * Validate OIDC state.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_state()
    {
        if ($this->cookie->get_cookie('lti1p3_' . $this->request['state']) !== $this->request['state']) {
            throw new LTI_Exception("State not found", 1);
        }
        return $this;
    }

    /**
     * Validate JWT format and decode header/body.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_jwt_format()
    {
        $jwt = $this->request['id_token'];
        if (empty($jwt)) {
            throw new LTI_Exception("Missing id_token", 1);
        }
        $jwt_parts = explode('.', $jwt);
        if (count($jwt_parts) !== 3) {
            throw new LTI_Exception("Invalid id_token, JWT must contain 3 parts", 1);
        }
        $this->jwt['header'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[0]), true);
        $this->jwt['body'] = json_decode(JWT::urlsafeB64Decode($jwt_parts[1]), true);
        return $this;
    }

    /**
     * Validate nonce for replay protection.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_nonce()
    {
        if (!$this->cache->check_nonce($this->jwt['body']['nonce'])) {
            throw new LTI_Exception("Invalid Nonce");
        }
        return $this;
    }

    /**
     * Validate registration by issuer and client id.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_registration()
    {
        $this->registration = $this->db->find_registration_by_issuer($this->jwt['body']['iss']);
        if (empty($this->registration)) {
            throw new LTI_Exception("Registration not found.", 1);
        }
        $client_id = is_array($this->jwt['body']['aud']) ? $this->jwt['body']['aud'][0] : $this->jwt['body']['aud'];
        if ($client_id !== $this->registration->get_client_id()) {
            throw new LTI_Exception("Client id not registered for this issuer", 1);
        }
        return $this;
    }

    /**
     * Validate JWT signature using public key.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_jwt_signature()
    {
        $public_key = $this->get_public_key();
        try {
            JWT::decode($this->request['id_token'], $public_key['key'], array('RS256'));
        } catch (\Exception $e) {
            throw new LTI_Exception("Invalid signature on id_token", 1);
        }
        return $this;
    }

    /**
     * Validate deployment for the launch.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_deployment()
    {
        $deployment = $this->db->find_deployment($this->jwt['body']['iss'], $this->jwt['body']['https://purl.imsglobal.org/spec/lti/claim/deployment_id']);
        if (empty($deployment)) {
            throw new LTI_Exception("Unable to find deployment", 1);
        }
        return $this;
    }

    /**
     * Validate message type and run message validator.
     * @throws LTI_Exception
     * @return $this
     */
    private function validate_message()
    {
        if (empty($this->jwt['body']['https://purl.imsglobal.org/spec/lti/claim/message_type'])) {
            throw new LTI_Exception("Invalid message type", 1);
        }
        foreach (glob(__DIR__ . "/message_validators/*.php") as $filename) {
            include_once $filename;
        }
        $classes = get_declared_classes();
        $validators = array();
        foreach ($classes as $class_name) {
            $reflect = new \ReflectionClass($class_name);
            if ($reflect->implementsInterface('\\IMSGlobal\\LTI\\Message_Validator')) {
                $validators[] = new $class_name();
            }
        }
        $message_validator = false;
        foreach ($validators as $validator) {
            if ($validator->can_validate($this->jwt['body'])) {
                if ($message_validator !== false) {
                    throw new LTI_Exception("Validator conflict", 1);
                }
                $message_validator = $validator;
            }
        }
        if ($message_validator === false) {
            throw new LTI_Exception("Unrecognized message type.", 1);
        }
        if (!$message_validator->validate($this->jwt['body'])) {
            throw new LTI_Exception("Message validation failed.", 1);
        }
        return $this;
    }
}
?>