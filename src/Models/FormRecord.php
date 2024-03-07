<?php

namespace Goldfinch\Component\Forms\Models;

use Carbon\Carbon;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\LiteralField;

class FormRecord extends DataObject
{
    private static $table_name = 'FormRecord';
    private static $singular_name = 'form record';
    private static $plural_name = 'form records';

    private static $db = [
        'RecordData' => 'Text',
    ];

    private static $has_one = [
        'Segment' => FormSegment::class,
    ];

    private static $summary_fields = [
        'TimeAgo' => 'Time ago',
        'Created' => 'Received at',
        'Segment.Type' => 'Type',
    ];

    private static $default_sort = 'Created DESC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fielder = $fields->fielder($this);

        if ($this->RecordData) {
            $beautyData =
                '<pre>' .
                print_r(json_decode($this->RecordData, true), true) .
                '</pre>';
        } else {
            $beautyData = '';
        }

        $fielder->fields([
            'Root.Main' => [$fielder->literal('RecordData', $beautyData)],
        ]);

        $fielder->makeReadonly();

        return $fields;
    }

    public function TimeAgo()
    {
        return Carbon::parse($this->Created)->diffForHumans();
    }
}
