<?php

namespace ComfortSmtpScoped\Illuminate\Database\PDO;

use ComfortSmtpScoped\Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use ComfortSmtpScoped\Illuminate\Database\PDO\Concerns\ConnectsToDatabase;
class SQLiteDriver extends AbstractSQLiteDriver
{
    use ConnectsToDatabase;
}
