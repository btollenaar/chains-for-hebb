<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;

class CmsPageController extends Controller
{
    /**
     * Display a CMS page by slug.
     */
    public function show(CmsPage $page)
    {
        if (!$page->is_published) {
            abort(404);
        }

        $template = $page->template ?? 'default';
        $viewName = "pages.templates.{$template}";

        if (!view()->exists($viewName)) {
            $viewName = 'pages.templates.default';
        }

        return view($viewName, compact('page'));
    }
}
