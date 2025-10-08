<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Deep_Link_Resource
 * Represents a resource for LTI Deep Linking.
 * @package IMSGlobal\LTI
 */
class LTI_Deep_Link_Resource
{
    /**
     * @var string Resource type
     */
    private $type = 'ltiResourceLink';
    /**
     * @var string|null Resource title
     */
    private $title;
    /**
     * @var string|null Resource URL
     */
    private $url;
    /**
     * @var LTI_Lineitem|null Associated line item
     */
    private $lineitem;
    /**
     * @var array<string, mixed> Custom parameters
     */
    private $custom_params = [];
    /**
     * @var string Presentation target
     */
    private $target = 'iframe';

    /**
     * Create a new LTI_Deep_Link_Resource instance.
     * @return LTI_Deep_Link_Resource
     */
    public static function new()
    {
        return new LTI_Deep_Link_Resource();
    }

    /**
     * Get resource type.
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Set resource type.
     * @param string $value
     * @return $this
     */
    public function set_type($value)
    {
        $this->type = $value;
        return $this;
    }

    /**
     * Get resource title.
     * @return string|null
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Set resource title.
     * @param string $value
     * @return $this
     */
    public function set_title($value)
    {
        $this->title = $value;
        return $this;
    }

    /**
     * Get resource URL.
     * @return string|null
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Set resource URL.
     * @param string $value
     * @return $this
     */
    public function set_url($value)
    {
        $this->url = $value;
        return $this;
    }

    /**
     * Get associated line item.
     * @return LTI_Lineitem|null
     */
    public function get_lineitem()
    {
        return $this->lineitem;
    }

    /**
     * Set associated line item.
     * @param LTI_Lineitem $value
     * @return $this
     */
    public function set_lineitem($value)
    {
        $this->lineitem = $value;
        return $this;
    }

    /**
     * Get custom parameters.
     * @return array<string, mixed>
     */
    public function get_custom_params()
    {
        return $this->custom_params;
    }

    /**
     * Set custom parameters.
     * @param array<string, mixed> $value
     * @return $this
     */
    public function set_custom_params($value)
    {
        $this->custom_params = $value;
        return $this;
    }

    /**
     * Get presentation target.
     * @return string
     */
    public function get_target()
    {
        return $this->target;
    }

    /**
     * Set presentation target.
     * @param string $value
     * @return $this
     */
    public function set_target($value)
    {
        $this->target = $value;
        return $this;
    }

    /**
     * Convert resource to array for LTI Deep Linking response.
     * @return array<string, mixed>
     */
    public function to_array()
    {
        $resource = [
            "type" => $this->type,
            "title" => $this->title,
            "url" => $this->url,
            "presentation" => [
                "documentTarget" => $this->target,
            ],
            "custom" => $this->custom_params,
        ];
        if ($this->lineitem !== null) {
            $resource["lineItem"] = [
                "scoreMaximum" => $this->lineitem->get_score_maximum(),
                "label" => $this->lineitem->get_label(),
            ];
        }
        return $resource;
    }
}
?>