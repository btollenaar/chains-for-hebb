<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterTrackingController extends Controller
{
    /**
     * Track email open (return 1x1 transparent GIF).
     */
    public function trackOpen(Request $request)
    {
        $token = $request->input('token');

        if ($token) {
            $send = NewsletterSend::where('tracking_token', $token)->first();

            if ($send) {
                try {
                    // Track the open (only once)
                    $send->trackOpen();
                } catch (\Exception $e) {
                    Log::error('Newsletter open tracking failed', [
                        'token' => $token,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Return 1x1 transparent GIF
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($gif, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Track link click and redirect to original URL.
     */
    public function trackClick(Request $request)
    {
        $token = $request->input('token');
        $url = $request->input('url');

        // Validate URL
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(404);
        }

        if ($token) {
            $send = NewsletterSend::where('tracking_token', $token)->first();

            if ($send) {
                try {
                    // Track the click (also marks as opened if not already)
                    $send->trackClick();
                } catch (\Exception $e) {
                    Log::error('Newsletter click tracking failed', [
                        'token' => $token,
                        'url' => $url,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Redirect to the original URL
        return redirect($url);
    }
}
