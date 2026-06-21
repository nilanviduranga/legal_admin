@extends('admin.layouts.app')
@section('title', $caseLaw->case_name)
@section('heading', 'Case Law')

@section('breadcrumb')
    <a href="{{ route('admin.case-laws.index') }}" class="hover:text-indigo-600">Case Laws</a>
    <span>/</span>
    <span class="text-gray-600 truncate max-w-xs">{{ $caseLaw->case_name }}</span>
@endsection

@section('action_button')
<div class="flex items-center gap-2">
    <button type="button" onclick="openEditModal({{ $caseLaw->id }})"
            class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Edit
    </button>
    <form method="POST" action="{{ route('admin.case-laws.destroy', $caseLaw) }}" class="inline"
          onsubmit="return confirm('Delete this case law? This cannot be undone.')">
        @csrf @method('DELETE')
        <button type="submit" class="rounded-xl bg-red-50 border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-100 transition-colors">
            Delete
        </button>
    </form>
</div>
@endsection

@section('content')

{{-- Header --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <h2 class="text-xl font-bold text-gray-900 flex-1 min-w-0 leading-snug">{{ $caseLaw->case_name }}</h2>
        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold shrink-0
            {{ $caseLaw->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ ucfirst($caseLaw->status) }}
        </span>
    </div>
    <p class="text-xs text-gray-400 mt-2">Updated {{ $caseLaw->updated_at->format('d M Y H:i') }}</p>
</div>

{{-- Content --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Content</h3>
    <div class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $caseLaw->content }}</div>
</div>


{{-- Edit Modal --}}
<div id="cl-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-800">Edit Case Law</h2>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            <div id="cl-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 space-y-0.5"></div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Case Name <span class="text-red-400">*</span></label>
                <input type="text" id="cl-case-name"
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select id="cl-status" class="rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    <option value="active">Active</option>
                    <option value="overruled">Overruled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Content <span class="text-red-400">*</span></label>
                <textarea id="cl-content" rows="14"
                          class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors resize-y"></textarea>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 shrink-0">
            <button type="button" onclick="closeModal()"
                    class="rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" id="cl-submit-btn" onclick="submitCaseLaw()"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition-all">
                Update
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const csrf      = document.querySelector('meta[name="csrf-token"]').content;
    const modal     = document.getElementById('cl-modal');
    const errorsBox = document.getElementById('cl-errors');
    const submitBtn = document.getElementById('cl-submit-btn');
    const editId    = {{ $caseLaw->id }};

    function clearErrors() { errorsBox.classList.add('hidden'); errorsBox.innerHTML = ''; }
    function showErrors(data) {
        const msgs = Object.values(data.errors ?? {}).flat();
        if (msgs.length) { errorsBox.innerHTML = msgs.map(m => `<p>${m}</p>`).join(''); errorsBox.classList.remove('hidden'); }
    }

    window.openEditModal = async function (id) {
        clearErrors();
        modal.style.display = 'flex';
        try {
            const resp = await fetch(`/api/v1/case-laws/${id}`, { headers: { 'Accept': 'application/json' } });
            const c    = await resp.json();
            document.getElementById('cl-case-name').value = c.case_name ?? '';
            document.getElementById('cl-status').value    = c.status ?? 'active';
            document.getElementById('cl-content').value   = c.content ?? '';
        } catch {
            errorsBox.innerHTML = '<p>Could not load data.</p>';
            errorsBox.classList.remove('hidden');
        }
    };

    window.closeModal = function () { modal.style.display = 'none'; };
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

    window.submitCaseLaw = async function () {
        clearErrors();
        submitBtn.disabled = true;
        const payload = {
            case_name: document.getElementById('cl-case-name').value.trim(),
            status:    document.getElementById('cl-status').value,
            content:   document.getElementById('cl-content').value.trim(),
        };
        try {
            const resp = await fetch(`/admin/case-laws/${editId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(payload),
            });
            const data = await resp.json();
            if (resp.ok) window.location.reload();
            else showErrors(data);
        } catch {
            errorsBox.innerHTML = '<p>Network error. Please try again.</p>';
            errorsBox.classList.remove('hidden');
        } finally { submitBtn.disabled = false; }
    };
})();
</script>

@endsection
