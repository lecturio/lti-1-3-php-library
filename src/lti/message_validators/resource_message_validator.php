<?php
namespace IMSGlobal\LTI;

/**
 * Class Resource_Message_Validator
 * Message validator for LtiResourceLinkRequest messages.
 */
class Resource_Message_Validator implements Message_Validator
{
    /**
     * Determine if the validator can validate the given JWT body.
     *
     * @param array<string, mixed> $jwt_body JWT body to check
     * @return bool True if can validate, false otherwise
     */
    public function can_validate($jwt_body)
    {
        return $jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiResourceLinkRequest';
    }

    /**
     * Validate the JWT body for a LtiResourceLinkRequest message.
     *
     * @param array<string, mixed> $jwt_body JWT body to validate
     * @return bool True if valid
     * @throws LTI_Exception If validation fails
     */
    public function validate($jwt_body)
    {
        if (empty($jwt_body['sub'])) {
            throw new LTI_Exception('Must have a user (sub)');
        }
        if ($jwt_body['https://purl.imsglobal.org/spec/lti/claim/version'] !== '1.3.0') {
            throw new LTI_Exception('Incorrect version, expected 1.3.0');
        }
        if (!isset($jwt_body['https://purl.imsglobal.org/spec/lti/claim/roles'])) {
            throw new LTI_Exception('Missing Roles Claim');
        }
        if (empty($jwt_body['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id'])) {
            throw new LTI_Exception('Missing Resource Link Id');
        }

        return true;
    }
}
?>