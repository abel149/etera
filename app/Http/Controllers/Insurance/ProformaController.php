<?php

namespace App\Http\Controllers\Insurance;

use App\Http\Controllers\Controller;
use App\Notifications\NewProformaFile;
use App\Models\User;
use App\Models\Brand;
use App\Models\Proforma;
use Illuminate\Http\Request;

class ProformaController extends Controller
{
    public function store(Request $request)
    {
        // ... existing validation and file creation code ...

        // Create the proforma using only the validated/explicit fields — never $request->all().
        $proforma = Proforma::create([
            'poster_id'   => auth()->id(),
            'file_number' => $request->file_number,
            'car_brand_id' => $request->brand_id,
            'model'       => $request->model,
            'year'        => $request->year,
            'customer_name' => $request->customer_name,
            'customer_phone_number' => $request->customer_phone_number,
            'chassis_number' => $request->chassis_number,
        ]);

        // Log Activity
        \App\Models\ProformaActivityLog::create([
            'proforma_id' => $proforma->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'details' => 'Proformaa created by ' . auth()->user()->name,
        ]);

        $brand = Brand::find($request->brand_id);
        
        // Get all shops that serve this brand
        $shops = User::whereHas('brands', function($query) use ($brand) {
            if ($brand) {
                $query->where('brands.id', $brand->id);
            }
        })->where('role', 'shop')->get();

        // Send notification to each shop
        if ($brand) {
            foreach ($shops as $shop) {
                $shop->notify(new NewProformaFile($brand, $proforma));
            }
        }

        return redirect()->back()->with('success', 'Proforma file created successfully');
    }
} 