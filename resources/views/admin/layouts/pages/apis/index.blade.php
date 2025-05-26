@extends('admin.layouts.master')

@section('content')
<div class="container mt-5">
  <h1 class="mb-4 fw-bold text-center">Your API Auth Token</h1>
  <div class="input-group mb-5 shadow-sm">
    <input type="text" id="mainToken" class="form-control form-control-lg" value="{{ $token }}" readonly>
    <button class="btn btn-warning copy-btn" data-target="mainToken">Copy</button>
  </div>

  <h2 class="mb-4 fw-semibold text-center">Projects</h2>
  <div class="row">
    @forelse ($projects as $index => $project)
      <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-semibold">{{ $project->name }}</h5>
            <div class="input-group mb-2">
              <input type="text" id="tokenInput{{ $index }}" class="form-control" value="{{ $project->token }}" readonly>
              <button class="btn btn-outline-primary copy-btn" data-target="tokenInput{{ $index }}">Copy</button>
            </div>

            <h6 class="fw-bold mt-3">API Endpoints</h6>
            <ul class="list-group list-group-flush small mb-3">
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <strong>Get All Projects</strong><br>
                  GET <code id="endpoint{{ $index }}-1">http://localhost:8000/api/all-projects</code>
                </div>
                <button class="btn btn-sm btn-outline-secondary copy-btn mt-1" data-target="endpoint{{ $index }}-1">Copy</button>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <strong>Get Current Project</strong><br>
                  <code id="endpoint{{ $index }}-2">GET http://localhost:8000/api/project</code>
                </div>
                <button class="btn btn-sm btn-outline-secondary copy-btn mt-1" data-target="endpoint{{ $index }}-2">Copy</button>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <strong>Get Project Tables</strong><br>
                  <code id="endpoint{{ $index }}-3">GET http://localhost:8000/api/project/tables</code>
                </div>
                <button class="btn btn-sm btn-outline-secondary copy-btn mt-1" data-target="endpoint{{ $index }}-3">Copy</button>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div>
                  <strong>Get Table Data</strong><br>
                  <code id="endpoint{{ $index }}-4">GET http://localhost:8000/api/project/table</code>
                </div>
                <button class="btn btn-sm btn-outline-secondary copy-btn mt-1" data-target="endpoint{{ $index }}-4">Copy</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    @empty
      <p class="text-muted text-center">No projects available.</p>
    @endforelse

    {{-- Usage Example Section --}}
    <div class="col-12 mt-5">
      <h4 class="mb-3">Example: How to Use the API</h4>
      <p>This JavaScript example shows how you can call the <code>GET /api/all-projects</code> endpoint and log the result:</p>
      <pre style="background:#f8f9fa; padding: 15px; border-radius: 6px; font-size: 14px; overflow-x:auto;">
<code>
const token = '{{ $token }}';

fetch('http://localhost:8000/api/all-projects', {
  headers: {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Project-Token': 'YOUR_PROJECT_TOKEN_HERE'
  }
})
.then(response =&gt; response.json())
.then(data =&gt; console.log('All projects:', data))
.catch(error =&gt; console.error('Error:', error));
      </pre>
    </div>
  </div>
</div>

<!-- Copy Toast -->
<div id="copy-toast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050; display: none;">
  <div class="toast align-items-center text-white bg-success border-0 show">
    <div class="d-flex">
      <div class="toast-body">
        Copied to clipboard!
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .toast {
    opacity: 0.95;
  }
</style>
@endpush

@push('scripts')
<script>
  document.querySelectorAll('.copy-btn').forEach(button => {
    button.addEventListener('click', async function () {
      const targetId = this.getAttribute('data-target');
      const target = document.getElementById(targetId);
      if (!target) return;

      const textToCopy = target.value || target.innerText;

      try {
        await navigator.clipboard.writeText(textToCopy);

        const toast = document.getElementById('copy-toast');
        toast.style.display = 'block';

        setTimeout(() => {
          toast.style.display = 'none';
        }, 1200);
      } catch (err) {
        console.error("Copy failed:", err);
      }
    });
  });
</script>
@endpush
