<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderDownload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download(string $token)
    {
        $download = OrderDownload::with(['order', 'file'])->where('download_token', $token)->firstOrFail();

        $order = $download->order;
        if ($order->user_id) {
            if (! Auth::check() || Auth::id() !== $order->user_id) {
                abort(403);
            }
        }

        if (! $download->canDownload()) {
            abort(403, 'انتهت صلاحية التحميل أو استنفدت عدد المحاولات');
        }

        if (! is_null($download->remaining_downloads)) {
            $download->decrement('remaining_downloads');
        }
        $download->update(['downloaded_at' => now()]);

        return Storage::disk('public')->download($download->file->path, $download->file->title);
    }
}
