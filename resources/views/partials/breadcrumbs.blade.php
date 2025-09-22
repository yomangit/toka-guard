@php
    // Ambil breadcrumbs untuk rute aktif (termasuk parameter jika ada)
    $crumbs = Breadcrumbs::generate();
@endphp

@if ($crumbs->isNotEmpty())
    <nav  class="breadcrumbs text-sm ">
        <ul>
            @foreach ($crumbs as $crumb)
                <li>
                    @if ($crumb->url && !$loop->last)
                        <a href="{{ $crumb->url }}" class="inline-flex items-center gap-1">
                            {{-- Icon folder --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            {{ $crumb->title }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1">
                            {{-- Icon file --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $crumb->title }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif
