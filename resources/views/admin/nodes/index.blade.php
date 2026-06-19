@extends('admin.layouts.app')
@section('title', 'Nodes — ' . ($act->short_title ?? $act->title))
@section('heading', 'Node Manager')

@section('breadcrumb')
    <a href="{{ route('admin.acts.index') }}" class="hover:text-indigo-600 transition-colors">Acts</a>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.acts.show', $act) }}" class="hover:text-indigo-600 transition-colors truncate max-w-xs">
        Act {{ $act->act_no }}/{{ $act->act_year }}
    </a>
    <span class="mx-1">›</span>
    <span class="text-gray-600">Nodes</span>
@endsection

@section('action_button')
<a href="{{ route('admin.acts.show', $act) }}"
   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Back to Act
</a>
@endsection

@section('content')

{{-- Act summary strip --}}
<div class="mb-5 flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
    <div class="flex-1 min-w-0">
        <p class="text-xs text-gray-400 mb-0.5">Act {{ $act->act_no }} of {{ $act->act_year }}</p>
        <p class="text-sm font-semibold text-gray-900 truncate">{{ $act->title }}</p>
    </div>
    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold shrink-0
        {{ $act->status === 'active' ? 'bg-green-100 text-green-700' :
           ($act->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
        {{ ucfirst($act->status) }}
    </span>
    <span class="text-xs text-gray-400 shrink-0" id="top-node-count">{{ $nodes->count() }} top-level node(s)</span>
</div>

{{-- ── Inline Add / Edit Form ─────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6" id="node-form-panel">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-2 h-2 rounded-full bg-indigo-500" id="nf-dot"></div>
        <h2 class="text-sm font-semibold text-gray-700" id="nf-heading">Add Top-Level Node</h2>
    </div>

    <div id="nf-errors" class="hidden mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 space-y-0.5"></div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
            <select id="nf-type"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                @foreach(['PREAMBLE','DEFINITIONS','PART','CHAPTER','SECTION','SUBSECTION','PARAGRAPH','SUBPARAGRAPH','ITEM','SCHEDULE'] as $t)
                    <option value="{{ $t }}" {{ $t === 'PART' ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="node_type"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">No. <span class="font-normal text-gray-400">(optional)</span></label>
            <input type="text" id="nf-no" placeholder="e.g. I, 1, A"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="node_no"></p>
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium text-gray-500 mb-1">Heading <span class="font-normal text-gray-400">(optional)</span></label>
            <input type="text" id="nf-heading-input" placeholder="e.g. Preliminary Provisions"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="heading"></p>
        </div>
        <div class="col-span-2 sm:col-span-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">Text Content <span class="font-normal text-gray-400">(for leaf nodes)</span></label>
            <textarea id="nf-text" rows="3" placeholder="Substantive legal text…"
                      class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors resize-y font-mono"></textarea>
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="text_content"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sort Order</label>
            <input type="number" id="nf-sort" value="{{ $nextSortOrder }}" min="0"
                   class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
            <p class="field-error hidden text-xs text-red-500 mt-1" data-field="sort_order"></p>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select id="nf-status"
                    class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                <option value="active">Active</option>
                <option value="amended">Amended</option>
                <option value="repealed">Repealed</option>
            </select>
        </div>
        <div class="col-span-2 flex items-end gap-3 pt-1">
            <button type="button" id="nf-submit"
                    class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 active:scale-95 transition-all">
                Add
            </button>
            <button type="button" id="nf-cancel"
                    class="hidden rounded-xl border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- ── Top-level nodes list ────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-3.5 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Top-Level Nodes</h2>
        <p class="text-xs text-gray-400 mt-0.5">Click <strong>Edit</strong> to update a node inline above · Click <strong>View →</strong> to manage its children</p>
    </div>
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-36">Type</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">No.</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Heading / Content</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-20">Order</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Status</th>
                <th class="px-4 py-3 w-40"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50" id="top-nodes-tbody">
            @forelse($nodes as $node)
            @php
            $typeBadge = match($node->node_type) {
                'PART'         => 'bg-indigo-100 text-indigo-800',
                'CHAPTER'      => 'bg-purple-100 text-purple-800',
                'SECTION'      => 'bg-blue-100 text-blue-800',
                'SUBSECTION'   => 'bg-cyan-100 text-cyan-800',
                'PARAGRAPH'    => 'bg-teal-100 text-teal-800',
                'SUBPARAGRAPH' => 'bg-green-100 text-green-800',
                'ITEM'         => 'bg-lime-100 text-lime-800',
                'SCHEDULE'     => 'bg-amber-100 text-amber-800',
                'PREAMBLE'     => 'bg-orange-100 text-orange-800',
                default        => 'bg-gray-100 text-gray-600',
            };
            $preview = $node->heading ?? Str::limit($node->text_content ?? '', 60);
            $tooltip = $node->heading
                ? ($node->text_content ? $node->heading . "\n\n" . $node->text_content : $node->heading)
                : ($node->text_content ?? '');
            @endphp
            <tr id="top-node-row-{{ $node->node_id }}"
                class="hover:bg-gray-50 transition-colors"
                data-node="{{ json_encode([
                    'node_id'      => $node->node_id,
                    'node_type'    => $node->node_type,
                    'node_no'      => $node->node_no,
                    'heading'      => $node->heading,
                    'text_content' => $node->text_content,
                    'sort_order'   => $node->sort_order,
                    'level'        => $node->level,
                    'status'       => $node->status,
                ]) }}">
                <td class="whitespace-nowrap px-5 py-3.5">
                    <span class="inline-flex rounded px-2 py-0.5 text-xs font-medium {{ $typeBadge }}">{{ $node->node_type }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-gray-700">{{ $node->node_no ?? '—' }}</td>
                <td class="px-4 py-3.5 text-sm text-gray-700 max-w-xs">
                    <span class="line-clamp-2 cursor-help" data-tooltip="{{ $tooltip }}">{{ $preview ?: '—' }}</span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-sm text-gray-400">{{ $node->sort_order }}</td>
                <td class="whitespace-nowrap px-4 py-3.5">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $node->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($node->status === 'repealed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($node->status) }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-4 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.nodes.show', $node) }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View →</a>
                        <button type="button" onclick="nodeIndexEdit(this.closest('tr'))"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
                        <form method="POST" action="{{ route('admin.nodes.destroy', $node) }}" class="inline"
                              onsubmit="return confirm('Delete this node and all its children? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr id="top-nodes-empty">
                <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                    No top-level nodes yet. Use the form above to add the first one.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── Full structure tree (read-only navigation) ─────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">Full Structure</h2>
            <p class="text-xs text-gray-400 mt-0.5">Navigation view — reflects the state at page load</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.querySelectorAll('#struct-tree details').forEach(d=>d.open=true)"
                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors">
                Expand all
            </button>
            <button onclick="document.querySelectorAll('#struct-tree details').forEach(d=>d.open=false)"
                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors">
                Collapse all
            </button>
        </div>
    </div>
    <div class="p-3" id="struct-tree">
        @forelse($nodes as $node)
            @include('admin.nodes._node', ['node' => $node, 'depth' => 0])
        @empty
        <div class="py-12 text-center">
            <p class="text-sm text-gray-400">This act has no nodes yet.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
(function () {
    const csrf       = document.querySelector('meta[name="csrf-token"]').content;
    const ACT_ID     = {{ $act->act_id }};
    const BASE_SORT  = {{ $nextSortOrder }};
    let editId       = null;
    let nextSort     = BASE_SORT;

    const fType    = document.getElementById('nf-type');
    const fNo      = document.getElementById('nf-no');
    const fHdg     = document.getElementById('nf-heading-input');
    const fText    = document.getElementById('nf-text');
    const fSort    = document.getElementById('nf-sort');
    const fStatus  = document.getElementById('nf-status');
    const submitBtn = document.getElementById('nf-submit');
    const cancelBtn = document.getElementById('nf-cancel');
    const formHdg  = document.getElementById('nf-heading');
    const dot      = document.getElementById('nf-dot');
    const errBox   = document.getElementById('nf-errors');

    const TYPE_CLS = {
        PART:'bg-indigo-100 text-indigo-800', CHAPTER:'bg-purple-100 text-purple-800',
        SECTION:'bg-blue-100 text-blue-800', SUBSECTION:'bg-cyan-100 text-cyan-800',
        PARAGRAPH:'bg-teal-100 text-teal-800', SUBPARAGRAPH:'bg-green-100 text-green-800',
        ITEM:'bg-lime-100 text-lime-800', SCHEDULE:'bg-amber-100 text-amber-800',
        PREAMBLE:'bg-orange-100 text-orange-800', DEFINITIONS:'bg-gray-100 text-gray-600',
    };

    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function typeBadge(type) {
        const cls = TYPE_CLS[type] ?? 'bg-gray-100 text-gray-600';
        return `<span class="inline-flex rounded px-2 py-0.5 text-xs font-medium ${cls}">${esc(type)}</span>`;
    }

    function statusBadge(s) {
        const map = {active:'bg-green-100 text-green-700', repealed:'bg-red-100 text-red-700', amended:'bg-amber-100 text-amber-700'};
        return `<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${map[s]??'bg-gray-100 text-gray-500'}">${esc(s.charAt(0).toUpperCase()+s.slice(1))}</span>`;
    }

    function clearErrors() {
        errBox.classList.add('hidden'); errBox.innerHTML = '';
        document.querySelectorAll('#node-form-panel .field-error').forEach(el => { el.textContent=''; el.classList.add('hidden'); });
    }

    function showErrors(data) {
        const fe = data.errors ?? {};
        const all = Object.values(fe).flat();
        if (all.length) { errBox.innerHTML = all.map(m=>`<p>${esc(m)}</p>`).join(''); errBox.classList.remove('hidden'); }
        document.querySelectorAll('#node-form-panel .field-error').forEach(el => {
            const msgs = fe[el.dataset.field];
            if (msgs?.length) { el.textContent = msgs[0]; el.classList.remove('hidden'); }
        });
    }

    function resetForm() {
        fType.value = 'PART'; fNo.value=''; fHdg.value=''; fText.value='';
        fSort.value = nextSort; fStatus.value='active';
        editId = null;
        formHdg.textContent = 'Add Top-Level Node';
        dot.className = 'w-2 h-2 rounded-full bg-indigo-500';
        submitBtn.textContent = 'Add';
        cancelBtn.classList.add('hidden');
        clearErrors();
    }

    function buildRow(n) {
        const preview  = n.heading ?? (n.text_content ? n.text_content.substring(0,60)+(n.text_content.length>60?'…':'') : '—');
        const tooltip  = (n.heading && n.text_content) ? n.heading + '\n\n' + n.text_content : (n.heading ?? n.text_content ?? '');
        const nodeJson = esc(JSON.stringify({node_id:n.node_id,node_type:n.node_type,node_no:n.node_no,heading:n.heading,text_content:n.text_content,sort_order:n.sort_order,level:n.level,status:n.status}));
        const confirmTxt = esc('Delete this node and all its children? This cannot be undone.');
        return `
<tr id="top-node-row-${n.node_id}" class="hover:bg-gray-50 transition-colors" data-node="${nodeJson}">
  <td class="whitespace-nowrap px-5 py-3.5">${typeBadge(n.node_type)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-sm font-medium text-gray-700">${esc(n.node_no??'—')}</td>
  <td class="px-4 py-3.5 text-sm text-gray-700 max-w-xs"><span class="line-clamp-2 cursor-help" data-tooltip="${esc(tooltip)}">${esc(preview)}</span></td>
  <td class="whitespace-nowrap px-4 py-3.5 text-sm text-gray-400">${esc(n.sort_order)}</td>
  <td class="whitespace-nowrap px-4 py-3.5">${statusBadge(n.status)}</td>
  <td class="whitespace-nowrap px-4 py-3.5 text-right">
    <div class="flex items-center justify-end gap-3">
      <a href="/admin/nodes/${n.node_id}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View →</a>
      <button type="button" onclick="nodeIndexEdit(this.closest('tr'))" class="text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Edit</button>
      <form method="POST" action="/admin/nodes/${n.node_id}" class="inline" onsubmit="return confirm('${confirmTxt}')">
        <input type="hidden" name="_token" value="${esc(csrf)}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Delete</button>
      </form>
    </div>
  </td>
</tr>`;
    }

    submitBtn.addEventListener('click', async function () {
        clearErrors(); submitBtn.disabled = true;

        const payload = {
            act_id:         ACT_ID,
            parent_node_id: null,
            node_type:      fType.value,
            node_no:        fNo.value.trim() || null,
            heading:        fHdg.value.trim() || null,
            text_content:   fText.value.trim() || null,
            sort_order:     parseInt(fSort.value, 10) || 0,
            level:          1,
            status:         fStatus.value,
        };

        const url    = editId ? `/admin/nodes/${editId}` : '/admin/nodes';
        const method = editId ? 'PUT' : 'POST';

        try {
            const resp = await fetch(url, {
                method,
                headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
                body: JSON.stringify(payload),
            });

            if (resp.ok) {
                const node  = await resp.json();
                const tbody = document.getElementById('top-nodes-tbody');
                const empty = document.getElementById('top-nodes-empty');

                if (editId) {
                    const row = document.getElementById(`top-node-row-${editId}`);
                    if (row) row.outerHTML = buildRow(node);
                } else {
                    if (empty) empty.remove();
                    tbody.insertAdjacentHTML('beforeend', buildRow(node));
                    nextSort = payload.sort_order + 1;
                    const countEl = document.getElementById('top-node-count');
                    if (countEl) countEl.textContent = (parseInt(countEl.textContent)||0)+1 + ' top-level node(s)';
                }
                resetForm();
            } else {
                showErrors(await resp.json());
            }
        } catch {
            errBox.innerHTML = '<p>Network error. Please try again.</p>';
            errBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    });

    cancelBtn.addEventListener('click', resetForm);

    window.nodeIndexEdit = function (row) {
        const n = JSON.parse(row.dataset.node);
        editId = n.node_id;
        fType.value   = n.node_type;
        fNo.value     = n.node_no ?? '';
        fHdg.value    = n.heading ?? '';
        fText.value   = n.text_content ?? '';
        fSort.value   = n.sort_order;
        fStatus.value = n.status;

        formHdg.textContent = 'Edit Node';
        dot.className = 'w-2 h-2 rounded-full bg-amber-400';
        submitBtn.textContent = 'Update';
        cancelBtn.classList.remove('hidden');
        clearErrors();
        document.getElementById('node-form-panel').scrollIntoView({behavior:'smooth',block:'start'});
    };
})();
</script>

@endsection
