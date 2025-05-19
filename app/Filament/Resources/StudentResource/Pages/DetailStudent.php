<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\Page;
use App\Models\Student;
use Illuminate\Contracts\Support\Htmlable;

class DetailStudent extends Page
{
    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.detail-student';

    public ?Student $record = null;

    public function mount(Student $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return "Student Details: {$this->record->name}";
    }

    public function getHeading(): string|Htmlable
    {
        return "Student Details: {$this->record->name}";
    }
} 