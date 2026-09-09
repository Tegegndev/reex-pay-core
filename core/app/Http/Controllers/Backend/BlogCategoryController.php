<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Blog\CategoryRequest;
use App\Models\BlogCategory;
use App\Models\Language;
use Illuminate\Support\Str;

class BlogCategoryController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'index'                     => 'blog-category-list',
            'store|edit|update|destroy' => 'blog-category-manage',
        ];
    }

    /**
     * Display a listing of the blog categories.
     */
    public function index()
    {
        $categories = BlogCategory::latest()->paginate(10);
        $locales    = Language::languageGet();

        return view('backend.blog.categories.index', compact('categories', 'locales'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request)
    {
        $data           = $request->validated();
        $data['slug']   = $data['slug'] ?? Str::slug($data['name'][app()->getDefaultLocale()] ?? collect($data['name'])->first());
        $data['status'] = $request->boolean('status');
        BlogCategory::create($data);

        notifyEvs('success', __('Category created successfully.'));

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        $blogCategory = BlogCategory::findOrFail($id);
        $locales      = Language::languageGet();

        return view('backend.blog.categories.partials._edit_form', compact('blogCategory', 'locales'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, $id)
    {
        $blog_category  = BlogCategory::findOrFail($id);
        $data           = $request->validated();
        $data['slug']   = $data['slug'] ?? $blog_category->slug;
        $data['status'] = $request->boolean('status');
        $blog_category->update($data);

        notifyEvs('success', __('Category updated successfully.'));

        return redirect()->back();
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $blogCategory = BlogCategory::findOrFail($id);

        // Check if the category has any associated blogs
        if ($blogCategory->blogs()->exists()) {
            notifyEvs('error', __('Category cannot be deleted because it has associated blogs.'));

            return redirect()->back();
        }
        $blogCategory->delete();
        notifyEvs('success', __('Category deleted successfully.'));

        return redirect()->back();
    }
}
