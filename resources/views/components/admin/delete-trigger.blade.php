@props([
    'action' => '',
    'item' => null,
    'title' => 'تأكيد الحذف',
    'message' => 'هل أنت متأكد من حذف هذا العنصر؟',
    'warning' => 'لن يمكن التراجع عن هذا الإجراء.',
    'method' => 'DELETE',
    'confirmText' => 'نعم، احذف',
    'cancelText' => 'إلغاء',
    'icon' => 'las la-trash-alt',
    'buttonClass' => 'btn-danger btn-sm',
    'label' => null,
    'disabled' => false,
    'titleAttr' => null,
])

<button type="button"
        class="btn {{ $buttonClass }}"
        data-confirm-delete
        data-action="{{ $action }}"
        data-title="{{ $title }}"
        data-message="{{ $message }}"
        data-warning="{{ $warning }}"
        data-method="{{ $method }}"
        data-confirm-text="{{ $confirmText }}"
        data-cancel-text="{{ $cancelText }}"
        data-icon="{{ $icon }}"
        @if($item !== null && $item !== '') data-item="{{ $item }}" @endif
        @if($disabled) disabled @endif
        @if($titleAttr) title="{{ $titleAttr }}" @endif
        {{ $attributes }}>
    <i class="{{ $icon }}" aria-hidden="true"></i>@if($label)<span class="ms-1">{{ $label }}</span>@endif
</button>
