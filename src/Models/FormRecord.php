<?php

namespace Goldfinch\Component\Forms\Models;

use Carbon\Carbon;
use Goldfinch\Harvest\Harvest;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\LiteralField;
use Goldfinch\Harvest\Traits\HarvestTrait;

class FormRecord extends DataObject
{
    use HarvestTrait;

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

    public function harvest(Harvest $harvest): void
    {
        if ($this->RecordData) {
            $beautyData =
                '<pre>' .
                print_r(json_decode($this->RecordData, true), true) .
                '</pre>';
        } else {
            $beautyData = '';
        }

        $harvest->fields([
            'Root.Main' => [$harvest->literal('RecordData', $beautyData)],
        ]);

        $harvest->makeReadonly();
    }

    public function TimeAgo()
    {
        return Carbon::parse($this->Created)->diffForHumans();
    }
}
