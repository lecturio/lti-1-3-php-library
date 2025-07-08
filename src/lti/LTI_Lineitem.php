<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Lineitem
 * Represents a line item for LTI assignments and grade services.
 */
class LTI_Lineitem
{
    /**
     * @var string|null Line item ID
     */
    private $id;
    /**
     * @var float|int|null Maximum score for the line item
     */
    private $score_maximum;
    /**
     * @var string|null Label for the line item
     */
    private $label;
    /**
     * @var string|null Resource ID associated with the line item
     */
    private $resource_id;
    /**
     * @var string|null Tag for the line item
     */
    private $tag;
    /**
     * @var string|null Start date/time (ISO 8601)
     */
    private $start_date_time;
    /**
     * @var string|null End date/time (ISO 8601)
     */
    private $end_date_time;

    /**
     * LTI_Lineitem constructor.
     *
     * @param array<string, mixed>|null $lineitem Optional associative array to initialize the line item.
     */
    public function __construct(array $lineitem = null)
    {
        if (empty($lineitem)) {
            return;
        }
        $this->id = $lineitem["id"];
        $this->score_maximum = $lineitem["scoreMaximum"];
        $this->label = $lineitem["label"];
        $this->resource_id = $lineitem["resourceId"];
        $this->tag = $lineitem["tag"];
        $this->start_date_time = $lineitem["startDateTime"];
        $this->end_date_time = $lineitem["endDateTime"];
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     *
     * @return LTI_Lineitem
     */
    public static function new()
    {
        return new LTI_Lineitem();
    }

    /**
     * Get the line item ID.
     *
     * @return string|null
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the line item ID.
     *
     * @param string|null $value
     * @return $this
     */
    public function set_id($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get the label for the line item.
     *
     * @return string|null
     */
    public function get_label()
    {
        return $this->label;
    }

    /**
     * Set the label for the line item.
     *
     * @param string|null $value
     * @return $this
     */
    public function set_label($value)
    {
        $this->label = $value;
        return $this;
    }

    /**
     * Get the maximum score for the line item.
     *
     * @return float|int|null
     */
    public function get_score_maximum()
    {
        return $this->score_maximum;
    }

    /**
     * Set the maximum score for the line item.
     *
     * @param float|int|null $value
     * @return $this
     */
    public function set_score_maximum($value)
    {
        $this->score_maximum = $value;
        return $this;
    }

    /**
     * Get the resource ID associated with the line item.
     *
     * @return string|null
     */
    public function get_resource_id()
    {
        return $this->resource_id;
    }

    /**
     * Set the resource ID associated with the line item.
     *
     * @param string|null $value
     * @return $this
     */
    public function set_resource_id($value)
    {
        $this->resource_id = $value;
        return $this;
    }

    /**
     * Get the tag for the line item.
     *
     * @return string|null
     */
    public function get_tag()
    {
        return $this->tag;
    }

    /**
     * Set the tag for the line item.
     *
     * @param string|null $value
     * @return $this
     */
    public function set_tag($value)
    {
        $this->tag = $value;
        return $this;
    }

    /**
     * Get the start date/time (ISO 8601).
     *
     * @return string|null
     */
    public function get_start_date_time()
    {
        return $this->start_date_time;
    }

    /**
     * Set the start date/time (ISO 8601).
     *
     * @param string|null $value
     * @return $this
     */
    public function set_start_date_time($value)
    {
        $this->start_date_time = $value;
        return $this;
    }

    /**
     * Get the end date/time (ISO 8601).
     *
     * @return string|null
     */
    public function get_end_date_time()
    {
        return $this->end_date_time;
    }

    /**
     * Set the end date/time (ISO 8601).
     *
     * @param string|null $value
     * @return $this
     */
    public function set_end_date_time($value)
    {
        $this->end_date_time = $value;
        return $this;
    }

    /**
     * Convert the line item to a JSON string.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(array_filter([
            "id" => $this->id,
            "scoreMaximum" => $this->score_maximum,
            "label" => $this->label,
            "resourceId" => $this->resource_id,
            "tag" => $this->tag,
            "startDateTime" => $this->start_date_time,
            "endDateTime" => $this->end_date_time,
        ]));
    }
}
?>