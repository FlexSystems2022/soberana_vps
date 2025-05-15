<?php

namespace App\Shared\Mail\Traits;

use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\SheetCollection;
use Rap2hpoutre\FastExcel\Facades\FastExcel;

trait HasExcelFile
{
    /**
     * Create Excel file with Sheets
     * 
     * @param \Illuminate\Support\Collection|\Generator|array|null $data
     * @param callable|null $callback
     * 
     * @return string
     */
    protected function createFileExcelSheet(array $data): string
    {
        $sheets = new SheetCollection($data);

        return $this->createFileExcel($sheets, null); 
    }

    /**
     * Create Excel file
     * 
     * @param \Illuminate\Support\Collection|\Generator|array|null $data
     * @param callable|null $callback
     * 
     * @return string
     */
    protected function createFileExcel(Collection|\Generator|array|null $data, callable|null $callback): string
    {
        $excel = FastExcel::data($data);

        return $excel->export(
            storage_path('app/public/file.xlsx'),
            $callback
        );
    }
}