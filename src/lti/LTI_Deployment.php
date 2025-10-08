<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Deployment
 * Handles LTI deployment identification.
 * @package IMSGlobal\LTI
 */
class LTI_Deployment
{
    /**
     * @var string|null Deployment ID
     */
    private $deployment_id;

    /**
     * Create a new LTI_Deployment instance.
     * @return LTI_Deployment
     */
    public static function new()
    {
        return new LTI_Deployment();
    }

    /**
     * Get the deployment ID.
     * @return string|null
     */
    public function get_deployment_id()
    {
        return $this->deployment_id;
    }

    /**
     * Set the deployment ID.
     * @param string $deployment_id
     * @return $this
     */
    public function set_deployment_id($deployment_id)
    {
        $this->deployment_id = $deployment_id;
        return $this;
    }

}

?>