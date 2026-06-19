<?php

declare(strict_types=1);

namespace pocketmine\utils;

trait DestructorCallbackTrait
{
    private ?ObjectSet $destructorCallbacks = null;

    public function getDestructorCallbacks(): ObjectSet
    {
        return $this->destructorCallbacks ??= new ObjectSet();
    }

    public function __destruct()
    {
        if ($this->destructorCallbacks !== null) {
            foreach ($this->destructorCallbacks as $callback) {
                $callback();
            }
        }
    }
}
