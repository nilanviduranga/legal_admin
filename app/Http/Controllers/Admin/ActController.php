<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActRequest;
use App\Models\Act;
use App\Models\Gazette;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActController extends Controller
{
    public function index(Request $request): View
    {
        $query = Act::with('gazette')
            ->withCount('legalNodes');

        if ($request->filled('q')) {
            $query->where(function ($sub) use ($request) {
                $sub->where('title', 'like', "%{$request->q}%")
                    ->orWhere('short_title', 'like', "%{$request->q}%")
                    ->orWhere('act_no', 'like', "%{$request->q}%");
            });
        }
        if ($request->filled('year')) {
            $query->where('act_year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $acts = $query->orderByDesc('act_year')->orderBy('act_no')->paginate(20);
        $years = Act::distinct()->orderByDesc('act_year')->pluck('act_year');
        return view('admin.acts.index', compact('acts', 'years'));
    }

    public function create(): View
    {
        $gazettes = Gazette::orderByDesc('gazette_date')->get();
        return view('admin.acts.create', compact('gazettes'));
    }

    public function store(StoreActRequest $request): RedirectResponse
    {
        $act = DB::transaction(fn () => Act::create($request->validated()));
        return redirect()->route('admin.acts.show', $act)->with('success', 'Act created.');
    }

    public function show(Act $act): View
    {
        $act->load(['gazette', 'topLevelNodes.allChildren']);
        return view('admin.acts.show', compact('act'));
    }

    public function edit(Act $act): View
    {
        $gazettes = Gazette::orderByDesc('gazette_date')->get();
        return view('admin.acts.edit', compact('act', 'gazettes'));
    }

    public function update(StoreActRequest $request, Act $act): RedirectResponse
    {
        DB::transaction(fn () => $act->update($request->validated()));
        return redirect()->route('admin.acts.show', $act)->with('success', 'Act updated.');
    }

    public function destroy(Act $act): RedirectResponse
    {
        DB::transaction(fn () => $act->delete());
        return redirect()->route('admin.acts.index')->with('success', 'Act deleted.');
    }
}
