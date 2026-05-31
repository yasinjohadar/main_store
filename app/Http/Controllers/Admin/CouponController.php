<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\RespondsWithAdminTable;
use App\Http\Requests\Admin\InlineUpdateCouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    use RespondsWithAdminTable;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Coupon::withCount('usages')->with(['products', 'categories'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($expiresFrom = $request->input('expires_from')) {
            $query->whereDate('expires_at', '>=', $expiresFrom);
        }

        if ($expiresTo = $request->input('expires_to')) {
            $query->whereDate('expires_at', '<=', $expiresTo);
        }

        $perPageOptions = [10, 15, 25, 50, 100];
        $perPage = (int) $request->input('per_page', 15);
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        $coupons = $query->paginate($perPage)->withQueryString();

        $data = compact('coupons');

        return $this->adminTableView(
            $request,
            'admin.pages.coupons.index',
            'admin.pages.coupons.partials.table-body',
            $data
        );
    }

    public function inlineUpdate(InlineUpdateCouponRequest $request, Coupon $coupon)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'applicable_to') {
            $coupon->loadCount(['products', 'categories']);
            if ($value === 'specific_products' && $coupon->products_count === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'اربط منتجاتاً واحدة على الأقل من صفحة التعديل قبل اختيار «منتجات محددة».',
                ], 422);
            }
            if ($value === 'specific_categories' && $coupon->categories_count === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'اربط تصنيفات من صفحة التعديل قبل اختيار «تصنيفات محددة».',
                ], 422);
            }
        }

        if ($field === 'expires_at' && $value && $coupon->starts_at && $value < $coupon->starts_at->format('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البداية.',
            ], 422);
        }

        if ($field === 'starts_at' && $value && $coupon->expires_at && $value > $coupon->expires_at->format('Y-m-d')) {
            return response()->json([
                'success' => false,
                'message' => 'تاريخ البداية يجب أن يكون قبل تاريخ الانتهاء.',
            ], 422);
        }

        $coupon->update([$field => $value]);
        $coupon->refresh()->loadCount(['usages', 'products', 'categories']);

        return response()->json([
            'success' => true,
            'field' => $field,
            'coupon_id' => $coupon->id,
            'display' => $this->couponInlineDisplay($coupon),
        ]);
    }

    protected function couponInlineDisplay(Coupon $coupon): array
    {
        $statusMeta = $this->couponStatusMeta($coupon->status);

        return [
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'type_label' => $this->couponTypeLabel($coupon->type),
            'value' => (float) $coupon->value,
            'value_formatted' => $this->couponValueFormatted($coupon),
            'usage_limit' => $coupon->usage_limit,
            'usage_display' => $this->couponUsageDisplay($coupon),
            'status' => $coupon->status,
            'status_label' => $statusMeta['label'],
            'status_class' => $statusMeta['class'],
            'starts_at' => $coupon->starts_at?->format('Y-m-d'),
            'starts_at_display' => $coupon->starts_at?->format('Y-m-d') ?? '—',
            'expires_at' => $coupon->expires_at?->format('Y-m-d'),
            'expires_at_display' => $coupon->expires_at?->format('Y-m-d') ?? '—',
            'applicable_to' => $coupon->applicable_to ?? 'entire_store',
            'applicable_to_label' => $this->couponApplicableLabel($coupon),
        ];
    }

    protected function couponStatusMeta(string $status): array
    {
        return match ($status) {
            'active' => ['label' => 'نشط', 'class' => 'admin-table__badge--success'],
            'inactive' => ['label' => 'غير نشط', 'class' => 'admin-table__badge--secondary'],
            'expired' => ['label' => 'منتهي', 'class' => 'admin-table__badge--danger'],
            default => ['label' => $status, 'class' => 'admin-table__badge--secondary'],
        };
    }

    protected function couponTypeLabel(string $type): string
    {
        return match ($type) {
            'percentage' => 'نسبة مئوية',
            'fixed_amount' => 'مبلغ ثابت',
            'buy_x_get_y' => 'اشترِ واحصل على',
            default => $type,
        };
    }

    protected function couponValueFormatted(Coupon $coupon): string
    {
        if ($coupon->type === 'percentage') {
            return rtrim(rtrim(number_format((float) $coupon->value, 2), '0'), '.') . '%';
        }

        return number_format((float) $coupon->value, 2) . ' ر.س';
    }

    protected function couponUsageDisplay(Coupon $coupon): string
    {
        $used = (int) ($coupon->usages_count ?? $coupon->usages()->count());
        $limit = $coupon->usage_limit;

        return $limit !== null ? "{$used} / {$limit}" : "{$used} / —";
    }

    protected function couponApplicableLabel(Coupon $coupon): string
    {
        return match ($coupon->applicable_to ?? 'entire_store') {
            'specific_products' => 'منتجات محددة (' . ($coupon->products_count ?? $coupon->products()->count()) . ')',
            'specific_categories' => 'تصنيفات محددة (' . ($coupon->categories_count ?? $coupon->categories()->count()) . ')',
            default => 'المتجر كامل',
        };
    }

    public function create()
    {
        $products = Product::visible()->orderBy('name')->get(['id', 'name']);
        $categories = Category::active()->ordered()->get(['id', 'name', 'parent_id']);
        return view('admin.pages.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,expired',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'applicable_to' => 'required|in:entire_store,specific_products,specific_categories',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ];

        if ($request->input('applicable_to') === 'specific_products') {
            $rules['product_ids'] = 'required|array|min:1';
        }
        if ($request->input('applicable_to') === 'specific_categories') {
            $rules['category_ids'] = 'required|array|min:1';
        }

        $request->validate($rules);

        if (empty($request->input('code'))) {
            $request->merge(['code' => strtoupper(Str::random(6))]);
        }

        $coupon = Coupon::create($request->only([
            'code', 'name', 'description', 'type', 'value', 'minimum_order_amount',
            'usage_limit', 'status', 'starts_at', 'expires_at', 'applicable_to',
        ]));

        $coupon->products()->sync($request->input('product_ids', []));
        $coupon->categories()->sync($request->input('category_ids', []));

        return redirect()->route('admin.coupons.index')->with('success', 'تم إنشاء الكوبون بنجاح.');
    }

    public function edit(Coupon $coupon)
    {
        $coupon->load('products', 'categories');
        $products = Product::visible()->orderBy('name')->get(['id', 'name']);
        $categories = Category::active()->ordered()->get(['id', 'name', 'parent_id']);
        return view('admin.pages.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $rules = [
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,expired',
            'starts_at' => 'nullable|date|after_or_equal:starts_at',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'applicable_to' => 'required|in:entire_store,specific_products,specific_categories',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ];

        if ($request->input('applicable_to') === 'specific_products') {
            $rules['product_ids'] = 'required|array|min:1';
        }
        if ($request->input('applicable_to') === 'specific_categories') {
            $rules['category_ids'] = 'required|array|min:1';
        }

        $request->validate($rules);

        $coupon->update($request->only([
            'code', 'name', 'description', 'type', 'value', 'minimum_order_amount',
            'usage_limit', 'status', 'starts_at', 'expires_at', 'applicable_to',
        ]));

        $coupon->products()->sync($request->input('product_ids', []));
        $coupon->categories()->sync($request->input('category_ids', []));

        return redirect()->route('admin.coupons.index')->with('success', 'تم تحديث الكوبون بنجاح.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'تم حذف الكوبون.');
    }

    public function markAsExpired()
    {
        $coupons = Coupon::where('expires_at', '<', now())->get();
        foreach ($coupons as $coupon) {
            $coupon->update(['status' => 'expired']);
        }
        return redirect()->route('admin.coupons.index')->with('success', 'تم تعليم ' . $coupons->count() . ' كوبون منتهي الصلاحية.');
    }

    public function getUsageReport()
    {
        $coupons = Coupon::with(['usages.user'])->get();
        $report = [];
        
        foreach ($coupons as $coupon) {
            $latestUsage = $coupon->usages()->latest('used_at')->first();

            $report[] = [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'total_usage' => $coupon->usages()->count(),
                'remaining_usage' => $coupon->usage_limit !== null
                    ? max(0, $coupon->usage_limit - $coupon->usages()->count())
                    : null,
                'status' => $coupon->status,
                'is_expired' => $coupon->expires_at && $coupon->expires_at->isPast(),
                'expires_at' => $coupon->expires_at?->format('Y-m-d'),
                'latest_usage' => $latestUsage?->used_at?->format('Y-m-d H:i'),
                'created_at' => $coupon->created_at->format('Y-m-d'),
                'usage_details' => $coupon->usages->map(function ($usage) {
                        return [
                            'user' => $usage->user ? $usage->user->name : 'زائر',
                            'order_number' => $usage->order_number ?? 'N/A',
                            'used_at' => $usage->used_at?->format('Y-m-d H:i:s'),
                            'discount_amount' => $usage->discount_amount,
                        ];
                    }),
            ];
        }

        return response()->json($report);
    }
}
