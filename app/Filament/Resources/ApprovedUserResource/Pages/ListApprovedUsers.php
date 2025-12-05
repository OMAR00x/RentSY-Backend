<?php

namespace App\Filament\Resources\ApprovedUserResource\Pages;

use App\Filament\Resources\ApprovedUserResource;
use Filament\Resources\Pages\ListRecords;

class ListApprovedUsers extends ListRecords
{
    protected static string $resource = ApprovedUserResource::class;
}
