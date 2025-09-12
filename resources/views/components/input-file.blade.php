@props(['value', 'name', 'error'])

<input {{ $attributes->class([
'file-input file-input-info file:bg-accent file:text-accent-content file:border-0 file-input-bordered file-input-xs pr-3 border
shadow-sm border-accent/20 placeholder-slate-400
focus:outline-none focus:border-accent focus:ring-accent/20 block w-full sm:text-sm font-semibold focus:ring-1 ',
'border-rose-500 ring-1 ring-rose-500 file-input file-input-bordered file-input-xs pr-3 border shadow-sm
border-slate-300 placeholder-slate-400' => $error,
]) }}
@isset($name) name="{{ $name }}" @endif
type="file"
@isset($value) value="{{ $value }}" @endif
{{ $attributes }} />
