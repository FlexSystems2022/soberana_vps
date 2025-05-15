<?php

namespace App\Vendor\Logging\MySQL;
 
use Monolog\Logger;
use App\Vendor\Logging\MySQL\MySQLLoggingHandler;
 
class MySqlLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('MySQLLoggingHandler');
        return $logger->pushHandler(new MySQLLoggingHandler());
    }
}