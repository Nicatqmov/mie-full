@extends('admin.layouts.master')

@section('content')
<div class="container mt-5">
  <h1 class="fw-bold text-center mb-4">Edit Project</h1>

  <form action="{{ route('projects.update', $project->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-4">
      <label class="form-label fw-semibold">Project Name</label>
      <input type="text" name="project_name" value="{{ old('project_name', $project->name) }}" class="form-control @error('project_name') is-invalid @enderror">
      @error('project_name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div id="entities-wrapper">
      @foreach ($project->entities as $entityIndex => $entity)
        <div class="card mb-4 entity-card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <button type="button" class="btn btn-danger btn-sm remove-entity">Remove</button>
            </div>

            <div class="mb-3">
              <label class="form-label">Entity Name</label>
              <input type="text" name="entities[{{ $entityIndex }}][name]" value="{{ old("entities.$entityIndex.name", $entity['name']) }}" class="form-control" required>
              <input type="hidden" name="entities[{{ $entityIndex }}][id]" value="{{ $entity['id'] }}">
            </div>

            <div class="fields-wrapper">
              @foreach ($entity->fields as $fieldIndex => $field)
                <div class="row mb-2 field-row">
                  <div class="col-md-6">
                    <input type="text" name="entities[{{ $entityIndex }}][fields][{{ $fieldIndex }}][name]" value="{{ $field['name'] }}" class="form-control" placeholder="Field Name" required>
                  </div>
                  <div class="col-md-4">
                    <select name="entities[{{ $entityIndex }}][fields][{{ $fieldIndex }}][type]" class="form-select" required>
                      @foreach ($dataTypes as $type)
                        <option value="{{ $type->id }}" @if($field['type'] == $type->id) selected @endif>{{ ucfirst($type->name) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-field w-100">Remove</button>
                  </div>
                </div>
              @endforeach
            </div>

            <button type="button" class="btn btn-outline-secondary btn-sm add-field">+ Add Field</button>
          </div>
        </div>
      @endforeach
    </div>

    <button type="button" id="add-entity" class="btn btn-secondary mb-4">+ Add Entity</button>

    <div class="text-center">
      <button type="submit" class="btn btn-primary px-5">Update Project</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    let entityCount = {{ count($project->entities) }};
    
    const addField = (entityIndex, container) => {
      const fieldIndex = container.querySelectorAll('.field-row').length;
      const row = document.createElement('div');
      row.className = 'row mb-2 field-row';
      row.innerHTML = `
        <div class="col-md-6">
          <input type="text" name="entities[${entityIndex}][fields][${fieldIndex}][name]" class="form-control" placeholder="Field Name" required>
          <input type="hidden" name="entities[${entityIndex}][fields][${fieldIndex}][id]" value="${fieldIndex}">
        </div>
        <div class="col-md-4">
          <select name="entities[${entityIndex}][fields][${fieldIndex}][type]" class="form-select" required>
            @foreach ($dataTypes as $type)
              <option value="{{ $type->id }}">{{ ucfirst($type->name) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-outline-danger btn-sm remove-field w-100">Remove</button>
        </div>
      `;
      container.appendChild(row);
    };

    document.getElementById('add-entity').addEventListener('click', () => {
      const wrapper = document.getElementById('entities-wrapper');

      const index = entityCount++;
      const card = document.createElement('div');
      card.className = 'card mb-4 entity-card shadow-sm';
      card.innerHTML = `
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-danger btn-sm remove-entity">Remove</button>
          </div>
          <div class="mb-3">
            <label class="form-label">Entity Name</label>
            <input type="text" name="entities[${index}][name]" class="form-control" required>
          </div>
          <div class="fields-wrapper"></div>
          <button type="button" class="btn btn-outline-secondary btn-sm add-field">+ Add Field</button>
        </div>
      `;

      wrapper.appendChild(card);
    });

    document.addEventListener('click', function (e) {
      if (e.target.classList.contains('add-field')) {
        const card = e.target.closest('.entity-card');
        const index = [...card.parentNode.children].indexOf(card);
        const wrapper = card.querySelector('.fields-wrapper');
        addField(index, wrapper);
      }

      if (e.target.classList.contains('remove-field')) {
        e.target.closest('.field-row').remove();
      }

      if (e.target.classList.contains('remove-entity')) {
        e.target.closest('.entity-card').remove();
      }
    });
  });
</script>
@endpush
