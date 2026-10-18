<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Support\Facades\Schema;

class PublicPortfolioController extends Controller
{
    public function show(string $slug)
    {
        if (! Schema::hasTable('portfolios')) {
            abort(404);
        }

        $portfolio = Portfolio::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('portfolios.public', compact('portfolio'));
    }
}
