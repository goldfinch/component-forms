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
        'GetTimeAgo' => 'Time ago',
        'Created' => 'Received at',
        'Segment.Type' => 'Type',
    ];

    // private static $belongs_to = [];
    // private static $has_many = [];
    // private static $many_many = [];
    // private static $many_many_extraFields = [];
    // private static $belongs_many_many = [];
    private static $default_sort = 'Created DESC';
    // private static $indexes = null;
    // private static $owns = [];
    // private static $casting = [];
    // private static $defaults = [];

    // private static $summary_fields = [];
    // private static $field_labels = [];
    // private static $searchable_fields = [];

    // private static $cascade_deletes = [];
    // private static $cascade_duplicates = [];

    // * goldfinch/helpers
    // private static $field_descriptions = [];
    // private static $required_fields = [];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields = $fields->makeReadonly();

        $fields->removeByName([
            'SegmentID',
            'RecordData',
        ]);

        $beautyData = '<pre>'.print_r(json_decode($this->RecordData, true),true).'</pre>';

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create('RecordData', $beautyData),
        );

        return $fields;
    }

    public function GetTimeAgo()
    {
        return Carbon::parse($this->Created)->diffForHumans();
    }

    // public function validate()
    // {
    //     $result = parent::validate();

    //     // $result->addError('Error message');

    //     return $result;
    // }

    // public function onBeforeWrite()
    // {
    //     // ..

    //     parent::onBeforeWrite();
    // }

    // public function onBeforeDelete()
    // {
    //     // ..

    //     parent::onBeforeDelete();
    // }

    // public function canView($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canEdit($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canDelete($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canCreate($member = null, $context = [])
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }
}
