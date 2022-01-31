<?php

namespace App\Jobs;

use App\Events\CategoriesExportFinishEvent;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class ExportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $categories = Product::get()->toArray();
        Storage::delete('/public/exportProduct.csv');

        if ($this->exportColumns) {
            $columns = [
                'id',
                'name',
                'description',
                'picture',
                'price',
                'category_id',
                'created_at',
                'updated_at'
            ];
            Storage::append('/public/exportProduct.csv', implode(';', $columns));
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
