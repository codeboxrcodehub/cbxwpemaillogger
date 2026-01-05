<?php

namespace ComfortSmtpScoped\Illuminate\Database\PDO;

use ComfortSmtpScoped\Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use ComfortSmtpScoped\Illuminate\Database\PDO\Concerns\ConnectsToDatabase;
class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ConnectsToDatabase;
}
