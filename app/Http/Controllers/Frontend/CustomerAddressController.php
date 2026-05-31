<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);
        $validated['user_id'] = Auth::id();

        if ($validated['is_default'] ?? false) {
            CustomerAddress::where('user_id', Auth::id())
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        CustomerAddress::create($validated);

        return back()->with('success', 'تمت إضافة العنوان');
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($address);
        $validated = $this->validateAddress($request);

        if ($validated['is_default'] ?? false) {
            CustomerAddress::where('user_id', Auth::id())
                ->where('type', $validated['type'])
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'تم تحديث العنوان');
    }

    public function destroy(CustomerAddress $address)
    {
        $this->authorizeAddress($address);
        $address->delete();

        return back()->with('success', 'تم حذف العنوان');
    }

    protected function validateAddress(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:billing,shipping',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'required|string|size:2',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);
    }

    protected function authorizeAddress(CustomerAddress $address): void
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
