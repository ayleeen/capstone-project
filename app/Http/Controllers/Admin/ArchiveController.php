<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\Package;
use App\Models\Customer;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    private function modelFor(string $tab): string
    {
        return match ($tab) {
            'reservations' => Reservation::class,
            'packages'     => Package::class,
            'menu'         => MenuItem::class,
            'customers'    => Customer::class,
            default        => abort(404),
        };
    }

    public function index()
    {
        $reservations = Reservation::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get()
            ->map(fn ($r) => [
                'id'         => (string) $r->id,
                'ref'        => 'RES-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
                'name'       => $r->event_title,
                'client'     => $r->full_name,
                'category'   => $r->event_type,
                'status'     => $r->status,
                'date'       => $r->start_date->format('Y-m-d'),
                'guests'     => $r->guests,
                'pkg'        => $r->package ?? '—',
                'total'      => $r->total_amount,
                'archivedOn' => $r->deleted_at->toIso8601String(),
            ]);

        $menu = MenuItem::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->get()
            ->map(fn ($m) => [
                'id'         => (string) $m->id,
                'name'       => $m->name,
                'category'   => $m->category,
                'price'      => $m->price,
                'status'     => $m->status ?? 'available',
                'archivedOn' => $m->deleted_at->toIso8601String(),
            ]);

            $packages = Package::onlyTrashed()
    ->orderByDesc('deleted_at')
    ->get()
    ->map(fn ($p) => [
        'id'         => (string) $p->id,
        'name'       => $p->package_name,
        'category'   => $p->category,
        'guests'     => $p->guest_min . '–' . $p->guest_max,
        'price'      => $p->price,
        'status'     => $p->status,
        'archivedOn' => $p->deleted_at->toIso8601String(),
    ]);
    $customers = Customer::onlyTrashed()
    ->orderByDesc('deleted_at')
    ->get()
    ->map(fn ($c) => [
        'id'         => (string) $c->id,
        'name'       => $c->name,
        'email'      => $c->email,
        'phone'      => $c->phone,
        'status'     => $c->status,
        'archivedOn' => $c->deleted_at->toIso8601String(),
    ]);
        $archive = [
            'reservations' => $reservations,
            'packages' => $packages,
            'menu' => $menu,
            'customers' => $customers,
        ];

        return view('admin.archive', compact('archive'));
    }

public function restore(Request $request, string $tab)
{
    $model = $this->modelFor($tab);

    $ids = $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'integer',
    ])['ids'];

    $model::onlyTrashed()
        ->whereIn('id', $ids)
        ->restore();

    return response()->json(['success' => true]);
}

    public function destroy(Request $request, string $tab)
    {
        $model = $this->modelFor($tab);
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];

        $model::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return response()->json(['success' => true]);
    }
}   