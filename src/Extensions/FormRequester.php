<?php

namespace Goldfinch\Component\Forms\Extensions;

use Goldfinch\Service\SendGrid;
use Goldfinch\Requester\Requester;
use Goldfinch\Illuminate\Validator;
use Goldfinch\Service\Rules\GoogleRecaptcha;
use Goldfinch\Component\Forms\Models\FormRecord;
use Goldfinch\Component\Forms\Models\FormSegment;
use Goldfinch\Component\Forms\Rules\FormSegmentChecker;

class FormRequester extends Requester
{
    public static $segment_type;
    public static $emailBody;

    public static $fallback_email = 'myfallback@email.com';
    public static $fallback_from_name = 'Enquiry';
    public static $fallback_from_subject = 'Subject';
    public static $fallback_fromsender_name = 'Enquiry';
    public static $fallback_fromsender_subject = 'Thank you for your enquiry';

    public static function handle()
    {
        $data = self::getData();

        // TODO: thank you page / popup

        $segment = self::getSegment();
        $cfg = $segment->getSegmentTypeConfig();

        // send email to admin
        SendGrid::send([
            'name' => $segment->FormName ?? self::$fallback_from_name,
            'from' => $segment->FormFrom ?? self::$fallback_email,
            'subject' => $segment->FormSubject ?? self::$fallback_from_subject,
            'reply_to' => $segment->FormReplyTo ?? self::$fallback_email,
            'to' => $segment->formatedTo() ?? self::$fallback_email,
            'bcc' => $segment->formatedBcc() ?? self::$fallback_email,
            'cc' => $segment->formatedCc() ?? self::$fallback_email,
            'body' => self::$emailBody,
        ]);

        // send email to the userw
        if ($segment->FormSendSenderEmail && $data['email'])
        {
            SendGrid::send([
                'name' => $segment->FormSenderName ?? self::$fallback_fromsender_name,
                'from' => $segment->FormSenderFrom ?? self::$fallback_email,
                'subject' => $segment->FormSenderSubject ?? self::$fallback_fromsender_subject,
                'reply_to' => $segment->FormSenderReplyTo ?? self::$fallback_email,
                'to' => [$data['email'] => $data['name']],
                'body' => $segment->replacableData($segment->FormSenderBody, $data),
            ]);
        }

        if ($cfg['records'])
        {
            if (isset($cfg['records_fields']) && !empty($cfg['records_fields']))
            {
                $recordData = array_only($data, $cfg['records_fields']);
            }
            else
            {
                $recordData = $data;
            }

            // save record
            $record = new FormRecord();
            $record->RecordData = json_encode($recordData);
            $record->write();
            $segment->Records()->add($record);
        }

        return json_encode([
            'status' => true,
            'message' => $segment->FormSuccessMessage ? $segment->replacableData($segment->FormSuccessMessage, $data) : self::message($data),
        ]);
    }

    public static function validator()
    {
        $data = self::getData();

        Validator::validate($data, [
            'token'       => ['required', new GoogleRecaptcha],
            'segment_id'  => ['required', new FormSegmentChecker(self::$segment_type)],
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

        if ($segment)
        {
            return $segment->getSegmentTypeConfig();
        }

        return null;
    }

    public static function getSegmentSupplies()
    {
        $segment = self::getSegment();

        if ($segment)
        {
            return json_decode($segment->FormSupplies(), true);
        }

        return null;
    }
}
