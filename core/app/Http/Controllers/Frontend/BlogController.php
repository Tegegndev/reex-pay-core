<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index(Request $request)
    {
        $searchQuery  = $request->input('search');
        $categorySlug = $request->input('category');
        $tagSlug      = $request->input('tag');

        $blogs = Blog::query()
            ->with('category') // Eager load category
            ->active()
            ->when($categorySlug, function ($query, $categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->when($searchQuery, function ($query, $searchQuery) {
                $locale = app()->getLocale();
                $query->where(function ($q) use ($searchQuery, $locale) {
                    $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"$locale\"'))) LIKE ?", ['%'.strtolower($searchQuery).'%'])
                        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(excerpt, '$.\"$locale\"'))) LIKE ?", ['%'.strtolower($searchQuery).'%']);
                });
            })

            ->when($tagSlug, function ($query, $tagSlug) {
                $tag = str_replace('-', ' ', $tagSlug); // slug থেকে আবার normal বানাই
                $query->where('meta_keywords', 'like', '%'.$tag.'%');
            })
            ->latest()
            ->paginate(6)
            ->withQueryString(); // Keep filters on pagination

        return view('frontend.blog.list', compact('blogs'));
    }

    /**
     * Display the specified blog.
     */
    public function details(string $slug): View
    {
        $blogDetails = Blog::findBySlug($slug);

        return view('frontend.blog.details', compact('blogDetails'));
    }
}
