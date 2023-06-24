<?php

namespace App\Http\Controllers;

use App\Models\TravelPackage;
use App\Models\TravelPackageType;
use Illuminate\Http\Request;

class TravelPackageTypeController extends Controller
{
    public function store(TravelPackage $package ,Request $request)
    {
        $request->validate([
            'packageType_title' => 'required|min:3',
            'packageType_fee' => 'required|numeric|min:1',
            'packageType_max_person' => 'required|numeric|integer|min:1'
        ]);

        TravelPackageType::create([
            'travel_package_id' => $package->id,
            'title' => $request['packageType_title'],
            'fee' => $request['packageType_fee'],
            'max_person' => $request['packageType_max_person'],
        ]);

        toast('Travel Package Type created successfully','success');
        return back();
        
    }

    public function destroy(TravelPackageType $packageType)
    {
        $packageType->delete();

        toast('Travel Package Type deleted successfully','warning');
        return back();
    }

    public function update(TravelPackageType $packageType, Request $request)
    {
        $request->validate([
            'edit_packageType_title' => 'required|min:3',
            'edit_packageType_fee' => 'required|numeric|min:1',
            'edit_packageType_max_person' => 'required|numeric|integer|min:1'
        ], [
            'edit_packageType_title.required' => 'Title is required.',
            'edit_packageType_title.min' => 'Title must be at least 3 characters.',
            'edit_packageType_fee.required' => 'Fee is required.',
            'edit_packageType_fee.numeric' => 'Fee must be a number',
            'edit_packageType_fee.min' => 'Fee must be at least 1',
            'edit_packageType_persons.required' => 'Person/heads is required.',
            'edit_packageType_persons.numeric' => 'Person/heads must be a number',
            'edit_packageType_persons.min' => 'Person/heads must be at least 1',
            'edit_packageType_persons.integer' => 'Person/heads must be a whole number',
        ]);

        $packageType->update([
            'title' => $request['edit_packageType_title'],
            'fee' => $request['edit_packageType_fee'],
            'max_person' => $request['edit_packageType_max_person'],
        ]);

        toast('Travel Package Type updated successfully','info');
        return back();
    }
}
