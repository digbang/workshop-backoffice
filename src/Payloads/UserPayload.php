<?php

namespace WorkshopBackoffice\Payloads;

interface UserPayload
{
    public function email(): string;

    public function password(): string;

    public function firstName(): string;

    public function lastname(): string;
}
