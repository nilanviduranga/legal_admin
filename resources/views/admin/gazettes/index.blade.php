@extends('admin.layouts.app')
@section('title', 'Gazettes')
@section('heading', 'Gazettes')

@section('content')

{{-- Add / Edit Form Panel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6" id="gazette-form-panel">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-2 h-2 rounded-full bg-indigo-500" id="form-dot"></div>
        <h2 class="text-sm font-semibold text-gray-700" id="form-heading">Add New Gazette</h2>
    </div>

    <div id="form-errors" class="hidden mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 space-y-0.5"></div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Gazette No</label>
            <input type="text" id="f-gazette-no" placeholder="e.g. 2187/10"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="gazette_no"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
            <input type="date" id="f-gazette-date"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="gazette_date"></p>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
            <input type="text" id="f-title" placeholder="Gazette title…"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error text-xs text-red-500 mt-1 hidden" data-field="title"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select id="f-status"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div class="sm:col-span-3 flex items-end gap-3 pt-1">
            <button type="button" id="gazette-submit-btn"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition-all">
                Add
            </button>
            <button type="button" id="gazette-cancel-btn"
                    class="hidden rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 items-end mb-5">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Gazette no. or title…"
                   class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-4 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
        <select name="status"
                class="rounded-xl border-gray-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pr-8">
            <option value="">All</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
        Filter
    </button>
    @if(request()->hasAny(['q','status']))
    <a href="{{ route('admin.gazettes.index') }}"
       class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
        Clear
    </a>
    @endif
</form>

<div class="text-xs text-gray-400 mb-3" id="gazette-count">{{ $gazettes->total() }} gazette(s)</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Gazette No</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Title</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Acts</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="gazettes-tbody">
            @forelse($gazettes as $gazette)
            <tr id="gazette-row-{{ $gazette->gazette_id }}"
                data-gazette-id="{{ $gazette->gazette_id }}"
                data-gazette-no="{{ $gazette->gazette_no }}"
                data-gazette-date="{{ $gazette->gazette_date->format('Y-m-d') }}"
                data-title="{{ $gazette->title }}"
                data-status="{{ $gazette->status }}"
                class="hover:bg-gray-50 transition-colors">
                <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-gray-900">{{ $gazette->gazette_no }}</td>
                <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-500">{{ $gazette->gazette_date->format('d M Y') }}</td>
                <td class="px-5 py-3.5 text-sm text-gray-700 max-w-xs">
                    <span class="line-clamp-2">{{ $gazette->title }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">
                        {{ $gazette->acts_count }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $gazette->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($gazette->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.gazettes.show', $gazette) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
                        <button type="button"
                                onclick="gazetteEditRow(this.closest('tr'))"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
                        <form method="POST" action="{{ route('admin.gazettes.destroy', $gazette) }}" class="inline"
                              onsubmit="return confirm('Delete gazette {{ $gazette->gazette_no }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="gazettes-empty-row">
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['q','status']))
                        No gazettes match the current filters.
                        <a href="{{ route('admin.gazettes.index') }}" class="ml-1 text-indigo-600">Clear filters</a>
                    @else
                        No gazettes yet. Use the form above to add one.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">{{ $gazettes->withQueryString()->links() }}</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let editId = null;

    const fNo     = document.getElementById('f-gazette-no');
    const fDate   = document.getElementById('f-gazette-date');
    const fTitle  = document.getElementById('f-title');
    const fStatus = document.getElementById('f-status');
    const submitBtn  = document.getElementById('gazette-submit-btn');
    const cancelBtn  = document.getElementById('gazette-cancel-btn');
    const heading    = document.getElementById('form-heading');
    const dot        = document.getElementById('form-dot');
    const errorsBox  = document.getElementById('form-errors');

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function fmtDate(ymd) {
        const [y, m, d] = ymd.split('-');
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return `${parseInt(d, 10)} ${months[parseInt(m, 10) - 1]} ${y}`;
    }

    function clearErrors() {
        errorsBox.classList.add('hidden');
        errorsBox.innerHTML = '';
        document.querySelectorAll('#gazette-form-panel .field-error').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    function showErrors(data) {
        const fieldErrors = data.errors ?? {};
        const all = Object.values(fieldErrors).flat();
        if (all.length) {
            errorsBox.innerHTML = all.map(m => `<p>${esc(m)}</p>`).join('');
            errorsBox.classList.remove('hidden');
        }
        document.querySelectorAll('#gazette-form-panel .field-error').forEach(el => {
            const msgs = fieldErrors[el.dataset.field];
            if (msgs?.length) {
                el.textContent = msgs[0];
                el.classList.remove('hidden');
            }
        });
    }

    function resetForm() {
        fNo.value     = '';
        fDate.value   = '';
        fTitle.value  = '';
        fStatus.value = 'active';
        editId = null;
        heading.textContent = 'Add New Gazette';
        dot.classList.remove('bg-amber-400');
        dot.classList.add('bg-indigo-500');
        submitBtn.textContent = 'Add';
        cancelBtn.classList.add('hidden');
        clearErrors();
    }

    function statusBadge(status) {
        return status === 'active'
            ? `<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-700">Active</span>`
            : `<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-500">Inactive</span>`;
    }

    function buildRow(g) {
        const gNo    = esc(g.gazette_no);
        const gTitle = esc(g.title);
        const gStat  = esc(g.status);
        const gDate  = esc(g.gazette_date);
        const confirmMsg = esc(`Delete gazette ${g.gazette_no}? This cannot be undone.`);
        return `
<tr id="gazette-row-${g.gazette_id}"
    data-gazette-id="${g.gazette_id}"
    data-gazette-no="${gNo}"
    data-gazette-date="${gDate}"
    data-title="${gTitle}"
    data-status="${gStat}"
    class="hover:bg-gray-50 transition-colors">
  <td class="whitespace-nowrap px-5 py-3.5 text-sm font-semibold text-gray-900">${gNo}</td>
  <td class="whitespace-nowrap px-5 py-3.5 text-sm text-gray-500">${fmtDate(g.gazette_date)}</td>
  <td class="px-5 py-3.5 text-sm text-gray-700 max-w-xs"><span class="line-clamp-2">${gTitle}</span></td>
  <td class="whitespace-nowrap px-4 py-3.5">
    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-600">${g.acts_count ?? 0}</span>
  </td>
  <td class="whitespace-nowrap px-4 py-3.5">${statusBadge(g.status)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-right">
    <div class="flex items-center justify-end gap-3">
      <a href="/admin/gazettes/${g.gazette_id}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View</a>
      <button type="button" onclick="gazetteEditRow(this.closest('tr'))" class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
      <form method="POST" action="/admin/gazettes/${g.gazette_id}" class="inline" onsubmit="return confirm('${confirmMsg}')">
        <input type="hidden" name="_token" value="${esc(csrf)}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
      </form>
    </div>
  </td>
</tr>`;
    }

    submitBtn.addEventListener('click', async function () {
        clearErrors();
        submitBtn.disabled = true;

        const payload = {
            gazette_no:   fNo.value.trim(),
            gazette_date: fDate.value,
            title:        fTitle.value.trim(),
            status:       fStatus.value,
        };

        const url    = editId ? `/admin/gazettes/${editId}` : '/admin/gazettes';
        const method = editId ? 'PUT' : 'POST';

        try {
            const resp = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });

            if (resp.ok) {
                const gazette = await resp.json();
                const tbody    = document.getElementById('gazettes-tbody');
                const emptyRow = document.getElementById('gazettes-empty-row');

                if (editId) {
                    const existing = document.getElementById(`gazette-row-${editId}`);
                    if (existing) existing.outerHTML = buildRow(gazette);
                } else {
                    if (emptyRow) emptyRow.remove();
                    tbody.insertAdjacentHTML('afterbegin', buildRow(gazette));
                    const countEl = document.getElementById('gazette-count');
                    if (countEl) {
                        const cur = parseInt(countEl.textContent, 10) || 0;
                        countEl.textContent = (cur + 1) + ' gazette(s)';
                    }
                }
                resetForm();
            } else {
                showErrors(await resp.json());
            }
        } catch {
            errorsBox.innerHTML = '<p>Network error. Please try again.</p>';
            errorsBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    });

    cancelBtn.addEventListener('click', resetForm);

    window.gazetteEditRow = function (row) {
        editId = row.dataset.gazetteId;
        fNo.value     = row.dataset.gazetteNo;
        fDate.value   = row.dataset.gazetteDate;
        fTitle.value  = row.dataset.title;
        fStatus.value = row.dataset.status;

        heading.textContent = 'Edit Gazette';
        dot.classList.remove('bg-indigo-500');
        dot.classList.add('bg-amber-400');
        submitBtn.textContent = 'Update';
        cancelBtn.classList.remove('hidden');
        clearErrors();
        document.getElementById('gazette-form-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    };
})();
</script>

@endsection
