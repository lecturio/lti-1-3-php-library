<?php
namespace IMSGlobal\LTI;

/**
 * Interface for LTI message validators.
 * @package IMSGlobal\LTI
 */
interface Message_Validator
{
    /**
     * Validate the JWT body for this message type.
     * @param array $jwt_body
     * @return bool
     */
    public function validate($jwt_body);
    /**
     * Check if this validator can handle the JWT body.
     * @param array $jwt_body
     * @return bool
     */
    public function can_validate($jwt_body);
}
?>