<?php
namespace IMSGlobal\LTI;

/**
 * Class Deep_Link_Message_Validator
 * Message validator for LTI Deep Linking requests.
 * @package IMSGlobal\LTI
 */
class Deep_Link_Message_Validator implements Message_Validator
{
    /**
     * Check if the validator can validate the given JWT body.
     *
     * @param array<string, mixed> $jwt_body
     * @return bool
     */
    public function can_validate($jwt_body)
    {
        return $jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiDeepLinkingRequest';
    }

    /**
     * Validate the Deep Linking request JWT body.
     *
     * @param array<string, mixed> $jwt_body
     * @return bool
     * @throws LTI_Exception
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
        if (empty($jwt_body['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings'])) {
            throw new LTI_Exception('Missing Deep Linking Settings');
        }
        $deep_link_settings = $jwt_body['https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings'];
        if (empty($deep_link_settings['deep_link_return_url'])) {
            throw new LTI_Exception('Missing Deep Linking Return URL');
        }
        if (empty($deep_link_settings['accept_types']) || !in_array('ltiResourceLink', $deep_link_settings['accept_types'])) {
            throw new LTI_Exception('Must support resource link placement types');
        }
        if (empty($deep_link_settings['accept_presentation_document_targets'])) {
            throw new LTI_Exception('Must support a presentation type');
        }

        return true;
    }
}
?>