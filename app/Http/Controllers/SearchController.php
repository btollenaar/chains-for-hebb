<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected SearchService $searchService) {}

    /**
     * Display search results page.
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'nullable|string|in:products,blog',
        ]);

        $query = $request->input('q');
        $type = $request->input('type');
        $results = $this->searchService->fullSearch($query, $type);

        return view('search.index', compact('query', 'type', 'results'));
    }

    /**
     * Return autocomplete results as JSON.
     */
    public function autocomplete(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $results = $this->searchService->search($request->input('q'));

        return response()->json($results);
    }
}
