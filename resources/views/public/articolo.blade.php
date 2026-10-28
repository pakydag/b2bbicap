@extends('public.layouts.main')

@section('title', $articolo->titolo . ' - ' . config('app.name'))

@section('content')

<!-- ================= 1. VIDEO HERO BANNER (1-to-1 bicap.shop) ================= -->
<div class="relative w-full overflow-hidden bg-black" style="height: 480px; max-height: 60vh;">
    <!-- Video Element (Desktop WebM & Mobile MP4) -->
    <video playsinline autoplay loop muted class="absolute inset-0 w-full h-full object-cover">
        <source src="https://www.bicap.it/video/SVILUPPO.webm" type="video/webm">
        <source src="https://www.bicap.it/video/SVILUPPO-mobile.mp4" type="video/mp4">
    </video>

    <!-- Dark Overlay with Centered Title & Breadcrumbs -->
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 z-10" style="background: linear-gradient(to bottom, rgba(0,0,0,0.50) 0%, rgba(0,0,0,0.70) 100%);">
        <h1 class="text-3xl sm:text-5xl md:text-6xl font-normal text-white uppercase tracking-wider mb-2" style="font-family: 'Montserrat', sans-serif; text-shadow: 0 2px 6px rgba(0,0,0,0.85); color: #ffffff !important;">
            {{ $articolo->titolo }}
        </h1>
        <div class="w-16 h-0.5 bg-white mx-auto my-3 shadow-sm" style="background-color: #ffffff !important;"></div>
        <div class="flex items-center justify-center gap-2 text-xs sm:text-sm font-bold uppercase tracking-widest text-white" style="font-family: 'Montserrat', sans-serif; text-shadow: 0 1px 4px rgba(0,0,0,0.9), 0 2px 8px rgba(0,0,0,0.85); color: #ffffff !important;">
            <a href="{{ route('public.home') }}" class="text-white hover:underline transition-opacity hover:opacity-85" style="color: #ffffff !important; text-shadow: 0 1px 4px rgba(0,0,0,0.9), 0 2px 8px rgba(0,0,0,0.85);">HOME</a>
            <span class="text-white font-bold" style="color: #ffffff !important; text-shadow: 0 1px 4px rgba(0,0,0,0.9), 0 2px 8px rgba(0,0,0,0.85);">·</span>
            @if($articolo->section && strtoupper(trim($articolo->section->nome)) !== strtoupper(trim($articolo->titolo)))
                <span class="text-white font-bold" style="color: #ffffff !important; text-shadow: 0 1px 4px rgba(0,0,0,0.9), 0 2px 8px rgba(0,0,0,0.85);">{{ strtoupper($articolo->section->nome) }}</span>
                <span class="text-white font-bold" style="color: #ffffff !important; text-shadow: 0 1px 4px rgba(0,0,0,0.9), 0 2px 8px rgba(0,0,0,0.85);">·</span>
            @endif
            <span class="text-white font-bold" style="color: #ffffff !important; text-shadow: 0 1px 4px rgba(0,0,0,0.9), 0 2px 8px rgba(0,0,0,0.85);">{{ $articolo->titolo }}</span>
        </div>
    </div>
</div>

