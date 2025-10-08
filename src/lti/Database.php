<?php
namespace IMSGlobal\LTI;

/**
 * Interface for LTI database access.
 * @package IMSGlobal\LTI
 */
interface Database
{
    /**
     * Find registration by issuer.
     * @param string $iss
     * @return mixed
     */
    public function find_registration_by_issuer($iss);
    /**
     * Find deployment by issuer and deployment id.
     * @param string $iss
     * @param string $deployment_id
     * @return mixed
     */
    public function find_deployment($iss, $deployment_id);
}

?>