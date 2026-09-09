<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Blog\SaveRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Language;
use App\Traits\FileManageTrait;
use App\Traits\PurifyTrait;

class BlogController extends BaseController
{
    use FileManageTrait, PurifyTrait;

    public static function permissions(): array
    {
        return [
            'index'        => 'blog-list',
            'create|store' => 'blog-create',
            'edit|update'  => 'blog-edit',
            'destroy'      => 'blog-delete',
        ];
    }

    public function index()
    {
        $filterCategory = request()->input('category');
        if ($filterCategory) {
            $blogs = Blog::with('category', 'author')
                ->whereHas('category', function ($query) use ($filterCategory) {
                    $query->where('id', $filterCategory);
                })
                ->latest()
                ->paginate(10);
        } else {
            $blogs = Blog::with('category', 'author')->latest()->paginate(10);
        }

        return view('backend.blog.index', compact('blogs'));
    }

    public function store(SaveRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->uploadImage($request->file('thumbnail'));
        }
        $data['admin_id']      = auth()->id(); // blog author id
        $data['meta_keywords'] = $this->processMetaKeywords($data['meta_keywords'] ?? null);
        $data['content']       = $this->purifyContent($data['content'] ?? null, 'images/blogs');
        $data['status']        = $request->boolean('status', false);

        Blog::create($data);

        notifyEvs('success', __('Blog created successfully.'));

        return redirect()->route('admin.blog.post.index');
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $locales    = Language::languageGet();
        $categories = BlogCategory::active()->get();

        return view('backend.blog.create', compact('locales', 'categories'));
    }

    public function edit($id)
    {
        $blog       = Blog::with('category')->findOrFail($id);
        $locales    = Language::languageGet();
        $categories = BlogCategory::active()->get();

        return view('backend.blog.edit', compact('blog', 'locales', 'categories'))->render();
    }

    public function update(SaveRequest $request, $id)
    {
        $data = $request->validated();

        $blog = Blog::findOrFail($id);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->uploadImage($request->file('thumbnail'), $blog->thumbnail);
        }

        $data['meta_keywords'] = $this->processMetaKeywords($data['meta_keywords'] ?? null);
        $data['content']       = $this->purifyContent($data['content'] ?? $blog->content, 'images/blogs');
        $data['status']        = $request->boolean('status', false);
        $blog->update($data);

        notifyEvs('success', __('Blog updated successfully.'));

        return redirect()->back();
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $this->delete($blog->thumbnail);
        $this->deleteSummernoteImages($blog->content);
        $blog->delete();
        notifyEvs('success', __('Blog deleted successfully.'));

        return redirect()->back();
    }

    private function processMetaKeywords(?string $metaKeywords): ?string
    {
        if (blank($metaKeywords)) {
            return null;
        }

        $decoded = json_decode($metaKeywords, true);

        if (is_array($decoded)) {
            $keywords = collect($decoded)
                ->pluck('value')
                ->filter()
                ->implode(',');

            return $keywords ?: null;
        }

        return $metaKeywords; // fallback if it's already string
    }
}
