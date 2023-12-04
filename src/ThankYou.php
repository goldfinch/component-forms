<?php

namespace Goldfinch\Component\Forms;

use SilverStripe\View\ArrayData;
use Goldfinch\Requester\Requester;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\SecurityToken;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Goldfinch\Component\Forms\Models\FormSegment;

class ThankYou extends Controller
{
    private static $url_handlers = [
        '$type!' => 'index',
    ];

    private static $allowed_actions = [
        'index',
    ];

    public function index(HTTPRequest $request)
    {
        $params = implode('/', $request->latestParams());

        $types = ss_config(FormSegment::class, 'segment_types');

        $currentType = null;

        foreach ($types as $key => $type)
        {
            if (isset($type['vue']) && isset($type['vue']['action']))
            {
                if ($type['vue']['action'] == $params)
                {
                    $currentType = [$type, $key];
                    break;
                }
            }
        }

        if ($currentType)
        {
            $segment = FormSegment::get()->filter('Type', $currentType[1])->first();

            $request = Injector::inst()->get(HTTPRequest::class);
            $session = $request->getSession();
            $thankYouSessionName = 'thank-you-' . $segment->Type;

            if ($segment && $segment->FormThankYouPage && $session->get($thankYouSessionName))
            {
                $message = DBHTMLText::create();
                $message->setValue($session->get($thankYouSessionName));

                $session->clear($thankYouSessionName);

                $data = new ArrayData([
                  'Segment' => $segment,
                  'Message' => $message,
                  'BackLink' => $_SERVER['HTTP_REFERER'],
                ]);

                return $this->customise($data);
            }
        }

        return $this->httpError(404);
    }
}
