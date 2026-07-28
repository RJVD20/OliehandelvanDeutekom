<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::orderBy('name')->get();

        return view('admin.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $before = $location->only($location->getFillable());
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode_city' => ['nullable', 'string', 'max:255'],
            'opening' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'show_on_map' => ['nullable', 'boolean'],
            'remark' => ['nullable', 'string', 'max:255'],
        ]);

        $data['show_on_map'] = !empty($data['show_on_map']);

        $location->update($data);
        AuditLog::record('updated', 'location', $location->id, $location->name, $before, $location->only($location->getFillable()));

        return redirect()->route('admin.locations.index')->with('toast', 'Locatie bijgewerkt');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode_city' => ['nullable', 'string', 'max:255'],
            'opening' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'show_on_map' => ['nullable', 'boolean'],
            'remark' => ['nullable', 'string', 'max:255'],
        ]);

        $data['show_on_map'] = !empty($data['show_on_map']);
        $data['slug'] = str($data['name'])->slug('-')->toString();

        $location = Location::create($data);
        AuditLog::record('created', 'location', $location->id, $location->name, [], $location->only($location->getFillable()));

        return redirect()->route('admin.locations.index')->with('toast', 'Locatie toegevoegd');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $before = $location->only($location->getFillable());
        $location->delete();
        AuditLog::record('deleted', 'location', $location->id, $location->name, $before, []);

        return redirect()->route('admin.locations.index')->with('toast', 'Locatie verwijderd');
    }

    public function toggleVisibility(Location $location): RedirectResponse
    {
        $before = $location->only($location->getFillable());
        $location->update(['show_on_map' => ! (bool) $location->show_on_map]);
        AuditLog::record('updated', 'location', $location->id, $location->name, $before, $location->only($location->getFillable()));

        return back()->with(
            'toast',
            $location->show_on_map ? 'Locatie is zichtbaar op de site' : 'Locatie is verborgen op de site'
        );
    }
}
