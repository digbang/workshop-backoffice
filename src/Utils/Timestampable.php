<?php

namespace WorkshopBackoffice\Utils;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;

trait Timestampable
{
    protected ChronosInterface $createdAt;
    protected ChronosInterface $updatedAt;

    public function getCreatedAt(): Chronos
    {
        return Chronos::instance($this->createdAt);
    }

    public function getUpdatedAt(): Chronos
    {
        return Chronos::instance($this->updatedAt);
    }
}
