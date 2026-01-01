<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('drivers.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DriverRequest $request): RedirectResponse
    {
        Driver::create($this->validatedPayload($request));

        return redirect()->route('drivers.index')->with('status', 'Conducteur créé.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DriverRequest $request, Driver $driver): RedirectResponse
    {
        $driver->update($this->validatedPayload($request, $driver));

        return redirect()->route('drivers.index')->with('status', 'Conducteur mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver): RedirectResponse
    {
        if ($driver->photo_path) {
            Storage::disk('public')->delete($driver->photo_path);
        }

        if ($driver->scan_permis_path) {
            Storage::disk('public')->delete($driver->scan_permis_path);
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('status', 'Conducteur supprimé.');
    }

    private function validatedPayload(DriverRequest $request, ?Driver $driver = null): array
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($driver?->photo_path) {
                Storage::disk('public')->delete($driver->photo_path);
            }

            $data['photo_path'] = $request->file('photo')->store('drivers/photos', 'public');
        }

        if ($request->hasFile('scan_permis')) {
            if ($driver?->scan_permis_path) {
                Storage::disk('public')->delete($driver->scan_permis_path);
            }

            $data['scan_permis_path'] = $request->file('scan_permis')->store('drivers/permis', 'public');
        }

        unset($data['photo'], $data['scan_permis']);

        return $data;
    }
}
