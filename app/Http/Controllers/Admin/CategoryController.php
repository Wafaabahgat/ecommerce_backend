<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\CategoryRequest;
use App\Models\Admin\Category;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::with('parent')->filter($request->query())->paginate(10);

        return Helper::sendSuccess('', $categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $slug = Str::slug($request->post("name"));
        $image = $this->uploadImg($request, 'categories', 'image');

        Category::create([
            'parent_id' => $request->post('parent_id'),
            'name' => $request->post('name'),
            'disc' => $request->post('disc'),
            'slug' => $slug,
            'image' => $image,
        ]);

        return Helper::sendSuccess('Category created successfully', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($category)
    {
        $category = Category::find($category);
        if (!$category) {
            return Helper::sendError("No category with this #ID: $category", [], 404);
        }
        $cats = Category::all();
        $category['categories'] = $cats;
        return Helper::sendSuccess('', $category, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, $category)
    {
        $category = Category::find($category);
        if (!$category) {
            return Helper::sendError("No category with this #ID: $category", [], 404);
        }

        $slug = Str::slug($request->post("name"));
        $image = $this->uploadImg($request, 'categories', 'image');
        $old_path = $category->image;

        $category->update([
            'parent_id' => $request->post('parent_id') ?? $category->parent_id,
            'name' => $request->post('name') ?? $category->name,
            'disc' => $request->post('disc') ?? $category->disc,
            'slug' => $slug ?? $category->slug,
            'image' => $image ?? $category->image,
            'status' => $request->post('status') ?? $category->status
        ]);

        if ($old_path && isset($image)) {
            Storage::disk('public')->delete($old_path);
        }

        return Helper::sendSuccess('Category updated successfully', [], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($category)
    {
        $category = Category::find($category);
        if (!$category) {
            return Helper::sendError("No category with this #ID $category", [], 404);
        }
        $category->forceDelete();

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return Helper::sendSuccess('category deleted successfully', [], 200);
    }
}
