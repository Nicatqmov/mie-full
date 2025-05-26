<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\DynamicModel;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EntityData extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-table';
    protected static ?string $navigationLabel = 'Entity Data';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.entity-data';
    protected static ?string $slug = 'entity-data';

    public ?Project $project = null;
    public $entity = null;

    public function mount(): void
    {
        $projectId = request()->query('project');
        $entityId = request()->query('entity');

        if ($projectId && $entityId) {
            $this->project = Project::with(['entities' => function ($query) use ($entityId) {
                $query->where('id', $entityId)->with('fields');
            }])->find($projectId);

            if ($this->project) {
                $this->entity = $this->project->entities->first();
            }
        }
    }

    public function table(Table $table): Table
    {
        if (!$this->entity) {
            return $table;
        }

        $actualTableName = $this->entity->table_name;
        $columns = Schema::getColumnListing($actualTableName);

        $tableColumns = [];
        foreach ($this->entity->fields as $field) {
            $tableColumns[] = $this->getTableColumn($field);
        }

        $model = new DynamicModel();
        $model->setTableName($actualTableName);

        return $table
            ->query($model->newQuery())
            ->columns($tableColumns)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.admin.pages.edit', [
                        'project' => $this->project->id,
                        'entity' => $this->entity->id,
                        'record' => $record->id,
                    ])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(route('filament.admin.pages.create', [
                        'project' => $this->project->id,
                        'entity' => $this->entity->id,
                    ])),
            ]);
    }

    public static function getSlug(): string
    {
        return 'entity-data';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getTableColumn($field)
    {
        $type = $field->dataType->type;
        $name = $field->column_name;

        return match ($type) {
            'string' => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->searchable(),
            'text' => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->searchable(),
            'integer' => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->numeric()
                ->sortable(),
            'decimal' => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->numeric()
                ->sortable(),
            'boolean' => Tables\Columns\IconColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->boolean(),
            'date' => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->date()
                ->sortable(),
            'datetime' => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->dateTime()
                ->sortable(),
            'image' => Tables\Columns\ImageColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->disk('public')
                ->visibility('public'),
            default => Tables\Columns\TextColumn::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->searchable(),
        };
    }
} 