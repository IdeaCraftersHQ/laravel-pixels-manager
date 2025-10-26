<?php

namespace Ideacrafters\PixelManager\Commands;

use Illuminate\Console\Command;

class PixelManagerCommand extends Command
{
    public $signature = 'pixels';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