<!-- ================= 2. MAIN CONTENT & SIDEBAR "LEGGI ALTRO" ================= -->
<div class="bg-white py-12 lg:py-16">
    <div class="bicap-article-wrapper">
        <div class="bicap-article-row">
            
            <!-- Left Column: Article Body (70-75%) -->
            <div class="bicap-article-main">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-black uppercase tracking-tight mb-8" style="font-family: 'Montserrat', sans-serif;">
                    {{ $articolo->titolo }}
                </h2>

                @if($articolo->sottotitolo)
                    <h3 class="text-lg font-semibold text-zinc-700 mb-6">
                        {{ $articolo->sottotitolo }}
                    </h3>
                @endif

                @php
                    $alignment = $articolo->allineamento_media ?? 'center';
                    $alignmentClasses = 'w-full rounded-xl bg-gray-50 object-cover max-h-[600px] shadow-lg ring-1 ring-gray-200 mb-10';

                    if ($alignment === 'left') {
                        $alignmentClasses = 'md:float-left md:mr-8 md:mb-6 md:w-1/2 w-full rounded-lg bg-gray-50 object-cover max-h-[500px] shadow-lg ring-1 ring-gray-200';
                    } elseif ($alignment === 'right') {
                        $alignmentClasses = 'md:float-right md:ml-8 md:mb-6 md:w-1/2 w-full rounded-xl bg-gray-50 object-cover max-h-[500px] shadow-lg ring-1 ring-gray-200';
                    }
                @endphp

                @if($articolo->video)
                    <figure class="{{ $alignment === 'center' ? 'my-8' : '' }}">
                        <video class="{{ $alignmentClasses }}" autoplay loop muted playsinline controls>
                            <source src="{{ asset($articolo->video) }}" type="video/mp4">
                        </video>
                    </figure>
                @elseif($articolo->hasMedia('foto'))
                    <figure class="{{ $alignment === 'center' ? 'my-8' : '' }}">
                        <img class="{{ $alignmentClasses }}" src="{{ $articolo->getFirstMediaUrl('foto') }}" alt="{{ $articolo->titolo }}">
                    </figure>
                @elseif($articolo->foto)
                    <figure class="{{ $alignment === 'center' ? 'my-8' : '' }}">
                        <img class="{{ $alignmentClasses }}" src="{{ asset($articolo->foto) }}" alt="{{ $articolo->titolo }}">
                    </figure>
                @endif

                <!-- Article Rich Text Description -->
                <div class="bicap-article-content text-zinc-800 text-[14px] leading-relaxed space-y-4">
                    {!! $articolo->descrizione !!}
                </div>

                @if($alignment !== 'center')
                    <div class="clear-both"></div>
                @endif

                <!-- Attachments & External Link -->
                @if($articolo->hasMedia('allegati') || $articolo->link)
                    <div class="mt-12 pt-6 border-t border-gray-100 flex flex-wrap gap-4 items-center">
                        @if($articolo->link)
                            <a href="{{ $articolo->link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-5 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-wider hover:bg-[#fedb00] hover:text-black transition-colors">
                                Visita Link Esterno →
                            </a>
                        @endif

                        @if($articolo->hasMedia('allegati'))
                            <a href="{{ $articolo->getFirstMediaUrl('allegati') }}" target="_blank" download class="inline-flex items-center px-5 py-2.5 bg-zinc-100 text-black border border-zinc-300 text-xs font-bold uppercase tracking-wider hover:bg-black hover:text-white transition-colors">
                                Scarica Allegato ↓
                            </a>
                        @endif
                    </div>
                @endif

                <!-- Area Widget -->
                @if($articolo->widgets && $articolo->widgets->count() > 0)
                    <div class="mt-12">
                        @foreach($articolo->widgets as $widget)
                            @php
                                $isGlobal = $widget->tipo === 'global_widget';
                                $actualWidget = $isGlobal ? \App\Models\GlobalWidget::find($widget->data['global_widget_id'] ?? null) : $widget;
                            @endphp

                            @if($actualWidget)
                                <div class="widget-block mb-8">
                                    @if($actualWidget->titolo && !in_array($actualWidget->tipo, ['gallery', 'video']))
                                        <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">{{ $actualWidget->titolo }}</h3>
                                    @endif

                                    @if($actualWidget->tipo === 'gallery')
                                        @include('public.partials.widgets.gallery', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'video')
                                        @include('public.partials.widgets.video', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'mirror_blocks')
                                        @include('public.partials.widgets.mirror', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'single_block')
                                        @include('public.partials.widgets.single_block', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'section_grid')
                                        @include('public.partials.widgets.section_grid', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'info_blocks')
                                        @include('public.partials.widgets.info_blocks', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'booking_structures')
                                        @include('public.partials.widgets.booking_structures', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'map')
                                        @include('public.partials.widgets.map', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'shop_collection')
                                        @include('public.partials.widgets.shop_collection', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'shop_featured_products')
                                        @include('public.partials.widgets.shop_featured_products', ['widget' => $actualWidget])
                                    @elseif($actualWidget->tipo === 'shop_brands')
                                        @include('public.partials.widgets.shop_brands', ['widget' => $actualWidget])
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Area Forms -->
                @if($articolo->has_contact_form)
                    <div class="mt-12">
                        @include('public.partials.contact_form')
                    </div>
                @endif

                @if($articolo->has_transfer_form)
                    <div class="mt-12">
                        @include('public.partials.transfer_form')
                    </div>
                @endif

                @if($articolo->has_car_rental_form)
                    <div class="mt-12">
                        @include('public.partials.car_rental_form')
                    </div>
                @endif

            </div>

            <!-- Right Column: Sidebar "LEGGI ALTRO" (25-30%) -->
            <aside class="bicap-article-sidebar">
                <div class="bicap-sidebar-box">
                    <h3 class="text-base font-black uppercase tracking-wider text-black pb-2 border-b-2 border-[#fedb00] mb-5" style="font-family: 'Montserrat', sans-serif;">
                        LEGGI ALTRO
                    </h3>

                    @if(isset($altriArticoli) && $altriArticoli->count() > 0)
                        <ul class="divide-y divide-gray-100">
                            @foreach($altriArticoli as $altro)
                                <li>
                                    <a href="{{ url('/' . $altro->slug) }}" class="block py-3 text-xs font-bold uppercase tracking-wider text-zinc-800 hover:text-[#fedb00] transition-colors">
                                        {{ $altro->titolo }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </aside>

        </div>
    </div>
</div>

<style>
.bicap-article-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 16px;
    box-sizing: border-box;
    width: 100%;
}
@media (min-width: 640px) {
    .bicap-article-wrapper {
        padding: 0 24px;
    }
}
@media (min-width: 1024px) {
    .bicap-article-wrapper {
        padding: 0 32px;
    }
}

