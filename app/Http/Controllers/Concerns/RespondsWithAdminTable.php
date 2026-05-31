<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\View\View;

trait RespondsWithAdminTable
{
    protected function adminTableView(Request $request, string $fullView, string $partialView, array $data): View
    {
        if ($request->ajax() || $request->header('X-Admin-Table') === '1') {
            return view($partialView, $data);
        }

        return view($fullView, $data);
    }
}
