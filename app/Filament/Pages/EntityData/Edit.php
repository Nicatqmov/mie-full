<?php

namespace App\Filament\Pages\EntityData;

use App\Models\Project;
use App\Models\DynamicModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class Edit extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static string $view = 'filament.pages.entity-data.edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'entity-data/edit';

    public ?Project $project = null;
    public $entity = null;
    public int|string|null $recordId = null;
    public $data = [];
    public $actualTableName = null;

    public function mount(): void
    {
        $projectId = request()->query('project');
        $entityId = request()->query('entity');
        $recordId = request()->query('record');

        $this->recordId = $recordId;

        if ($projectId && $entityId && $recordId) {
            $this->project = Project::with(['entities' => function ($query) use ($entityId) {
                $query->where('id', $entityId)->with('fields.dataType');
            }])->find($projectId);

            if ($this->project) {
                $this->entity = $this->project->entities->first();

                if ($this->entity) {
                    $this->actualTableName = $this->entity->table_name;

                    $model = (new DynamicModel())
                        ->setTableName($this->actualTableName)
                        ->newQuery()
                        ->find($this->recordId);

                    if ($model) {
                        $this->form->fill($model->toArray());
                    }
                }
            }
        }
    }

    public function form(Form $form): Form
    {
        if (!$this->entity) {
            return $form;
        }

        $schema = [];
        foreach ($this->entity->fields as $field) {
            $schema[] = $this->getFormField($field);
        }

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    protected function getFormField($field)
    {
        $type = $field->dataType->type;
        $name = $field->column_name;

        return match ($type) {
            'string' => Forms\Components\TextInput::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->required(),
            'text' => Forms\Components\Textarea::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->required(),
            'image' => Forms\Components\FileUpload::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->image()
                ->disk('public')
                ->directory(fn () => $this->entity->table_name . '_' . $this->project->id)
                ->visibility('public')
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('16:9')
                ->imageResizeTargetWidth('1920')
                ->imageResizeTargetHeight('1080'),
            'integer' => Forms\Components\TextInput::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->numeric()
                ->required(),
            'decimal' => Forms\Components\TextInput::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->numeric()
                ->required(),
            'boolean' => Forms\Components\Toggle::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->default(false),
            'date' => Forms\Components\DatePicker::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->required(),
            'datetime' => Forms\Components\DateTimePicker::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->required(),
            default => Forms\Components\TextInput::make($name)
                ->label(ucfirst(str_replace('_', ' ', $name)))
                ->required(),
        };
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Ensure boolean fields have a value
        $booleanFields = $this->entity->fields->where('dataType.type', 'boolean')->pluck('name')->all();
        foreach ($booleanFields as $field) {
            if (!isset($data[$field])) {
                $data[$field] = false;
            }
        }

        $model = (new DynamicModel())
            ->setTableName($this->actualTableName)
            ->newQuery()
            ->find($this->recordId);

        if (! $model) {
            throw new \Exception("Record not found in table {$this->actualTableName}");
        }

        $model->fill($data);
        $model->save();

        $this->redirect(route('filament.admin.pages.entity-data', [
            'project' => $this->project->id,
            'entity' => $this->entity->id,
        ]));
    }
    

    public static function getSlug(): string
    {
        return 'edit';
    }
}
