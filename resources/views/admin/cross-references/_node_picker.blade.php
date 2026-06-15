{{--
  Reusable act → node picker.
  Required vars:
    $pickerPrefix   : 'source' or 'target'
    $acts           : collection of Act
    $selectedActId  : pre-selected act_id (nullable)
    $selectedNodeId : pre-selected node_id (nullable)
--}}
@php
    $nodeField  = $pickerPrefix . '_node_id';
    $actSelId   = 'picker_act_'  . $pickerPrefix;
    $nodeSelId  = 'picker_node_' . $pickerPrefix;
    $label      = $pickerPrefix === 'source' ? 'Source' : 'Target';
@endphp

<div class="space-y-3">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }} Act <span class="text-red-500">*</span></label>
        <select id="{{ $actSelId }}"
                class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">— select act —</option>
            @foreach($acts as $act)
                <option value="{{ $act->act_id }}"
                    {{ (string)($selectedActId ?? '') === (string)$act->act_id ? 'selected' : '' }}>
                    Act {{ $act->act_no }}/{{ $act->act_year }} — {{ $act->short_title ?? Str::limit($act->title, 55) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }} Node <span class="text-red-500">*</span></label>
        <select id="{{ $nodeSelId }}" name="{{ $nodeField }}" required
                class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                disabled>
            <option value="">— select node —</option>
        </select>
        <p id="{{ $nodeSelId }}_hint" class="text-xs text-gray-400 mt-1" style="display:none">Loading…</p>
    </div>
</div>

<script>
(function () {
    var endpoint   = '{{ route('admin.nodes.by-act') }}';
    var actSelId   = '{{ $actSelId }}';
    var nodeSelId  = '{{ $nodeSelId }}';
    var preselect  = {{ $selectedNodeId ? (int) $selectedNodeId : 'null' }};

    function loadNodes(actId) {
        var nodeSel  = document.getElementById(nodeSelId);
        var hint     = document.getElementById(nodeSelId + '_hint');
        nodeSel.innerHTML = '<option value="">— select node —</option>';

        if (!actId) { nodeSel.disabled = true; return; }

        hint.style.display = 'block';
        nodeSel.disabled   = true;

        fetch(endpoint + '?act_id=' + actId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (nodes) {
                hint.style.display = 'none';
                nodes.forEach(function (n) {
                    var opt       = document.createElement('option');
                    opt.value     = n.node_id;
                    opt.textContent = n.label;
                    if (preselect && n.node_id === preselect) { opt.selected = true; preselect = null; }
                    nodeSel.appendChild(opt);
                });
                nodeSel.disabled = false;
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var actSel = document.getElementById(actSelId);
        actSel.addEventListener('change', function () { loadNodes(this.value); });
        if (actSel.value) { loadNodes(actSel.value); }
    });
})();
</script>
