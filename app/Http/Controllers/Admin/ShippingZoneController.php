<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingZoneRequest;
use App\Http\Requests\Admin\UpdateShippingZoneRequest;
use App\Http\Requests\Admin\StoreShippingMethodRequest;
use App\Http\Requests\Admin\UpdateShippingMethodRequest;
use App\Models\ShippingZone;
use App\Models\ShippingZoneLocation;
use App\Models\ShippingMethod;
use App\Models\ShippingMethodRule;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $zones = ShippingZone::withCount('locations', 'methods')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.pages.shipping.zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.pages.shipping.zones.create');
    }

    public function store(StoreShippingZoneRequest $request)
    {
        $zone = ShippingZone::create($request->validated());
        return redirect()->route('admin.shipping.zones.edit', $zone)->with('success', 'تم إنشاء منطقة الشحن بنجاح.');
    }

    public function edit(ShippingZone $zone)
    {
        $zone->load('locations', 'methods.rules');
        return view('admin.pages.shipping.zones.edit', compact('zone'));
    }

    public function update(UpdateShippingZoneRequest $request, ShippingZone $zone)
    {
        $zone->update($request->validated());

        return redirect()->route('admin.shipping.zones.edit', $zone)->with('success', 'تم تحديث منطقة الشحن بنجاح.');
    }

    public function destroy(ShippingZone $zone)
    {
        $zone->delete();
        return redirect()->route('admin.shipping.zones.index')->with('success', 'تم حذف منطقة الشحن بنجاح.');
    }

    public function storeLocation(Request $request, ShippingZone $zone)
    {
        $data = $request->validate([
            'country_code' => ['nullable', 'string', 'size:2'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code_pattern' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:country,state,city,postal'],
        ]);

        $zone->locations()->create($data);

        return back()->with('success', 'تم إضافة الموقع للمنطقة.');
    }

    public function destroyLocation(ShippingZone $zone, ShippingZoneLocation $location)
    {
        if ($location->shipping_zone_id !== $zone->id) {
            abort(404);
        }
        $location->delete();

        return back()->with('success', 'تم حذف الموقع من المنطقة.');
    }

    public function storeMethod(StoreShippingMethodRequest $request, ShippingZone $zone)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $method = $zone->methods()->create($data);

        return redirect()->route('admin.shipping.zones.edit', $zone)->with('success', 'تم إضافة طريقة الشحن بنجاح.');
    }

    public function updateMethod(UpdateShippingMethodRequest $request, ShippingZone $zone, ShippingMethod $method)
    {
        if ($method->shipping_zone_id !== $zone->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $method->update($data);

        return back()->with('success', 'تم تحديث طريقة الشحن بنجاح.');
    }

    public function destroyMethod(ShippingZone $zone, ShippingMethod $method)
    {
        if ($method->shipping_zone_id !== $zone->id) {
            abort(404);
        }

        $method->delete();

        return back()->with('success', 'تم حذف طريقة الشحن بنجاح.');
    }

    public function storeRule(Request $request, ShippingZone $zone, ShippingMethod $method)
    {
        if ($method->shipping_zone_id !== $zone->id) {
            abort(404);
        }

        $data = $request->validate([
            'condition_type' => ['required', 'string', 'in:weight,subtotal'],
            'min_value' => ['required', 'numeric', 'min:0'],
            'max_value' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'per_unit' => ['nullable', 'numeric', 'min:0'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $method->rules()->create($data);

        return back()->with('success', 'تم إضافة قاعدة الشحن بنجاح.');
    }

    public function destroyRule(ShippingZone $zone, ShippingMethod $method, ShippingMethodRule $rule)
    {
        if ($method->shipping_zone_id !== $zone->id || $rule->shipping_method_id !== $method->id) {
            abort(404);
        }

        $rule->delete();

        return back()->with('success', 'تم حذف قاعدة الشحن بنجاح.');
    }
}

