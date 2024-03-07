<?php

namespace Goldfinch\Component\Forms\Models;

use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use Goldfinch\Component\Forms\Blocks\FormBlock;
use Goldfinch\Component\Forms\Models\FormRecord;
use Goldfinch\Component\Forms\Configs\FormConfig;
use Goldfinch\JSONEditor\ORM\FieldType\DBJSONText;
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

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fielder = $fields->fielder($this);

        $fielder->remove([
            'Emailtoadmin',
            'Emailtosender',
        ]);

        $fielder->required(['Title', 'Type']);

        if ($this->getSegmentTypeConfig('records')) {
            $recordsGrid = $fielder->dataField('Records');
            if ($recordsGrid) {
                $recordsGrid
                    ->getConfig()
                    ->removeComponentsByType(GridFieldDeleteAction::class)
                    ->removeComponentsByType(GridFieldAddNewButton::class)
                    ->removeComponentsByType(GridFieldPrintButton::class)
                    ->removeComponentsByType(GridFieldExportButton::class)
                    ->removeComponentsByType(GridFieldImportButton::class)
                    ->removeComponentsByType(
                        GridFieldAddExistingAutocompleter::class,
                        // ->removeComponentsByType(GridFieldPaginator::class)
                        // ->addComponent(GridFieldConfigurablePaginator::create())
                    );
            }
        } else {
            $fielder->remove('Records');
        }

        $typesOptions = $this->getSegmentListOfTypes() ?? [];

        $fielder->fields([
            'Root.Main' => [
                $fielder->string('Title'),
                $fielder
                    ->checkbox('Disabled')
                    ->setDescription('hide this form across the website'),
                $fielder->dropdown('Type', 'Type', $typesOptions),
            ],
        ]);

        $fielder->fields([
            'Root.Settings' => [
                $fielder
                    ->group(
                        'Email to admin',
                        $fielder
                            ->string('FormName', 'Name')
                            ->setAttribute('placeholder', 'Jaina Proudmoore')
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->string('FormFrom', 'From')
                            ->setAttribute(
                                'placeholder',
                                'jaina@proudmoore.com',
                            )
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->string('FormSubject', 'Subject')
                            ->setAttribute('placeholder', 'Contact enquiry')
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->string('FormReplyTo', 'Reply to')
                            ->setAttribute(
                                'placeholder',
                                'jaina@proudmoore.com',
                            )
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->text('FormTo', 'To')
                            ->addExtraClass('fcol-12')
                            ->setAttribute(
                                'placeholder',
                                'john@doe.com : John Doe' . PHP_EOL . 'varian@wrynn.com : Varian Wrynn',
                            ),
                        $fielder
                            ->text('FormBcc', 'BCC')
                            ->addExtraClass('fcol-12')
                            ->setAttribute(
                                'placeholder',
                                'john@doe.com : John Doe' . PHP_EOL . 'varian@wrynn.com : Varian Wrynn',
                            ),
                        $fielder
                            ->text('FormCc', 'CC')
                            ->addExtraClass('fcol-12')
                            ->setAttribute(
                                'placeholder',
                                'john@doe.com : John Doe' . PHP_EOL . 'varian@wrynn.com : Varian Wrynn',
                            ),
                        // $fielder->html('FormBody', 'Body')->addExtraClass('fcol-12'),
                        $fielder
                            ->html('FormSuccessMessage', 'Thank you message')
                            ->addExtraClass('fcol-12'),
                        // $fielder->html('FormFailMessage', 'Failed message')->addExtraClass('fcol-12'
                    ),

                $fielder
                    ->checkbox('FormThankYouPage', 'Thank you page')
                    ->setDescription('Show thank you message on its own page'),
                $fielder
                    ->string('FormThankYouPageTitle', 'Thank you page (Title)')
                    ->displayIf('FormThankYouPage')
                    ->isChecked()
                    ->end(),
                $fielder->literal('FormSendSenderEmailHTML', '<p></p>'),
                $fielder->checkbox(
                    'FormSendSenderEmail',
                    'Send confirmation email to the sender',
                ),

                $fielder
                    ->wrapper(
                        $fielder
                            ->group(
                                'Email to sender',
                                $fielder
                                    ->string('FormSenderName', 'Name')
                                    ->setAttribute(
                                        'placeholder',
                                        'Jaina Proudmoore',
                                    )
                                    ->addExtraClass('fcol-6'),
                        $fielder
                            ->string('FormSenderFrom', 'From')
                            ->setAttribute(
                                'placeholder',
                                'jaina@proudmoore.com',
                            )
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->string('FormSenderSubject', 'Subject')
                            ->setAttribute(
                                'placeholder',
                                'Thank you for your enquiry',
                            )
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->string('FormSenderReplyTo', 'Reply to')
                            ->setAttribute(
                                'placeholder',
                                'jaina@proudmoore.com',
                            )
                            ->addExtraClass('fcol-6'),
                        $fielder
                            ->html('FormSenderBody', 'Body')
                            ->addExtraClass('fcol-12'),

                        ),
                        )
                    ->displayIf('FormSendSenderEmail')
                    ->isChecked()
                    ->end(),
            ],
        ]);

        if ($this->ID && $this->Type) {
            $schemaParamsPath =
                BASE_PATH . '/app/_schema/' . 'form-' . $this->Type . '.json';

            if (file_exists($schemaParamsPath)) {
                $schemaParams = file_get_contents($schemaParamsPath);

                $fielder->fields([
                    'Root.Settings' => [
                        $fielder
                            ->json(
                                'Parameters',
                                null,
                                [],
                                '{}',
                                null,
                                $schemaParams,
                            )
                            ->addExtraClass('mt-2'),
                    ],
                ]);
            }
        }

        $cfg = FormConfig::current_config();

        if (!class_exists('DNADesign\Elemental\Models\BaseElement')) {
            $fielder->remove('Blocks');
        }

        if ($cfg->DisabledRecords) {
            $fielder->remove('Records');
        }

        return $fields;
    }

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

        if ($str) {
            $explodedItems = explode(PHP_EOL, $str);

            foreach ($explodedItems as $key => $line) {
                if (strpos($line, ':') !== false) {
                    $item = explode(':', $line);

                    $itemEmail = trim($item[0]);
                    $itemName = trim($item[1]);
                } else {
                    $itemEmail = trim($item[0]);
                    $itemName = explode('@', $itemEmail)[0];
                }

                $data[$itemEmail] = $itemName;
            }
        }

        return $data;
    }

    public function RenderSegmentForm($blockID, $blockClass)
    {
        if ($this->Disabled) {
            return;
        }

        $partialFile = 'Components/Forms/' . $this->Type;

        if (ss_theme_template_file_exists($partialFile)) {
            return $this->Type
                ? $this->customise([
                    'Block' => $blockClass::get()->byID($blockID),
                ])->renderWith($partialFile)
                : null;
        } else {
            return $this->customise([
                'Block' => $blockClass::get()->byID($blockID),
            ])->renderWith('Goldfinch/Component/Forms/FormSegment');
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

        if ($cfg && isset($cfg['supplies_fields'])) {
            $parameters = $this->dbObject('Parameters')
                ->Parse();

            if (get_class($parameters) == ArrayList::class) {
                $parameters = $parameters->map();
            } else {
                $parameters = $parameters->toMap();
            }

            foreach ($cfg['supplies_fields'] as $field) {
                $item = $parameters[$field];

                if (is_object($item) && get_class($item) == ArrayList::class) {

                    foreach ($item as $i) {
                        $data['parameters'][$field][] = $i->getValue();
                    }

                    // $data['parameters'][$field] = $item->toArray();
                } elseif (is_string($item)) {
                    $data['parameters'][$field] = $item;
                }
            }

            return json_encode($data);
        }

        return json_encode($data);
    }

    public function RecordsCounter()
    {
        if ($this->getSegmentTypeConfig('records')) {
            return $this->Records()->Count();
        }

        return '-';
    }

    public function getSegmentListOfTypes($key = 'label')
    {
        $types = $this->config()->get('segment_types');

        if ($types && count($types)) {
            return array_map(function ($n) use ($key) {
                return $n[$key];
            }, $types);
        }

        return null;
    }

    public function replacableData($string, $data)
    {
        $cfg = $this->getSegmentTypeConfig();

        if ($cfg && $cfg['replacable_data']) {
            $replace_from = array_map(
                fn($value): string => '[' . $value . ']',
                $cfg['replacable_data'],
            );

            $replace_to = array_only($data, $cfg['replacable_data']);

            $string = str_replace($replace_from, $replace_to, $string);
        }

        return $string;
    }

    public function getSegmentTypeConfig($param = null)
    {
        $types = $this->config()->get('segment_types');

        if (
            $types &&
            count($types) &&
            $this->Type &&
            isset($types[$this->Type])
        ) {
            if ($param) {
                if (isset($types[$this->Type][$param])) {
                    return $types[$this->Type][$param];
                } else {
                    return null;
                }
            } else {
                return $types[$this->Type];
            }
        }

        return null;
    }

    public function onBeforeWrite()
    {
        $changed = $this->getChangedFields();

        if (isset($changed['Type'])) {
            if ($changed['Type']['before'] != $changed['Type']['after']) {
                $this->Parameters = '';
            }
        }

        parent::onBeforeWrite();
    }
}
