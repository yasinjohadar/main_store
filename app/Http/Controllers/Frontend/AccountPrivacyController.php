<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\GdprService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountPrivacyController extends Controller
{
    public function __construct(protected GdprService $gdpr) {}

    public function export(Request $request)
    {
        $data = $this->gdpr->exportUserData($request->user());

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="my-data.json"',
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'accepted'],
        ]);

        $user = $request->user();
        $this->gdpr->anonymizeUser($user);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home')->with('status', 'تم حذف بيانات حسابك بنجاح.');
    }
}
