@extends('shop.layouts.app')
@section('title', 'Choose your gift')

@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8"
         x-data="{ picked: @js(collect($gifts)->whereIn('product_id', $chosen)->pluck('id')->values()), max: {{ (int) $allowed }},
                   toggle(id) { const i = this.picked.indexOf(id);
                                if (i >= 0) this.picked.splice(i, 1);
                                else if (this.max === 1) this.picked = [id];
                                else if (this.picked.length < this.max) this.picked.push(id); } }">

        <div class="text-center mb-8">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-rose-50 text-rose-500 text-2xl mb-3"><i class="fas fa-gift"></i></div>
            <h1 class="display text-3xl sm:text-4xl font-bold">
                @if ($allowed > 0)
                    Choose {{ $allowed === 1 ? 'a gift' : "up to {$allowed} gifts" }}
                @else
                    Gifts
                @endif
            </h1>
            <p class="text-gray-500 text-sm mt-2">
                @if ($allowed > 0)
                    A little thank-you from Almufeed with your order. Pick {{ $allowed === 1 ? 'one' : "up to {$allowed}" }}, or skip.
                @endif
                @if ($next)
                    <span class="block mt-1 font-semibold text-rose-600">Add {{ shop_price($next['min'] - $paid) }} more to your cart to choose {{ $next['gifts'] }} {{ Str::plural('gift', $next['gifts']) }}.</span>
                @endif
            </p>
        </div>

        @include('shop.partials.notice', ['class' => 'mb-6'])

        <form method="POST" action="{{ route('shop.checkout.gift.choose') }}">
            @csrf
            <template x-for="id in picked" :key="id"><input type="hidden" name="gifts[]" :value="id"></template>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($gifts as $g)
                    @php $p = $g->product; @endphp
                    <button type="button" @click="toggle({{ $g->id }})" @if ($allowed < 1) disabled @endif
                            class="relative text-left bg-white rounded-2xl border-2 overflow-hidden transition hover:shadow-md disabled:cursor-default disabled:hover:shadow-none"
                            :class="picked.includes({{ $g->id }}) ? 'border-rose-500 ring-2 ring-rose-200' : 'border-gray-100'">
                        <img src="{{ shop_image($p->image) }}" alt="" class="w-full aspect-square object-cover" style="background:#f5f1e8;">
                        <span x-show="picked.includes({{ $g->id }})" class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-rose-500 text-white text-xs"><i class="fas fa-check"></i></span>
                        <div class="p-3">
                            <div class="font-semibold text-sm text-gray-900 line-clamp-2">{{ $p->name }}</div>
                            @if ($p->name_ur)<div class="urdu text-sm text-gray-600 line-clamp-1" dir="rtl" lang="ur">{{ $p->name_ur }}</div>@endif
                            <div class="mt-1 text-sm font-bold text-rose-600">{{ $g->price > 0 ? shop_price($g->price) : 'Free' }}</div>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="sticky bottom-0 mt-8 -mx-4 sm:mx-0 bg-white/95 backdrop-blur border-t sm:border sm:rounded-2xl px-4 py-3 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <button name="action" value="skip" class="btn btn-ghost justify-center">No thanks</button>
                @if ($allowed > 0)
                    <div class="flex items-center justify-between sm:justify-end gap-4">
                        <span class="text-sm text-gray-500"><b x-text="picked.length"></b> of {{ $allowed }} chosen</span>
                        <button name="action" value="choose" :disabled="!picked.length" class="btn btn-dark disabled:opacity-50">Continue to checkout <i class="fas fa-arrow-right text-xs"></i></button>
                    </div>
                @endif
            </div>
        </form>
    </div>
</section>
@endsection
