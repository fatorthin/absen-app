<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;
use Carbon\Carbon;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                DatePicker::make('birthdate')->required()->maxDate(now()),
                TextInput::make('no_wa')
                    ->label('No. WA')
                    ->required(),
                Select::make('group_id')
                    ->relationship('group', 'name')
                    ->label('Group')
                    ->required(),
                Select::make('class')->options([
                    '1 SMA' => '1 SMA',
                    '2 SMA' => '2 SMA',
                    '3 SMA' => '3 SMA',
                ]),
                Select::make('gender')->options([
                    'L' => 'Laki-laki',
                    'P' => 'Perempuan',
                ]),
                FileUpload::make('avatar')
                    ->avatar()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')
                //     ->label('ID'),
                ImageColumn::make('avatar')
                    ->circular(),
                TextColumn::make('name')->sortable()->searchable()
                    ->label('Name'),
                TextColumn::make('age')
                    ->label('Age')
                    ->getStateUsing(function (Student $record) {
                        return Carbon::parse($record->birthdate)->age;
                    }),
                TextColumn::make('gender')->sortable()->searchable(),
                TextColumn::make('group.name')->sortable()->searchable()
                    ->label('Group'),
                TextColumn::make('class')->sortable()->searchable()
                    ->label('Class'),

                ImageColumn::make('qr_code')
                    ->label('QR Code')
                    ->getStateUsing(function (Student $record) {
                        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . $record->uuid;
                        return $qrCodeUrl;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_id')
                    ->relationship('group', 'name')
                    ->label('Group'),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('details')
                    ->url(fn (Student $record): string => static::getUrl('detail', ['record' => $record]))
                    ->icon('heroicon-o-information-circle')
                    ->color('success')
                    ->iconButton()
                    ->label('Detail'),
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ViewAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->recordAction(Tables\Actions\ViewAction::class)
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'detail' => Pages\DetailStudent::route('/{record}/detail'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