.bicap-article-row {
    display: flex;
    flex-direction: column;
    gap: 40px;
    align-items: flex-start;
    width: 100%;
    box-sizing: border-box;
}

.bicap-article-main {
    width: 100%;
    min-width: 0;
    flex: 1 1 100%;
    box-sizing: border-box;
}

.bicap-article-sidebar {
    width: 100%;
    flex-shrink: 0;
    box-sizing: border-box;
}

.bicap-sidebar-box {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    border-radius: 4px;
}

@media (min-width: 1024px) {
    .bicap-article-row {
        flex-direction: row;
        gap: 56px;
    }
    .bicap-article-main {
        flex: 1 1 0%;
        width: calc(100% - 336px);
        max-width: calc(100% - 336px);
    }
    .bicap-article-sidebar {
        width: 280px;
        max-width: 280px;
        flex: 0 0 280px;
    }
    .bicap-sidebar-box {
        position: sticky;
        top: 110px;
    }
}

.bicap-article-content img {
    max-width: 100% !important;
    height: auto !important;
    margin: 16px 0 !important;
}
.bicap-article-content p {
    margin-bottom: 1rem !important;
    line-height: 1.7 !important;
}
.bicap-article-content strong {
    font-weight: 700 !important;
    color: #000000 !important;
}
.bicap-article-content ul, .bicap-article-content ol {
    margin-left: 20px !important;
    margin-bottom: 1rem !important;
    list-style: disc !important;
}
.bicap-article-content ol {
    list-style: decimal !important;
}
.bicap-article-content h1, .bicap-article-content h2, .bicap-article-content h3 {
    margin-top: 1.5rem !important;
    margin-bottom: 0.75rem !important;
    font-weight: 700 !important;
    color: #000000 !important;
}
</style>
@endsection
