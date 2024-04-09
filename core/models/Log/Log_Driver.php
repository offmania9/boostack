<?php
namespace Core\Models\Log;

abstract class Log_Driver
{
    const FILE = "log_driver_file";
    const DATABASE = "log_driver_database";
    const BOTH = "log_driver_both";
}