<?php

namespace ComfortSmtpScoped\Illuminate\Database\PDO;

use ComfortSmtpScoped\Doctrine\DBAL\Driver\AbstractMySQLDriver;
use ComfortSmtpScoped\Illuminate\Database\PDO\Concerns\ConnectsToDatabase;
class MySqlDriver extends AbstractMySQLDriver
{
    use ConnectsToDatabase;
}
