<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseLawRequest;
use App\Models\CaseLaw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CaseLawController extends Controller
{
    public function index(Request $request): View
    {
        $query = CaseLaw::query();

        if ($request->filled('q')) {
            $query->where('case_name', 'like', "%{$request->q}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $caseLaws = $query->orderByDesc('updated_at')->paginate(20);

        return view('admin.case-laws.index', compact('caseLaws'));
    }

    public function store(StoreCaseLawRequest $request): RedirectResponse|JsonResponse
    {
        $caseLaw = DB::transaction(fn () => CaseLaw::create($request->validated()));

        if ($request->wantsJson()) {
            return response()->json($caseLaw, 201);
        }

        return redirect()->route('admin.case-laws.show', $caseLaw)->with('success', 'Case law created.');
    }

    public function show(CaseLaw $caseLaw): View
    {
        return view('admin.case-laws.show', compact('caseLaw'));
    }

    public function update(StoreCaseLawRequest $request, CaseLaw $caseLaw): RedirectResponse|JsonResponse
    {
        DB::transaction(fn () => $caseLaw->update($request->validated()));

        if ($request->wantsJson()) {
            return response()->json($caseLaw->fresh());
        }

        return redirect()->route('admin.case-laws.show', $caseLaw)->with('success', 'Case law updated.');
    }

    public function destroy(CaseLaw $caseLaw): RedirectResponse
    {
        DB::transaction(fn () => $caseLaw->delete());
        return redirect()->route('admin.case-laws.index')->with('success', 'Case law deleted.');
    }
}
