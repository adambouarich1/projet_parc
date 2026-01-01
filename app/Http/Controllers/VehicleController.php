<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('vehicles.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::create($this->validatedPayload($request));

        return redirect()
            ->route('vehicles.index')
            ->with('status', 'Véhicule créé avec succès.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validatedPayload($request, $vehicle));

        return redirect()
            ->route('vehicles.index')
            ->with('status', 'Véhicule mis à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->image_path) {
            Storage::disk('public')->delete($vehicle->image_path);
        }

        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('status', 'Véhicule supprimé.');
    }

    /**
     * Normalise les données validées (image incluse).
     */
    private function validatedPayload(VehicleRequest $request, ?Vehicle $vehicle = null): array
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($vehicle?->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }

            $data['image_path'] = $request->file('image')->store('vehicles', 'public');
        }

        unset($data['image']);

        return $data;
    }
}
