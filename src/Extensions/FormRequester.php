<?php

namespace Goldfinch\Component\Forms\Extensions;

use Goldfinch\Component\Forms\Models\FormRecord;
use Goldfinch\Component\Forms\Models\FormSegment;
use Goldfinch\Component\Forms\Rules\FormSegmentChecker;
use Goldfinch\Illuminate\Validator;
use Goldfinch\Requester\Requester;
use Goldfinch\Service\Rules\GoogleRecaptcha;
use Goldfinch\Service\SendGrid;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;

class FormRequester extends Requester
{
    public static $segment_type;

    public static $emailBody;

    public static $attachments = [];

    public static $fallback_email = 'unassigned@unassignedemail.com';

    public static $fallback_from_name = 'Unassigned Name';

    public static $fallback_from_subject = 'Unassigned Subject';

    public static function handle()
    {
        $data = self::getData();

        // TODO: thank you page / popup

        $segment = self::getSegment();
        $cfg = $segment->getSegmentTypeConfig();

        // send email to admin
        SendGrid::send([
            'name' => $segment->FormName ?? static::$fallback_from_name,
            'from' => $segment->FormFrom ?? static::$fallback_email,
            'subject' => $segment->FormSubject ?? static::$fallback_from_subject,
            'reply_to' => $segment->FormReplyTo ?? static::$fallback_email,
            'to' => $segment->formatedTo() && ! empty($segment->formatedTo())
                    ? $segment->formatedTo()
                    : static::$fallback_email,
            'bcc' => $segment->formatedBcc() && ! empty($segment->formatedBcc())
                    ? $segment->formatedBcc()
                    : null,
            'cc' => $segment->formatedCc() && ! empty($segment->formatedCc())
                    ? $segment->formatedCc()
                    : null,
            'body' => static::$emailBody,
            'attachments' => self::$attachments,
        ]);

        // send email to the user
        if (
            $segment->FormSendSenderEmail &&
            $data['email'] &&
            $segment->FormSenderName &&
            $segment->FormSenderFrom &&
            $segment->FormSenderSubject &&
            $segment->FormSenderReplyTo
        ) {
            $name = '';

            if (isset($data['name'])) {
                $name = $data['name'];
            } elseif (isset($data['firstname'])) {
                $name = $data['firstname'];

                if (isset($data['lastname'])) {
                    $name .= ' '.$data['lastname'];
                }
            }

            SendGrid::send([
                'name' => $segment->FormSenderName,
                'from' => $segment->FormSenderFrom,
                'subject' => $segment->FormSenderSubject,
                'reply_to' => $segment->FormSenderReplyTo,
                'to' => [$data['email'] => $name],
                'body' => $segment->replacableData(
                    $segment->FormSenderBody,
                    $data,
                ),
            ]);
        }

        if ($cfg['records']) {
            if (
                isset($cfg['records_fields']) &&
                ! empty($cfg['records_fields'])
            ) {
                $recordData = array_only($data, $cfg['records_fields']);
            } else {
                $recordData = $data;
            }

            // save record
            $record = new FormRecord();
            $record->RecordData = json_encode($recordData);
            $record->write();
            $segment->Records()->add($record);
        }

        if ($segment->FormThankYouPage) {
            $request = Injector::inst()->get(HTTPRequest::class);
            $session = $request->getSession();
            $session->set(
                'thank-you-'.$segment->Type,
                $segment->replacableData($segment->FormSuccessMessage, $data),
            );
        }

        return json_encode([
            'status' => true,
            'message' => $segment->FormSuccessMessage
                ? $segment->replacableData($segment->FormSuccessMessage, $data)
                : self::message($data),
        ], JSON_HEX_QUOT | JSON_HEX_TAG);
    }

    public static function setAttachments($data, $field)
    {
        if (isset($data[$field]) && isset($data[$field]['tmp_name'])) {

            if (is_array($data[$field]['tmp_name'])) {

                foreach ($data[$field]['tmp_name'] as $key => $file) {
                    self::$attachments[] = [
                        'name' => $data[$field]['name'][$key],
                        'tmp_name' => $data[$field]['tmp_name'][$key],
                        'size' => $data[$field]['size'][$key],
                    ];
                }

            } else {

                self::$attachments[] = [
                    'name' => $data[$field]['name'],
                    'tmp_name' => $data[$field]['tmp_name'],
                    'size' => $data[$field]['size'],
                ];
            }

        }
    }

    public static function validator()
    {
        $data = self::getData();

        Validator::validate($data, [
            'token' => ['required', new GoogleRecaptcha()],
            'segment_id' => [
                'required',
                new FormSegmentChecker(self::$segment_type),
            ],
        ]);

        return true;
    }

    public static function getSegment()
    {
        $data = self::getData();

        $id = (int) $data['segment_id'];

        return FormSegment::get()->byID($id);
    }

    public static function getSegmentConfig()
    {
        $segment = self::getSegment();

        if ($segment) {
            return $segment->getSegmentTypeConfig();
        }

        return null;
    }

    public static function getSegmentSupplies()
    {
        $segment = self::getSegment();

        if ($segment) {
            return json_decode($segment->FormSupplies(), true);
        }

        return null;
    }
}
