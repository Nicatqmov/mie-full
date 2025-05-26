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
                    <form action="{{ route('projects.store') }}" method="POST">
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
                            <button type="submit" class="btn btn-primary">Create Project</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Adding Fields -->
<div class="modal fade" id="fieldModal" tabindex="-1" aria-labelledby="fieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fieldModalLabel">Add Field to Entity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="selectedEntityId">

                <div class="mb-3">
                    <label for="fieldName" class="form-label">Field Name</label>
                    <input type="text" class="form-control" id="fieldName" placeholder="Enter field name">
                </div>

                <div class="mb-3">
                    <label for="fieldType" class="form-label">Field Type</label>
                    <select class="form-control" id="fieldType">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="boolean">Boolean</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fieldValidation" class="form-label">Validation (Optional)</label>
                    <input type="text" class="form-control" id="fieldValidation" placeholder="E.g., required, max:255">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveFieldBtn">Save Field</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Dynamic Entities & Fields -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const entitiesContainer = document.getElementById("entitiesContainer");
        const addEntityBtn = document.getElementById("addEntityBtn");
        let entityCount = 0;

        // Add new entity dynamically
        addEntityBtn.addEventListener("click", function() {
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
                        <button type="button" class="btn btn-sm btn-success openFieldModal" data-id="${entityCount}" data-bs-toggle="modal" data-bs-target="#fieldModal">+ Add Field</button>
                        <div class="mt-2" id="fieldsContainer-${entityCount}"></div>
                    </div>
                </div>
            `;
            entitiesContainer.insertAdjacentHTML("beforeend", entityHTML);
        });

        // Open field modal with selected entity ID
        let selectedEntityId;
        entitiesContainer.addEventListener("click", function(event) {
            if (event.target.classList.contains("openFieldModal")) {
                selectedEntityId = event.target.getAttribute("data-id");
                document.getElementById("selectedEntityId").value = selectedEntityId;
            }
        });

        // Save field inside the selected entity
        document.getElementById("saveFieldBtn").addEventListener("click", function() {
            const entityId = document.getElementById("selectedEntityId").value;
            const fieldName = document.getElementById("fieldName").value;
            const fieldType = document.getElementById("fieldType").value;
            const fieldValidation = document.getElementById("fieldValidation").value;
            const fieldsContainer = document.getElementById(`fieldsContainer-${entityId}`);

            if (!fieldName) {
                alert("Field name is required!");
                return;
            }

            const fieldHTML = `
                <div class="input-group mb-2" id="field-${entityId}-${fieldName}">
                    <input type="text" name="entities[${entityId}][fields][${fieldName}][name]" class="form-control" value="${fieldName}" readonly>
                    <input type="hidden" name="entities[${entityId}][fields][${fieldName}][type]" value="${fieldType}">
                    <input type="hidden" name="entities[${entityId}][fields][${fieldName}][validation]" value="${fieldValidation}">
                    <button type="button" class="btn btn-danger btn-sm removeField" data-id="${entityId}-${fieldName}">X</button>
                </div>
            `;
            fieldsContainer.insertAdjacentHTML("beforeend", fieldHTML);

            // Close modal and reset inputs
            document.getElementById("fieldName").value = "";
            document.getElementById("fieldType").value = "text";
            document.getElementById("fieldValidation").value = "";
            document.querySelector("#fieldModal .btn-close").click();
        });

        // Remove entity
        entitiesContainer.addEventListener("click", function(event) {
            if (event.target.classList.contains("removeEntity")) {
                const id = event.target.getAttribute("data-id");
                document.getElementById(`entity-${id}`).remove();
            }
        });

        // Remove field
        entitiesContainer.addEventListener("click", function(event) {
            if (event.target.classList.contains("removeField")) {
                const id = event.target.getAttribute("data-id");
                document.getElementById(`field-${id}`).remove();
            }
        });

    });
</script>

@endsection
