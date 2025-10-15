<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\StoreRequest;
use App\Models\Admin\Store;
use App\Models\User;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $stores = Store::filter($request->query())->paginate(10);
        return Helper::sendSuccess('', $stores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        $slug = Str::slug($request->post("name"));
        $logo = $this->uploadImg($request, 'stores', 'logo');
        $cover = $this->uploadImg($request, 'stores', 'cover');

        $store = Store::create([
            'name' => $request->post('name'),
            'disc' => $request->post('disc'),
            'slug' => $slug,
            'logo' => $logo,
            'cover' => $cover,
        ]);

        if ($user->role == 'user') {
            User::where('id', $user->id)->update([
                'store_id' => $store->id
            ]);
        }

        return Helper::sendSuccess('Store created successfully', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($store)
    {
        $store = Store::find($store);
        if (!$store) {
            return Helper::sendError('No Store with this #ID', [], 404);
        }
        return Helper::sendSuccess('', $store, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRequest $request, $store)
    {
        $store = Store::find($store);
        if (!$store) {
            return Helper::sendError('No Store with this #ID', [], 404);
        }

        $slug = Str::slug($request->post("name"));

        $old_path_logo = $store->logo;
        $path_logo = $this->uploadImg($request, 'stores', 'logo');
        $old_path_cover = $store->cover;
        $path_cover = $this->uploadImg($request, 'stores', 'cover');

        $store->update([
            'name' => $request->post('name') ?? $store->name,
            'disc' => $request->post('disc') ?? $store->disc,
            'slug' => $slug ?? $store->slug,
            'logo' => $path_logo ?? $store->logo,
            'cover' => $path_cover ?? $store->cover,
            'status' => $request->post('status') ?? $store->status,
        ]);

        if ($old_path_logo && isset($path_logo)) {
            Storage::disk('public')->delete($old_path_logo);
        }
        if ($old_path_cover && isset($path_cover)) {
            Storage::disk('public')->delete($old_path_cover);
        }

        return Helper::sendSuccess('Store updated successfully', $store, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($store)
    {
        $store = Store::find($store);
        if (!$store) {
            return Helper::sendError('No Store with this #ID', [], 404);
        }
        $store->forceDelete();

        if ($store->logo) {
            Storage::disk('public')->delete($store->logo);
        }
        if ($store->cover) {
            Storage::disk('public')->delete($store->cover);
        }

        return Helper::sendSuccess('Store deleted successfully', [], 200);
    }
}
