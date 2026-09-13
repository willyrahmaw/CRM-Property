@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'required' => false,
    'placeholder' => '-- Pilih --',
    'searchable' => true,
    'options' => null,
    'selected' => null,
    'help' => null,
])

@php
    $inputId = $id ?? $name ?? 'select-' . Str::random(8);
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-xs font-semibold text-[#161616]">
            {{ $label }}
            @if ($required)
                <span class="text-[#991B1B]">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $inputId }}"
        {{ $required ? 'required' : '' }}
        {{ ! $searchable ? 'data-no-search="true"' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]']) }}>

        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if ($options)
            @foreach ($options as $key => $optionLabel)
                <option value="{{ $key }}" {{ (string) $selected === (string) $key ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>

    @if ($help)
        <p class="text-[10px] text-[#79766F]">{{ $help }}</p>
    @endif

    @if ($name)
        @error($name)
            <p class="text-[11px] text-[#991B1B] font-medium mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>
