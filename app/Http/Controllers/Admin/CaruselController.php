<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Resources\Fron\CaruselResource;
use App\Models\Admin\Carusel;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaruselController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carusel = Carusel::get();
        return Helper::sendSuccess('', CaruselResource::collection($carusel), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['image' => 'image']);

        $path = $this->uploadImg($request, 'carusels');

        Carusel::create([
            'image' => $path
        ]);

        return Helper::sendSuccess('Carusel created.', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $carusel = Carusel::find($id);
        if (!$carusel) {
            return Helper::sendError('carusel with this id not found.', [], 404);
        }
        return Helper::sendSuccess('', new CaruselResource($carusel), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $carusel = Carusel::find($id);

        if (!$carusel) {
            return Helper::sendError('carusel with this id not found.', [], 404);
        }

        $request->validate(['image' => 'image']);
        $old_path = $carusel->image;
        $path = $this->uploadImg($request, 'carusels');

        $carusel->update([
            'image' => $path ?? $old_path
        ]);

        if ($old_path && isset($path)) {
            Storage::disk('public')->delete($old_path);
        }

        return Helper::sendSuccess('Carusel updated', $carusel, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $carusel = Carusel::find($id);
        if (!$carusel) {
            return Helper::sendError('carusel with this id not found.', [], 404);
        }
        $carusel->delete();
        if ($carusel->image) {
            Storage::disk('public')->delete($carusel->image);
        }

        return Helper::sendSuccess('Carusel deleted successfully.', $carusel, 200);
    }
}
