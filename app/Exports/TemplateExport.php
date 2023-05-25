<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class TemplateExport implements FromView,WithColumnWidths
{
    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 50,            
        ];
    } 
    public function view(): View
    {
        return view('exports.template');
    }
}
