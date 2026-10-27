<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * One sheet of the Profit & Loss workbook. Strict null comparison keeps 0 amounts as 0 instead of empty cells.
 */
class ProfitLossSheetExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStrictNullComparison
{
    public function __construct(private string $title, private array $headings, private array $rows)
    {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}
