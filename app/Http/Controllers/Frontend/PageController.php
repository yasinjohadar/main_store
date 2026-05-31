<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Seo\SeoResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function __construct(protected SeoResolverService $seoResolver) {}

    public function about()
    {
        $seo = $this->seoResolver->forStaticPage('من نحن', 'frontend.about', 'تعرّف على متجرنا وقيمنا');

        return view('frontend.pages.about', compact('seo'));
    }

    public function contact(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:5000',
            ]);

            try {
                Mail::raw($request->message, function ($message) use ($request) {
                    $message->to(site_setting(\App\Services\SiteSettingsService::KEY_SITE_CONTACT_EMAIL, config('mail.from.address', 'info@example.com')))
                        ->subject($request->subject)
                        ->from($request->email, $request->name);
                });

                return back()->with('success', 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.');
            } catch (\Exception $e) {
                return back()->with('error', 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
            }
        }

        $seo = $this->seoResolver->forStaticPage('اتصل بنا', 'frontend.contact', 'تواصل مع فريق المتجر');

        return view('frontend.pages.contact', compact('seo'));
    }

    public function faq()
    {
        $seo = $this->seoResolver->forStaticPage('الأسئلة الشائعة', 'frontend.faq', 'إجابات على أكثر الأسئلة شيوعاً');

        return view('frontend.pages.faq', compact('seo'));
    }

    public function privacy()
    {
        $seo = $this->seoResolver->forStaticPage('سياسة الخصوصية', 'frontend.privacy');

        return view('frontend.pages.privacy', compact('seo'));
    }

    public function terms()
    {
        $seo = $this->seoResolver->forStaticPage('الشروط والأحكام', 'frontend.terms');

        return view('frontend.pages.terms', compact('seo'));
    }
}
