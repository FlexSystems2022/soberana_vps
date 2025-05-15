<?php

namespace App\Shared\Commands;

use App\Shared\Commands\Traits\LoggerCommand;
use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
    use LoggerCommand;
}