<?php
namespace IMSGlobal\LTI;

/**
 * Class LTI_Assignments_Grades_Service
 * Handles LTI Assignment and Grade Services (AGS) operations.
 * @package IMSGlobal\LTI
 */
class LTI_Assignments_Grades_Service
{
    /**
     * @var LTI_Service_Connector
     */
    private $service_connector;

    /**
     * Service data array.
     * @var array<string, mixed> {
     *     scope: array<int, string>,
     *     lineitem?: string,
     *     lineitems?: string
     * }
     */
    private $service_data;

    /**
     * LTI_Assignments_Grades_Service constructor.
     * @param LTI_Service_Connector $service_connector
     * @param array<string, mixed> $service_data
     */
    public function __construct(LTI_Service_Connector $service_connector, $service_data)
    {
        $this->service_connector = $service_connector;
        $this->service_data = $service_data;
    }

    /**
     * Submit a grade for a user to a line item.
     *
     * @param LTI_Grade $grade
     * @param LTI_Lineitem|null $lineitem
     * @return mixed
     * @throws LTI_Exception
     */
    public function put_grade(LTI_Grade $grade, LTI_Lineitem $lineitem = null)
    {
        if (!in_array("https://purl.imsglobal.org/spec/lti-ags/scope/score", $this->service_data['scope'])) {
            throw new LTI_Exception('Missing required scope', 1);
        }
        $score_url = '';
        if ($lineitem !== null && empty($lineitem->get_id())) {
            $lineitem = $this->find_or_create_lineitem($lineitem);
            $score_url = $lineitem->get_id();
        } else if ($lineitem !== null && !empty($lineitem->get_id())) {
            $score_url = $lineitem->get_id();
        } else if ($lineitem === null && !empty($this->service_data['lineitem'])) {
            $score_url = $this->service_data['lineitem'];
        } else if ($lineitem === null && !empty($this->service_data['lineitems'])) {
            $lineitem = LTI_Lineitem::new()
                ->set_label('default')
                ->set_score_maximum(100);
            $lineitem = $this->find_or_create_lineitem($lineitem);
            $score_url = $lineitem->get_id();
        } else {
            throw new LTI_Exception('No lineitem or lineitems information available to submit grade', 1);
        }

        // Place '/scores' before url params
        $pos = strpos($score_url, '?');
        $score_url = $pos === false ? $score_url . '/scores' : substr_replace($score_url, '/scores', $pos, 0);
        return $this->service_connector->make_service_request(
            $this->service_data['scope'],
            'POST',
            $score_url,
            strval($grade),
            'application/vnd.ims.lis.v1.score+json'
        );
    }

    /**
     * Find an existing line item or create a new one if not found.
     *
     * @param LTI_Lineitem $new_line_item
     * @return LTI_Lineitem
     * @throws LTI_Exception
     */
    public function find_or_create_lineitem(LTI_Lineitem $new_line_item)
    {
        if (!in_array("https://purl.imsglobal.org/spec/lti-ags/scope/lineitem", $this->service_data['scope'])) {
            throw new LTI_Exception('Missing required scope', 1);
        }
        $line_items = $this->service_connector->make_service_request(
            $this->service_data['scope'],
            'GET',
            $this->service_data['lineitems'],
            null,
            null,
            'application/vnd.ims.lis.v2.lineitemcontainer+json'
        );
        foreach ($line_items['body'] as $line_item) {
            if (empty($new_line_item->get_resource_id()) || $line_item['resourceId'] == $new_line_item->get_resource_id()) {
                if (empty($new_line_item->get_tag()) || $line_item['tag'] == $new_line_item->get_tag()) {
                    return new LTI_Lineitem($line_item);
                }
            }
        }
        $created_line_item = $this->service_connector->make_service_request(
            $this->service_data['scope'],
            'POST',
            $this->service_data['lineitems'],
            strval($new_line_item),
            'application/vnd.ims.lis.v2.lineitem+json',
            'application/vnd.ims.lis.v2.lineitem+json'
        );
        return new LTI_Lineitem($created_line_item['body']);
    }

    /**
     * Get grades/results for a line item.
     *
     * @param LTI_Lineitem $lineitem
     * @return array<int, array<string, mixed>>
     */
    public function get_grades(LTI_Lineitem $lineitem)
    {
        $lineitem = $this->find_or_create_lineitem($lineitem);
        // Place '/results' before url params
        $pos = strpos($lineitem->get_id(), '?');
        $results_url = $pos === false ? $lineitem->get_id() . '/results' : substr_replace($lineitem->get_id(), '/results', $pos, 0);
        $scores = $this->service_connector->make_service_request(
            $this->service_data['scope'],
            'GET',
            $results_url,
            null,
            null,
            'application/vnd.ims.lis.v2.resultcontainer+json'
        );

        return $scores['body'];
    }
}
?>