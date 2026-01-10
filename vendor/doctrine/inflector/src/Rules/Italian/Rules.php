<?php

declare (strict_types=1);
namespace ComfortSmtpScoped\Doctrine\Inflector\Rules\Italian;

use ComfortSmtpScoped\Doctrine\Inflector\Rules\Patterns;
use ComfortSmtpScoped\Doctrine\Inflector\Rules\Ruleset;
use ComfortSmtpScoped\Doctrine\Inflector\Rules\Substitutions;
use ComfortSmtpScoped\Doctrine\Inflector\Rules\Transformations;
final class Rules
{
    public static function getSingularRuleset(): Ruleset
    {
        return new Ruleset(new Transformations(...Inflectible::getSingular()), new Patterns(...Uninflected::getSingular()), (new Substitutions(...Inflectible::getIrregular()))->getFlippedSubstitutions());
    }
    public static function getPluralRuleset(): Ruleset
    {
        return new Ruleset(new Transformations(...Inflectible::getPlural()), new Patterns(...Uninflected::getPlural()), new Substitutions(...Inflectible::getIrregular()));
    }
}
