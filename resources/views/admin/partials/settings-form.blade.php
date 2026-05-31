@props([
    'schema' => [],
    'settings' => [],
    'sectionLabels' => [],
    'formAction' => '',
    'orderStatusField' => null,
    'orderStatuses' => collect(),
    'secretKeys' => [],
    'storedSecrets' => collect(),
])

@php
    $bySection = [];
    foreach ($schema as $key => $def) {
        $bySection[$def['section']][$key] = $def;
    }
    $sectionsOrder = array_keys($sectionLabels);
@endphp

<form action="{{ $formAction }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                @foreach ($sectionsOrder as $idx => $sectionKey)
                    @if (isset($bySection[$sectionKey]))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $idx === 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                data-bs-target="#pane-{{ $sectionKey }}" type="button" role="tab">
                                {{ $sectionLabels[$sectionKey] }}
                            </button>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="card-body tab-content">
            @foreach ($sectionsOrder as $idx => $sectionKey)
                @if (!isset($bySection[$sectionKey]))
                    @continue
                @endif
                <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="pane-{{ $sectionKey }}" role="tabpanel">
                    <div class="row g-3">
                        @foreach ($bySection[$sectionKey] as $key => $def)
                            @php
                                $value = old($key, $settings[$key] ?? $def['default']);
                                $isBoolean = ($def['type'] ?? 'string') === 'boolean';
                                $isSecret = in_array($key, $secretKeys, true);
                                $hasStoredSecret = $storedSecrets->contains($key);
                            @endphp
                            <div class="col-12 {{ $isBoolean ? '' : 'col-md-6' }}">
                                @if ($isBoolean)
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="{{ $key }}" value="0">
                                        <input class="form-check-input @error($key) is-invalid @enderror" type="checkbox"
                                            name="{{ $key }}" value="1" id="input-{{ $key }}"
                                            {{ $value ? 'checked' : '' }}>
                                        <label class="form-check-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                        @if (!empty($def['hint']))
                                            <div class="form-text">{{ $def['hint'] }}</div>
                                        @endif
                                        @error($key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                @elseif($orderStatusField === $key)
                                    <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                    <select name="{{ $key }}" id="input-{{ $key }}" class="form-select @error($key) is-invalid @enderror">
                                        @foreach($orderStatuses as $status)
                                            <option value="{{ $status->slug }}" @selected($value === $status->slug)>{{ $status->name }} ({{ $status->slug }})</option>
                                        @endforeach
                                    </select>
                                    @if (!empty($def['hint']))<div class="form-text">{{ $def['hint'] }}</div>@endif
                                    @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @else
                                    <label class="form-label" for="input-{{ $key }}">{{ $def['label'] }}</label>
                                    <input type="{{ $isSecret ? 'password' : 'text' }}"
                                        name="{{ $key }}"
                                        id="input-{{ $key }}"
                                        class="form-control @error($key) is-invalid @enderror"
                                        value="{{ $isSecret ? '' : $value }}"
                                        placeholder="{{ $isSecret && $hasStoredSecret ? '•••••••• (محفوظ — اتركه فارغاً للإبقاء)' : '' }}"
                                        autocomplete="off">
                                    @if (!empty($def['hint']))<div class="form-text">{{ $def['hint'] }}</div>@endif
                                    @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
        </div>
    </div>
</form>
