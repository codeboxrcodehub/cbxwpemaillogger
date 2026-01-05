<?php

namespace ComfortSmtpScoped\Illuminate\Support;

use ComfortSmtpScoped\Carbon\Carbon as BaseCarbon;
use ComfortSmtpScoped\Carbon\CarbonImmutable as BaseCarbonImmutable;
class Carbon extends BaseCarbon
{
    /**
     * {@inheritdoc}
     */
    public static function setTestNow($testNow = null)
    {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }
}
