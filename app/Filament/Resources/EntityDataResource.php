<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntityDataResource\Pages;
use App\Models\DynamicModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema;

class EntityDataResource extends Resource
{
    protected static ?string $model = DynamicModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-table';
    protected static ?string $navigationLabel = 'Entity Data';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form fields will be added dynamically
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Table columns will be added dynamically
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListEntityData::route('/'),
            'create' => Pages\CreateEntityData::route('/create'),
            'edit' => Pages\EditEntityData::route('/{record}/edit'),
        ];
    }
} 