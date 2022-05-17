<?php

namespace App\Jobs;

class ExampleJob extends BaseJob
{
    protected const QUEUE = 'examples';

    public function handle(): void
    {
        // Handle the job.
        // The serializer helps to avoid the problem of entities authomatic serialization.
        // If you need info from the entity's related entities, create serializers and fill them in the main serializer constructor.
    }
}
