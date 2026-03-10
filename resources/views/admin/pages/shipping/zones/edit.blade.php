@extends('admin.layouts.master')

@section('page-title')
    تعديل منطقة الشحن
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">منطقة الشحن: {{ $zone->name }}</h5>
                <a href="{{ route('admin.shipping.zones.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">تفاصيل المنطقة</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.shipping.zones.update', $zone) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">اسم المنطقة</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $zone->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">الترتيب</label>
                                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $zone->order) }}" min="0">
                                    @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $zone->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">مفعّل</label>
                                </div>
                                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">نطاق التغطية (الدول / المدن)</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.shipping.zones.locations.store', $zone) }}" class="mb-3">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label">نوع الموقع</label>
                                        <select name="type" class="form-select">
                                            <option value="country">دولة كاملة</option>
                                            <option value="state">ولاية / منطقة</option>
                                            <option value="city">مدينة</option>
                                            <option value="postal">رمز بريدي</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الدولة (رمز ISO-2)</label>
                                        <input type="text" name="country_code" class="form-control" maxlength="2" placeholder="SA">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الولاية / المنطقة</label>
                                        <input type="text" name="state" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">المدينة</label>
                                        <input type="text" name="city" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">نمط الرمز البريدي</label>
                                        <input type="text" name="postal_code_pattern" class="form-control" placeholder="مثال: 12* أو 12345">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">إضافة موقع</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-sm table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>النوع</th>
                                            <th>الدولة</th>
                                            <th>الولاية</th>
                                            <th>المدينة</th>
                                            <th>الرمز البريدي</th>
                                            <th style="width: 60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($zone->locations as $location)
                                            <tr>
                                                <td>
                                                    @switch($location->type)
                                                        @case('country') دولة @break
                                                        @case('state') ولاية @break
                                                        @case('city') مدينة @break
                                                        @case('postal') رمز بريدي @break
                                                        @default —
                                                    @endswitch
                                                </td>
                                                <td>{{ $location->country_code ?? '—' }}</td>
                                                <td>{{ $location->state ?? '—' }}</td>
                                                <td>{{ $location->city ?? '—' }}</td>
                                                <td>{{ $location->postal_code_pattern ?? '—' }}</td>
                                                <td>
                                                    <form action="{{ route('admin.shipping.zones.locations.destroy', [$zone, $location]) }}" method="POST" onsubmit="return confirm('حذف هذا الموقع من المنطقة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">لم يتم إضافة مواقع بعد.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">طرق الشحن للمنطقة</h6>
                                <p class="text-muted small mb-0">يمكنك إنشاء أكثر من طريقة (شحن ثابت، مجاني، حسب الوزن أو المبلغ) مع قواعد مرنة.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.shipping.zones.methods.store', $zone) }}" class="border rounded p-3 mb-4">
                                @csrf
                                <h6 class="mb-3 text-primary">إضافة طريقة شحن جديدة</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">الاسم</label>
                                        <input type="text" name="name" class="form-control" required placeholder="مثال: شحن قياسي">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">النوع</label>
                                        <select name="type" class="form-select">
                                            <option value="flat_rate">سعر ثابت</option>
                                            <option value="free_shipping">شحن مجاني</option>
                                            <option value="by_weight">حسب الوزن</option>
                                            <option value="by_price">حسب مبلغ السلة</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">التكلفة الأساسية</label>
                                        <input type="number" name="base_cost" step="0.01" min="0" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">الترتيب</label>
                                        <input type="number" name="order" min="0" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">أقل مبلغ للسلة لتطبيق الطريقة</label>
                                        <input type="number" name="min_cart_total" step="0.01" min="0" class="form-control">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center mt-4 pt-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="method_is_active" name="is_active" value="1" checked>
                                            <label class="form-check-label" for="method_is_active">مفعّلة</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">إضافة الطريقة</button>
                                    </div>
                                </div>
                            </form>

                            @forelse($zone->methods as $method)
                                <div class="border rounded p-3 mb-3">
                                    <form method="POST" action="{{ route('admin.shipping.zones.methods.update', [$zone, $method]) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label">الاسم</label>
                                                <input type="text" name="name" class="form-control" value="{{ $method->name }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">النوع</label>
                                                <select name="type" class="form-select">
                                                    <option value="flat_rate" @selected($method->type === 'flat_rate')>سعر ثابت</option>
                                                    <option value="free_shipping" @selected($method->type === 'free_shipping')>شحن مجاني</option>
                                                    <option value="by_weight" @selected($method->type === 'by_weight')>حسب الوزن</option>
                                                    <option value="by_price" @selected($method->type === 'by_price')>حسب مبلغ السلة</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">التكلفة الأساسية</label>
                                                <input type="number" name="base_cost" step="0.01" min="0" class="form-control" value="{{ $method->base_cost }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">الترتيب</label>
                                                <input type="number" name="order" min="0" class="form-control" value="{{ $method->order }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">أقل مبلغ للسلة</label>
                                                <input type="number" name="min_cart_total" step="0.01" min="0" class="form-control" value="{{ $method->min_cart_total }}">
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-switch mt-4 pt-2">
                                                    <input class="form-check-input" type="checkbox" id="is_active_{{ $method->id }}" name="is_active" value="1" {{ $method->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active_{{ $method->id }}">مفعّلة</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-end mt-4 pt-2">
                                                <button type="submit" class="btn btn-sm btn-primary">حفظ</button>
                                                <form action="{{ route('admin.shipping.zones.methods.destroy', [$zone, $method]) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف طريقة الشحن هذه مع قواعدها؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                </form>
                                            </div>
                                        </div>
                                    </form>

                                    <hr>
                                    <h6 class="mb-2">قواعد التسعير</h6>
                                    <p class="text-muted small mb-2">مثال: من 0 إلى 5 كجم = 20 ر.س، من 5 إلى 10 كجم = 35 ر.س، وهكذا.</p>
                                    <form method="POST" action="{{ route('admin.shipping.zones.methods.rules.store', [$zone, $method]) }}" class="row g-2 align-items-end mb-3">
                                        @csrf
                                        <div class="col-md-2">
                                            <label class="form-label">نوع الشرط</label>
                                            <select name="condition_type" class="form-select">
                                                <option value="weight">وزن السلة</option>
                                                <option value="subtotal">مجموع السلة</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">من</label>
                                            <input type="number" name="min_value" step="0.001" min="0" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">إلى</label>
                                            <input type="number" name="max_value" step="0.001" min="0" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">التكلفة</label>
                                            <input type="number" name="cost" step="0.01" min="0" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">لكل وحدة إضافية</label>
                                            <input type="number" name="per_unit" step="0.01" min="0" class="form-control" placeholder="اختياري">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">ترتيب</label>
                                            <input type="number" name="order" min="0" class="form-control" value="0">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">+</button>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>الشرط</th>
                                                    <th>من</th>
                                                    <th>إلى</th>
                                                    <th>التكلفة</th>
                                                    <th>لكل وحدة إضافية</th>
                                                    <th>الترتيب</th>
                                                    <th style="width: 60px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($method->rules as $rule)
                                                    <tr>
                                                        <td>{{ $rule->condition_type === 'weight' ? 'وزن السلة' : 'مجموع السلة' }}</td>
                                                        <td>{{ $rule->min_value }}</td>
                                                        <td>{{ $rule->max_value ?? '—' }}</td>
                                                        <td>{{ $rule->cost }}</td>
                                                        <td>{{ $rule->per_unit ?? '—' }}</td>
                                                        <td>{{ $rule->order }}</td>
                                                        <td>
                                                            <form action="{{ route('admin.shipping.zones.methods.rules.destroy', [$zone, $method, $rule]) }}" method="POST" onsubmit="return confirm('حذف هذه القاعدة؟');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-danger">حذف</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">لم يتم إضافة قواعد بعد.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">لم يتم إضافة طرق شحن لهذه المنطقة بعد.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

