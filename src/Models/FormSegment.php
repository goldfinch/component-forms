<?php

namespace Goldfinch\Component\Forms\Models;

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\SSViewer;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\GridField\GridField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use Goldfinch\Component\Forms\Blocks\FormBlock;
use Goldfinch\JSONEditor\Forms\JSONEditorField;
use Goldfinch\Component\Forms\Models\FormRecord;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Goldfinch\JSONEditor\ORM\FieldType\DBJSONText;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use Symbiote\GridFieldExtensions\GridFieldConfigurablePaginator;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

class FormSegment extends DataObject
{
    private static $table_name = 'FormSegment';
    private static $singular_name = 'form segment';
    private static $plural_name = 'form segments';

    private static $db = [
        'Title' => 'Varchar',
        'Type' => 'Varchar',
        'Disabled' => 'Boolean',

        'FormName' => 'Varchar',
        'FormSubject' => 'Varchar',
        'FormFrom' => 'Varchar',
        'FormReplyTo' => 'Varchar',
        'FormTo' => 'Text',
        'FormBcc' => 'Text',
        'FormCc' => 'Text',
        // 'FormBody' => 'HTMLText',
        'FormSuccessMessage' => 'HTMLText',
        // 'FormFailMessage' => 'HTMLText',

        'FormThankYouPage' => 'Boolean',
        'FormThankYouPageTitle' => 'Varchar',

        'FormSendSenderEmail' => 'Boolean',

        'FormSenderName' => 'Varchar',
        'FormSenderSubject' => 'Varchar',
        'FormSenderFrom' => 'Varchar',
        'FormSenderReplyTo' => 'Varchar',
        'FormSenderBody' => 'HTMLText',

        'Parameters' => DBJSONText::class,
    ];

    private static $has_many = [
        'Blocks' => FormBlock::class,
        'Records' => FormRecord::class,
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Type' => 'Type',
        'RecordsCounter' => 'Records',
        'Disabled.NiceAsBoolean' => 'Disabled',
    ];

    // private static $belongs_to = [];
    // private static $belongs_many_many = [];

    // private static $default_sort = null;
    // private static $indexes = null;
    // private static $casting = [];
    // private static $defaults = [];

    // private static $field_labels = [];
    // private static $searchable_fields = [];

    // private static $cascade_deletes = [];
    // private static $cascade_duplicates = [];

    // * goldfinch/helpers
    // private static $field_descriptions = [];
    private static $required_fields = [
        'FormName',
        'FormSubject',
        'FormFrom',
        'FormReplyTo',
        'FormTo',
    ];

    public function formatedTo()
    {
        return $this->formatInlineEmails($this->FormTo);
    }

    public function formatedBcc()
    {
        return $this->formatInlineEmails($this->FormBcc);
    }

    public function formatedCc()
    {
        return $this->formatInlineEmails($this->FormCc);
    }

    private function formatInlineEmails($str)
    {
        $data = [];

        if ($str)
        {
            $explodedItems = explode(PHP_EOL, $str);

            foreach ($explodedItems as $key => $line)
            {
                if (strpos($line, ':') !== false)
                {
                    $item = explode(':', $line);

                    $itemEmail = trim($item[0]);
                    $itemName = trim($item[1]);
                }
                else
                {
                    $itemEmail = trim($item[0]);
                    $itemName = explode('@', $itemEmail)[0];
                }

                $data[$itemEmail] = $itemName;
            }
        }

        return $data;
    }

    public function RenderSegmentForm()
    {
        if ($this->Disabled)
        {
            return;
        }

        $partialFile = 'Components/Forms/' . $this->Type;

        if (ss_theme_template_file_exists($partialFile))
        {
            return $this->Type ? $this->renderWith($partialFile) : null;
        }
        else
        {
            return $this->renderWith('Goldfinch/Component/Forms/FormSegment');
        }

        return null;
    }

