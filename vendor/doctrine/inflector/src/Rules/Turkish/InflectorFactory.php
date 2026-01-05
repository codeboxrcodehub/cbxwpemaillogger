<?php

declare (strict_types=1);
namespace ComfortSmtpScoped\Doctrine\Inflector\Rules\Turkish;

use ComfortSmtpScoped\Doctrine\Inflector\GenericLanguageInflectorFactory;
use ComfortSmtpScoped\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : Ruleset
    {
        return Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : Ruleset
    {
        return Rules::getPluralRuleset();
    }
}
