<?php

namespace xenialdan\MapAPI\task;

use pocketmine\scheduler\Task;
use xenialdan\MapAPI\Loader;

class AsyncGenerateTask extends Task
{
    /** @var Loader $plugin */
    private $plugin;

    public function __construct(Loader $owner)
    {
        $this->plugin = $owner;
    }

    public function onRun(int $currentTick)
    {
    }

    public function cancel()
    {
        $this->getHandler()->cancel();
    }
}