    public function FormSupplies()
    {
        $types = $this->config()->get('segment_types');

        $cfg = $this->getSegmentTypeConfig();

        $data = [
            'id' => $this->ID,
            'thankyou_page' => $this->FormThankYouPage,
            'parameters' => [],
            'form' => isset($cfg['vue']) ? $cfg['vue'] : [],
        ];

        if ($cfg && isset($cfg['supplies_fields']))
        {
            $parameters = $this->dbObject('Parameters')->Parse()->toMap();

            foreach($cfg['supplies_fields'] as $field)
            {
                $item = $parameters[$field];

                if (is_object($item) && get_class($item) == ArrayList::class)
                {
                    $data['parameters'][$field] = $item->toArray();
                }
                else if (is_string($item))
                {
                    $data['parameters'][$field] = $item;
                }
            }

            return json_encode($data);
        }

        return json_encode($data);
    }

    public function RecordsCounter()
    {
        if ($this->getSegmentTypeConfig('records'))
        {
            return $this->Records()->Count();
        }

        return '-';
    }

    public function getSegmentListOfTypes($key = 'label')
    {
        $types = $this->config()->get('segment_types');

        if ($types && count($types))
        {
            return array_map(function($n) use ($key) {
                return $n[$key];
            }, $types);
        }

        return null;
    }

    public function replacableData($string, $data)
    {
        $cfg = $this->getSegmentTypeConfig();

        if ($cfg && $cfg['replacable_data'])
        {
            $replace_from = array_map(fn($value): string => ('[' . $value . ']'), $cfg['replacable_data']);

            $replace_to = array_only($data, $cfg['replacable_data']);

            $string = str_replace($replace_from, $replace_to, $string);
        }

        return $string;
    }

