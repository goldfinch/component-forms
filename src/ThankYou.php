<?php

namespace Goldfinch\Component\Forms;

use SilverStripe\View\ArrayData;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Goldfinch\Component\Forms\Models\FormSegment;

class ThankYou extends Controller
{
    private static $url_handlers = [
        '$type!' => 'index',
    ];

    private static $allowed_actions = ['index'];

    public function index(HTTPRequest $request)
    {
        $params = implode('/', $request->latestParams());

        $types = ss_config(FormSegment::class, 'segment_types');

        $currentType = null;

        foreach ($types as $key => $type) {
            if (isset($type['vue']) && isset($type['vue']['action'])) {
                if ($type['vue']['action'] == $params) {
                    $currentType = [$type, $key];
                    break;
                }
            }
        }

        if ($currentType) {
            $segment = FormSegment::get()
                ->filter('Type', $currentType[1])
                ->first();

            $request = Injector::inst()->get(HTTPRequest::class);
            $session = $request->getSession();
            $thankYouSessionName = 'thank-you-' . $segment->Type;

            if (
                $segment &&
                $segment->FormThankYouPage &&
                $session->get($thankYouSessionName)
            ) {
                $message = DBHTMLText::create();
                $message->setValue($session->get($thankYouSessionName));

                $session->clear($thankYouSessionName);

                $title = $segment->FormThankYouPageTitle;

                $this->MetaTitle = $title;

                $data = new ArrayData([
                    'Title' => $title,
                    'Segment' => $segment,
                    'Message' => $message,
                    'BackLink' => $_SERVER['HTTP_REFERER'],
                ]);

                return $this->customise([
                    'Layout' => $this->customise($data)->renderWith(
                        ThankYou::class,
                    ),
                    'Dashpanel' => '',
                ])->renderWith('Page');
            }
        }

        return $this->httpError(404);
    }
}
