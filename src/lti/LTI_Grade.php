<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Grade
 * Represents a grade object for LTI assignments.
 * @package IMSGlobal\LTI
 */
class LTI_Grade
{
    /**
     * @var float|int|null
     */
    private $score_given;
    /**
     * @var float|int|null
     */
    private $score_maximum;
    /**
     * @var string|null
     */
    private $comment;
    /**
     * @var string|null
     */
    private $activity_progress;
    /**
     * @var string|null
     */
    private $grading_progress;
    /**
     * @var string|null
     */
    private $timestamp;
    /**
     * @var string|int|null
     */
    private $user_id;
    /**
     * @var mixed|null
     */
    private $submission_review;

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     * @return LTI_Grade
     */
    public static function new()
    {
        return new LTI_Grade();
    }

    /**
     * @return float|int|null
     */
    public function get_score_given()
    {
        return $this->score_given;
    }

    /**
     * @param float|int|null $value
     * @return $this
     */
    public function set_score_given($value)
    {
        $this->score_given = $value;
        return $this;
    }

    /**
     * @return float|int|null
     */
    public function get_score_maximum()
    {
        return $this->score_maximum;
    }

    /**
     * @param float|int|null $value
     * @return $this
     */
    public function set_score_maximum($value)
    {
        $this->score_maximum = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function get_comment()
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return $this
     */
    public function set_comment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string|null
     */
    public function get_activity_progress()
    {
        return $this->activity_progress;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function set_activity_progress($value)
    {
        $this->activity_progress = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function get_grading_progress()
    {
        return $this->grading_progress;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function set_grading_progress($value)
    {
        $this->grading_progress = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function get_timestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function set_timestamp($value)
    {
        $this->timestamp = $value;
        return $this;
    }

    /**
     * @return string|int|null
     */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     * @param string|int|null $value
     * @return $this
     */
    public function set_user_id($value)
    {
        $this->user_id = $value;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function get_submission_review()
    {
        return $this->submission_review;
    }

    /**
     * @param mixed|null $value
     * @return $this
     */
    public function set_submission_review($value)
    {
        $this->submission_review = $value;
        return $this;
    }

    /**
     * Convert the grade object to a JSON string.
     *
     * @return string JSON representation of the grade object.
     * @psalm-return string JSON encoded array with keys:
     *   scoreGiven: float|int|null,
     *   scoreMaximum: float|int|null,
     *   comment: string|null,
     *   activityProgress: string|null,
     *   gradingProgress: string|null,
     *   timestamp: string|null,
     *   userId: string|int|null,
     *   submissionReview: mixed|null
     */
    public function __toString()
    {
        return json_encode(array_filter([
            "scoreGiven" => 0 + $this->score_given,
            "scoreMaximum" => 0 + $this->score_maximum,
            "comment" => $this->comment,
            "activityProgress" => $this->activity_progress,
            "gradingProgress" => $this->grading_progress,
            "timestamp" => $this->timestamp,
            "userId" => $this->user_id,
            "submissionReview" => $this->submission_review,
        ]));
    }
}
?>