@extends('admin.layouts.app')
@section('title', 'Case Laws')
@section('heading', 'Case Laws')

@section('action_button')
<button type="button" onclick="openModal()"
        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
    Add Case Law
</button>
@endsection

@section('content')

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div class="flex-1 min-w-56">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search case name…"
                   class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status" class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="overruled" {{ request('status') === 'overruled' ? 'selected' : '' }}>Overruled</option>
        </select>
    </div>
    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">Filter</button>
    @if(request()->hasAny(['q','status']))
    <a href="{{ route('admin.case-laws.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Clear</a>
    @endif
</form>

<div class="text-xs text-gray-400 mb-3">{{ $caseLaws->total() }} case law(s)</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Case Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Updated</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($caseLaws as $case)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3.5 max-w-sm">
                    <p class="text-sm font-medium text-gray-900 line-clamp-2 cursor-help" data-tooltip="{{ $case->case_name }}">
                        {{ $case->case_name }}
                    </p>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $case->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($case->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-xs text-gray-400">
                    {{ $case->updated_at->format('d M Y') }}
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.case-laws.show', $case) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
                        <button type="button" onclick="openEditModal({{ $case->id }})"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
                        <form method="POST" action="{{ route('admin.case-laws.destroy', $case) }}" class="inline"
                              onsubmit="return confirm('Delete this case law? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['q','status']))
                        No cases match the current filters.
                        <a href="{{ route('admin.case-laws.index') }}" class="ml-1 text-indigo-600">Clear filters</a>
                    @else
                        No case laws yet. Click <strong>Add Case Law</strong> to get started.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $caseLaws->withQueryString()->links() }}</div>


{{-- ─── Modal ──────────────────────────────────────────────────────────────── --}}
<div id="cl-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(0,0,0,0.45)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-800" id="cl-modal-title">Add Case Law</h2>
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
                <input type="text" id="cl-case-name" placeholder="e.g. Perera v. Employer Ltd"
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
                <textarea id="cl-content" rows="14" placeholder="Paste the full case law content here…"
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
                Save
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const csrf      = document.querySelector('meta[name="csrf-token"]').content;
    const modal     = document.getElementById('cl-modal');
    const titleEl   = document.getElementById('cl-modal-title');
    const errorsBox = document.getElementById('cl-errors');
    const submitBtn = document.getElementById('cl-submit-btn');

    let editId = null;

    function clearErrors() { errorsBox.classList.add('hidden'); errorsBox.innerHTML = ''; }
    function showErrors(data) {
        const msgs = Object.values(data.errors ?? {}).flat();
        if (msgs.length) { errorsBox.innerHTML = msgs.map(m => `<p>${m}</p>`).join(''); errorsBox.classList.remove('hidden'); }
    }

    function resetForm() {
        editId = null;
        document.getElementById('cl-case-name').value = '';
        document.getElementById('cl-status').value    = 'active';
        document.getElementById('cl-content').value   = '';
        clearErrors();
    }

    window.openModal = function () {
        resetForm();
        titleEl.textContent   = 'Add Case Law';
        submitBtn.textContent = 'Save';
        modal.style.display   = 'flex';
    };

    window.openEditModal = async function (id) {
        resetForm();
        editId = id;
        titleEl.textContent   = 'Edit Case Law';
        submitBtn.textContent = 'Update';
        modal.style.display   = 'flex';
        try {
            const resp = await fetch(`/api/v1/case-laws/${id}`, { headers: { 'Accept': 'application/json' } });
            const c    = await resp.json();
            document.getElementById('cl-case-name').value = c.case_name ?? '';
            document.getElementById('cl-status').value    = c.status ?? 'active';
            document.getElementById('cl-content').value   = c.content ?? '';
        } catch {
            errorsBox.innerHTML = '<p>Could not load case law data.</p>';
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
        const url    = editId ? `/admin/case-laws/${editId}` : '/admin/case-laws';
        const method = editId ? 'PUT' : 'POST';
        try {
            const resp = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(payload),
            });
            const data = await resp.json();
            if (resp.ok) {
                window.location.href = editId ? `/admin/case-laws/${editId}` : `/admin/case-laws/${data.id}`;
            } else {
                showErrors(data);
            }
        } catch {
            errorsBox.innerHTML = '<p>Network error. Please try again.</p>';
            errorsBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    };
})();
</script>

@endsection
