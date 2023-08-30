<?php

namespace Ps\Sms;

use Ps\Sms\Provider\SMSUslugi;

class Events
{
    public function registerProvider()
    {
        $providers = [

            new SMSUslugi()
        ];

         

        return $providers;
    }
}