    public function getSegmentTypeConfig($param = null)
    {
        $types = $this->config()->get('segment_types');

        if ($types && count($types) && $this->Type && isset($types[$this->Type]))
        {
            if ($param)
            {
                if (isset($types[$this->Type][$param]))
                {
                    return $types[$this->Type][$param];
                }
                else
                {
                    return null;
                }
            }
            else
            {
                return $types[$this->Type];
            }
        }

        return null;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Title',
            'Type',
            'Disabled',
            'Parameters',

            'FormName',
            'FormSubject',
            'FormFrom',
            'FormReplyTo',
            'FormTo',
            'FormBcc',
            'FormCc',
            // 'FormBody',
            'FormSuccessMessage',
            // 'FormFailMessage',

            'FormSendSenderEmail',
            'FormSenderName',
            'FormSenderSubject',
            'FormSenderFrom',
            'FormSenderReplyTo',
            'FormSenderBody',
        ]);

        if ($this->getSegmentTypeConfig('records'))
        {
            $recordsGrid = $fields->dataFieldByName('Records');
            $recordsGrid->getConfig()
                ->removeComponentsByType(GridFieldDeleteAction::class)
                ->removeComponentsByType(GridFieldAddNewButton::class)
                ->removeComponentsByType(GridFieldPrintButton::class)
                ->removeComponentsByType(GridFieldExportButton::class)
                ->removeComponentsByType(GridFieldImportButton::class)
                ->removeComponentsByType(GridFieldAddExistingAutocompleter::class)
                // ->removeComponentsByType(GridFieldPaginator::class)
                // ->addComponent(GridFieldConfigurablePaginator::create())
            ;
        }
        else
        {
            $fields->removeByName('Records');
        }

        $typesOptions = $this->getSegmentListOfTypes();

        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Title',
                    'Title'
                ),
                CheckboxField::create('Disabled', 'Disabled')->setDescription('hide this form across the website'),
                DropdownField::create(
                    'Type',
                    'Type',
                    $typesOptions,
                ),
            ]
        );

        if ($this->getSegmentTypeConfig('settings'))
        {
            $fields->addFieldsToTab(
                'Root.Settings',
                [
                    FieldGroup::create(

                        TextField::create('FormName', 'Name')->setAttribute('placeholder', 'Jaina Proudmoore')->addExtraClass('fcol-6'),
                        TextField::create('FormFrom', 'From')->setAttribute('placeholder', 'jaina@proudmoore.com')->addExtraClass('fcol-6'),
                        TextField::create('FormSubject', 'Subject')->setAttribute('placeholder', 'Contact enquiry')->addExtraClass('fcol-6'),
                        TextField::create('FormReplyTo', 'Reply to')->setAttribute('placeholder', 'jaina@proudmoore.com')->addExtraClass('fcol-6'),
                        TextareaField::create('FormTo', 'To')->addExtraClass('fcol-12')->setAttribute('placeholder', 'john@doe.com : John Doe
varian@wrynn.com : Varian Wrynn'),
                        TextareaField::create('FormBcc', 'BCC')->addExtraClass('fcol-12')->setAttribute('placeholder', 'john@doe.com : John Doe
varian@wrynn.com : Varian Wrynn'),
                        TextareaField::create('FormCc', 'CC')->addExtraClass('fcol-12')->setAttribute('placeholder', 'john@doe.com : John Doe
varian@wrynn.com : Varian Wrynn'),
                        // HTMLEditorField::create('FormBody', 'Body')->addExtraClass('fcol-12'),
                        HTMLEditorField::create('FormSuccessMessage', 'Thank you message')->addExtraClass('fcol-12'),
                        // HTMLEditorField::create('FormFailMessage', 'Failed message')->addExtraClass('fcol-12'),

                    )->setTitle('Email to admin'),

                    CheckboxField::create(
                      'FormThankYouPage',
                      'Thank you page'
                    )->setDescription('Show thank you message on its own page'),

                    TextField::create('FormThankYouPageTitle', 'Thank you page (Title)')->displayIf('FormThankYouPage')->isChecked()->end(),

                    CheckboxField::create('FormSendSenderEmail','Send confirmation email to the sender'),
                    Wrapper::create(

                        FieldGroup::create(

                            TextField::create('FormSenderName', 'Name')->setAttribute('placeholder', 'Jaina Proudmoore')->addExtraClass('fcol-6'),
                            TextField::create('FormSenderFrom', 'From')->setAttribute('placeholder', 'jaina@proudmoore.com')->addExtraClass('fcol-6'),
                            TextField::create('FormSenderSubject', 'Subject')->setAttribute('placeholder', 'Thank you for your enquiry')->addExtraClass('fcol-6'),
                            TextField::create('FormSenderReplyTo', 'Reply to')->setAttribute('placeholder', 'jaina@proudmoore.com')->addExtraClass('fcol-6'),
                            HTMLEditorField::create('FormSenderBody', 'Body')->addExtraClass('fcol-12'),

                        )->setTitle('Email to sender'),

                    )->displayIf('FormSendSenderEmail')->isChecked()->end(),

                ]
            );
        }

        if ($this->ID && $this->Type)
        {
            $schemaParamsPath = BASE_PATH . '/app/_schema/' . 'form-' . $this->Type . '.json';

            if (file_exists($schemaParamsPath))
            {
                $schemaParams = file_get_contents($schemaParamsPath);

                $fields->addFieldsToTab(
                    'Root.Settings',
                    [
                        JSONEditorField::create('Parameters', 'Parameters', $this, [], '{}', null, $schemaParams)->addExtraClass('mt-2'),
                    ]
                );
            }
        }

        return $fields;
    }

    // public function validate()
    // {
    //     $result = parent::validate();

    //     // $result->addError('Error message');

    //     return $result;
    // }

    public function onBeforeWrite()
    {
        $changed = $this->getChangedFields();

        if (isset($changed['Type']))
        {
            if ($changed['Type']['before'] != $changed['Type']['after'])
            {
                $this->Parameters = '';
            }
        }

        parent::onBeforeWrite();
    }

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
