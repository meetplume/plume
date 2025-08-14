<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Request $request, Page $page): View
    {
        // Only admins can view unpublished pages
        abort_if(! $request->user()?->isAdmin() && ! $page->published_at, 404);

        return view('pages.show', [
            'page' => $page,
        ]);
    }
}
