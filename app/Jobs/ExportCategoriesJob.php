<?php

namespace App\Jobs;

use App\Events\CategoriesExportFinishEvent;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportCategoriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    public $exportColumns;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($exportColumns = true)
    {
        $this->exportColumns = $exportColumns;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $categories = Category::get()->toArray();
        Storage::delete('/public/exportCategories.csv');

        if ($this->exportColumns) {
            $columns = [
                'id',
                'name',
                'description',
                'picture',
                'created_at',
                'updated_at'
            ];
            Storage::append('/public/exportCategories.csv', implode(';', $columns));
        }

        foreach ($categories as $category) {
            $category['name'] = iconv('utf-8', 'windows-1251//IGNORE', $category['name']);
            $category['description'] = iconv('utf-8', 'windows-1251//IGNORE', $category['description']);
            $category['picture'] = iconv('utf-8', 'windows-1251//IGNORE', $category['picture']);
            Storage::append('/public/exportCategories.csv', implode(';', $category));
        }
        event(new CategoriesExportFinishEvent('exportCategories.csv'));
    }
}
