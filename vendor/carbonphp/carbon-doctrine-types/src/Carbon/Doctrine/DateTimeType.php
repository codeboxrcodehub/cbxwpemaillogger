<?php

declare (strict_types=1);
namespace ComfortSmtpScoped\Carbon\Doctrine;

use ComfortSmtpScoped\Carbon\Carbon;
use DateTime;
use ComfortSmtpScoped\Doctrine\DBAL\Platforms\AbstractPlatform;
use ComfortSmtpScoped\Doctrine\DBAL\Types\VarDateTimeType;
class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<Carbon> */
    use CarbonTypeConverter;
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Carbon
    {
        return $this->doConvertToPHPValue($value);
    }
}
