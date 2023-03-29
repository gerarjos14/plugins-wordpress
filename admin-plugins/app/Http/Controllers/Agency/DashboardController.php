<?php

namespace App\Http\Controllers\Agency;

use Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('is_agency');
    }

    public function index()
    {
        return view('agency.dashboard');
    }

    public function storeLogo(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,svg|max:2048'
        ]); 
        $user = auth()->user(); 
        if(File::exists($user->image)) File::delete($user->image);
        
        $file = $request->file('image');
        $filename = $file->getClientOriginalName();
        $filename = time() . $filename;

        $img = Image::make($file)->resize(234, 40, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save('img/'.$filename); 

        $user->image = 'img/'.$filename;
        $user->save();
        
        return back();
    }
}
