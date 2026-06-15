{{-- Graceful placeholder shown when a product section has no items yet.
     Keeps the landing-page grid full so no section ever looks broken. --}}
<div class="reveal">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        @for ($i = 0; $i < 4; $i++)
            <div class="rounded-2xl border border-gray-100 bg-white overflow-hidden">
                <div class="aspect-[4/5] skel"></div>
                <div class="p-4 space-y-2">
                    <div class="skel h-3 w-3/4"></div>
                    <div class="skel h-3 w-1/2"></div>
                </div>
            </div>
        @endfor
    </div>
    <div class="text-center mt-8">
        <p class="text-gray-500 text-sm">More products are on the way.</p>
        <a href="{{ route('shop.catalog') }}" class="inline-flex items-center gap-2 mt-2 text-sm font-semibold" style="color:var(--rose);">
            Browse the catalog <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>
