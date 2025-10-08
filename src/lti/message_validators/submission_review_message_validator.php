<?php
namespace IMSGlobal\LTI;

/**
 * Class Submission_Review_Message_Validator
 * Message validator for LtiSubmissionReviewRequest messages.
 * @package IMSGlobal\LTI
 */
class Submission_Review_Message_Validator implements Message_Validator
{
    /**
     * Check if the validator can validate the given JWT body.
     *
     * @param array<string, mixed> $jwt_body
     * @return bool
     */
    public function can_validate($jwt_body)
    {
        return $jwt_body['https://purl.imsglobal.org/spec/lti/claim/message_type'] === 'LtiSubmissionReviewRequest';
    }

    /**
     * Validate the JWT body for LtiSubmissionReviewRequest.
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
        if (empty($jwt_body['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id'])) {
            throw new LTI_Exception('Missing Resource Link Id');
        }
        if (empty($jwt_body['https://purl.imsglobal.org/spec/lti/claim/for_user'])) {
            throw new LTI_Exception('Missing For User');
        }

        return true;
    }
}
?>