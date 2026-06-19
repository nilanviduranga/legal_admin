<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGazetteRequest;
use App\Models\Gazette;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GazetteController extends Controller
{
    public function index(Request $request): View
    {
        $gazettes = Gazette::withCount('acts')
            ->when($request->get('q'), fn ($q, $search) => $q->where(function ($sub) use ($search) {
                $sub->where('gazette_no', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            }))
            ->when($request->get('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest('gazette_date')
            ->paginate(20);

        return view('admin.gazettes.index', compact('gazettes'));
    }

    public function create(): View
    {
        return view('admin.gazettes.create');
    }

    public function store(StoreGazetteRequest $request): RedirectResponse|JsonResponse
    {
        $gazette = DB::transaction(fn () => Gazette::create($request->validated()));

        if ($request->wantsJson()) {
            $gazette->loadCount('acts');
            return response()->json($gazette, 201);
        }

        return redirect()->route('admin.gazettes.index')->with('success', 'Gazette created.');
    }

    public function show(Gazette $gazette): View
    {
        $gazette->load('acts');
        return view('admin.gazettes.show', compact('gazette'));
    }

    public function edit(Gazette $gazette): View
    {
        return view('admin.gazettes.edit', compact('gazette'));
    }

    public function update(StoreGazetteRequest $request, Gazette $gazette): RedirectResponse|JsonResponse
    {
        DB::transaction(fn () => $gazette->update($request->validated()));

        if ($request->wantsJson()) {
            $gazette->refresh()->loadCount('acts');
            return response()->json($gazette);
        }

        return redirect()->route('admin.gazettes.index')->with('success', 'Gazette updated.');
    }

    public function destroy(Gazette $gazette): RedirectResponse
    {
        DB::transaction(fn () => $gazette->delete());
        return redirect()->route('admin.gazettes.index')->with('success', 'Gazette deleted.');
    }
}
