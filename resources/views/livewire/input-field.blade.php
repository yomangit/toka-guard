<fieldset class="fieldset">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <input
        id="{{ $id }}"
        name="{{ $id }}"
        type="{{ $type }}"
        wire:model.live="{{ $model }}"
        placeholder="{{ $placeholder }}"
        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs
        @error($model) ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500 @enderror"
    />

    <x-label-error :messages="$errors->get($model)" />
</fieldset>
