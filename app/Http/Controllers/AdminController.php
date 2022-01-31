<?php

namespace App\Http\Controllers;

use App\Jobs\ExportCategoriesJob;
use App\Jobs\ExportProductJob;

class AdminController extends Controller
{
    public function exportCategories (): \Illuminate\Foundation\Bus\PendingDispatch
    {
        return ExportCategoriesJob::dispatch();
    }
    public function exportProduct (): \Illuminate\Foundation\Bus\PendingDispatch
    {
        return ExportProductJob::dispatch();
    }
}
