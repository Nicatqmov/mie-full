@extends('admin.layouts.master')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Create New Project</h5>
                </div>
                <div class="card-body">
                    <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST">
                        @csrf

                        <!-- Project Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Entities Section -->
                        <div class="mb-3">
                            <label class="form-label">Entities</label>
                            <button type="button" class="btn btn-sm btn-info" id="addEntityBtn">+ Add Entity</button>
                            <div id="entitiesContainer" class="mt-3"></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Create Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Adding Fields -->
<div class="modal fade" id="addFieldModal" tabindex="-1" aria-labelledby="addFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFieldModalLabel">Add Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="selectedEntityId">
                <div class="mb-3">
                    <label for="fieldName" class="form-label">Field Name</label>
                    <input type="text" class="form-control" id="fieldName" placeholder="Enter field name" required>
                </div>
                <div class="mb-3">
                    <label for="fieldType" class="form-label">Field Type</label>
                    <select class="form-control" id="fieldType" required>
                        @foreach ($dataTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveFieldBtn">Save Field</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createProjectForm');
    const submitBtn = document.getElementById('submitBtn');
    const entitiesContainer = document.getElementById('entitiesContainer');
    const addEntityBtn = document.getElementById('addEntityBtn');
    const fieldModal = new bootstrap.Modal(document.getElementById('addFieldModal'));
    let entityCount = 0;

    addEntityBtn.addEventListener('click', function() {
        entityCount++;
        const entityHTML = `
            <div class="card mt-3" id="entity-${entityCount}">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Entity: <input type="text" name="entities[${entityCount}][name]" class="form-control d-inline w-50" placeholder="Entity Name" required></span>
                        <button type="button" class="btn btn-danger btn-sm removeEntity" data-id="${entityCount}">X</button>
                    </div>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-success openFieldModal" data-id="${entityCount}">+ Add Field</button>
                    <div class="mt-2" id="fieldsContainer-${entityCount}"></div>
                </div>
            </div>
        `;
        entitiesContainer.insertAdjacentHTML('beforeend', entityHTML);
    });

    entitiesContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('openFieldModal')) {
            const entityId = event.target.getAttribute('data-id');
            document.getElementById('selectedEntityId').value = entityId;
            fieldModal.show();
        }

        if (event.target.classList.contains('removeEntity')) {
            const id = event.target.getAttribute('data-id');
            document.getElementById(`entity-${id}`).remove();
        }

        if (event.target.classList.contains('removeField')) {
            const id = event.target.getAttribute('data-id');
            document.getElementById(`field-${id}`).remove();
        }
    });

    document.getElementById('saveFieldBtn').addEventListener('click', function() {
        const entityId = document.getElementById('selectedEntityId').value;
        const fieldName = document.getElementById('fieldName').value.trim();
        const fieldType = document.getElementById('fieldType').value;
        const fieldsContainer = document.getElementById(`fieldsContainer-${entityId}`);

        if (!fieldName) {
            alert('Field name is required!');
            return;
        }

        const fieldIndex = fieldsContainer.children.length;

        const fieldHTML = `
            <div class="input-group mb-2" id="field-${entityId}-${fieldIndex}">
                <input type="text" name="entities[${entityId}][fields][${fieldIndex}][name]" class="form-control" value="${fieldName}" readonly>
                <input type="hidden" name="entities[${entityId}][fields][${fieldIndex}][type]" value="${fieldType}">
                <button type="button" class="btn btn-danger btn-sm removeField" data-id="${entityId}-${fieldIndex}">X</button>
            </div>
        `;
        fieldsContainer.insertAdjacentHTML('beforeend', fieldHTML);

        document.getElementById('fieldName').value = '';
        document.getElementById('fieldType').value = '{{ $dataTypes->first()->id }}';
        fieldModal.hide();
    });

    form.addEventListener('submit', function(e) {
        if (entitiesContainer.children.length === 0) {
            alert('Please add at least one entity to the project.');
            e.preventDefault();
        }
    });
});
</script>
@endpush
