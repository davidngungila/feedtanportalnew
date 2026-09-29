<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PayoutSheetImport implements ToCollection
{
    public array $rows = [];

    public function collection(Collection $rows): void
    {
        $this->rows = $rows->toArray();
    }
}
