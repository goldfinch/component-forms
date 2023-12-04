<?php

namespace Goldfinch\Component\Forms\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\SecurityToken;
use Goldfinch\Requester\Requester;
use Goldfinch\Component\Forms\Models\FormSegment;

class ThankYouController extends Controller
{
    private static $url_handlers = [
        'POST $type' => 'tunnel',
    ];

    private static $allowed_actions = [
        'tunnel',
    ];

    public function init()
    {
        parent::init();
    }

    public function tunnel(HTTPRequest $request)
    {
        $this->authorized($request);

        $data = $request->postVars();
        $params = implode('/', $request->latestParams());

        $types = ss_config(FormSegment::class, 'segment_types');

        if (isset($rules[$params]))
        {
            return $rules[$params]::init($request);
        }

        return $this->httpError(403);
    }

    protected function authorized(HTTPRequest $request)
    {
        if(!$request->isPOST())
        {
            return $this->httpError(403, 'This action is unauthorized');
        }
        else if($request->getHeader('X-CSRF-TOKEN') != SecurityToken::getSecurityID())
        {
            return $this->httpError(401, 'Unauthorized');
        }
    }
}
