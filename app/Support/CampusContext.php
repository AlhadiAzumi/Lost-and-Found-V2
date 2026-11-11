<?php

namespace App\Support;

use App\Models\Campus;

class CampusContext
{
    private ?Campus $campus = null;

    public function set(Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function campus(): ?Campus
    {
        return $this->campus;
    }

    public function id(): ?int
    {
        return $this->campus?->id;
    }
}
