<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\TravelPackage;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function store(TravelPackage $package, Request $request)
    {

        $request->validate([
            'location_name' => 'required|min:3',
            'location_description' => 'required|min:6',
            'location_image' => ['nullable', File::image()]
        ]);

        if($request->hasFile('location_image')) {
            $image = $request->file('location_image')->store('tourist-spots', 'public');
        }

        Location::create([
            'travel_package_id' => $package->id,
            'name' => $request['location_name'],
            'description' => $request['location_description'],
            'image' => $image ?? null
        ]);

        toast('Location created successfully','success');
        return back();
    }

    public function destroy(Location $location)
    {
        if($location->image != null){
            Storage::delete($location->image);
            unlink(storage_path('app/public/'. $location->image));
        }

        $location->delete();

        toast('Location deleted successfully','warning');
        return back();
    }

    public function update(Location $location, Request $request)
    {

        $request->validate([
            'edit_location_name' => 'required|min:3',
            'edit_location_description' => 'required|min:6',
            'edit_location_image' => ['nullable', File::image()]
        ], [
            'edit_location_name.required' => 'Location name is required.',
            'edit_location_name.min' => 'Location name must be at least 3 characters.',
            'edit_location_description.required' => 'Location description is required.',
            'edit_location_description.min' => 'Location description must be at least 6 characters.',
            'edit_location_image.image' => 'Location image must be an image.',
        ]);

        if($request->hasFile('edit_location_image')) {
            $image = $request->file('edit_location_image')->store('tourist-spots', 'public');
        }

        if($location->image != null){
            Storage::delete($location->image);
            unlink(storage_path('app/public/'. $location->image));
        }

        $location->update([
            'name' => $request['edit_location_name'],
            'description' => $request['edit_location_description'],
            'image' => $image ?? $location->image
        ]);

        toast('Location updated successfully','info');
        return back();

    }
}
