<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Grade_Submission_Review
 * Handles reviewable status, label, URL, and custom data for grade submission review.
 * @package IMSGlobal\LTI
 */
class LTI_Grade_Submission_Review
{
    /**
     * @var string|null Reviewable status value
     */
    private $reviewable_status;
    /**
     * @var string|null Label for the review
     */
    private $label;
    /**
     * @var string|null URL for the review
     */
    private $url;
    /**
     * @var mixed Custom data for the review
     */
    private $custom;

    /**
     * Create a new instance for method chaining.
     * @return LTI_Grade_Submission_Review
     */
    public static function new()
    {
        return new LTI_Grade_Submission_Review();
    }

    /**
     * Get the reviewable status.
     * @return string|null
     */
    public function get_reviewable_status()
    {
        return $this->reviewable_status;
    }

    /**
     * Set the reviewable status.
     * @param string|null $value
     * @return $this
     */
    public function set_reviewable_status($value)
    {
        $this->reviewable_status = $value;
        return $this;
    }

    /**
     * Get the label.
     * @return string|null
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     * Set the label.
     * @param string|null $value
     * @return $this
     */
    public function set_label($value)
    {
        $this->label = $value;
        return $this;
    }

    /**
     * Get the URL.
     * @return string|null
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Set the URL.
     * @param string|null $url
     * @return $this
     */
    public function set_url($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the custom data.
     * @return mixed
     */
    public function get_custom()
    {
        return $this->custom;
    }

    /**
     * Set the custom data.
     * @param mixed $value
     * @return $this
     */
    public function set_custom($value)
    {
        $this->custom = $value;
        return $this;
    }

    /**
     * Convert the object to a JSON string.
     * @return string
     */
    public function __toString()
    {
        return json_encode(array_filter([
            "reviewableStatus" => $this->reviewable_status,
            "label" => $this->label,
            "url" => $this->url,
            "custom" => $this->custom,
        ]));
    }
}
?>