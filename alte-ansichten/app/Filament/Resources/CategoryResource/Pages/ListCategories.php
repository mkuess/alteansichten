<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected static string $view = 'filament.resources.category-resource.pages.list-categories';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Kategorie'),
        ];
    }

    public function getViewData(): array
    {
        $categories = Category::withCount('places')
            ->orderBy('name')
            ->get();

        return [
            'categories' => $categories,
            'total' => $categories->sum('places_count'),
        ];
    }
}
