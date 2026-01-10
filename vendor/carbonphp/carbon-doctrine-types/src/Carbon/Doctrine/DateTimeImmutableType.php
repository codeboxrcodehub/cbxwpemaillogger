<?php

declare (strict_types=1);
namespace ComfortSmtpScoped\Carbon\Doctrine;

use ComfortSmtpScoped\Carbon\CarbonImmutable;
use DateTimeImmutable;
use ComfortSmtpScoped\Doctrine\DBAL\Platforms\AbstractPlatform;
use ComfortSmtpScoped\Doctrine\DBAL\Types\VarDateTimeImmutableType;
class DateTimeImmutableType extends VarDateTimeImmutableType implements CarbonDoctrineType
{
    /** @use CarbonTypeConverter<CarbonImmutable> */
    use CarbonTypeConverter;
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?CarbonImmutable
    {
        return $this->doConvertToPHPValue($value);
    }
    /**
     * @return class-string<CarbonImmutable>
     */
    protected function getCarbonClassName(): string
    {
        return CarbonImmutable::class;
    }
}
