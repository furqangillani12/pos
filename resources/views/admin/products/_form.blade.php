{{-- Add / Edit Product (client design, Oct 2026). Wrapped by create/edit, which own the <form>. --}}
@php
    $p        = $product ?? null;
    $isEdit   = $p && $p->exists;
    $roots    = $categories->whereNull('parent_id')->sortBy('name')->values();
    $children = $categories->whereNotNull('parent_id')->groupBy('parent_id')
                    ->map(fn ($g) => $g->sortBy('name')->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values());

    // Price grid: saved breakdown, else start from the product's current prices.
    $cols = ['cost' => 'cost_price', 'wholesale' => 'wholesale_price', 'resale' => 'resale_price', 'walkin' => 'sale_price'];
    $saved = $isEdit ? ($p->price_breakdown ?? []) : [];
    $pricing = [];
    foreach ($cols as $col => $field) {
        $final = (float) ($p->$field ?? 0);
        $base  = $final;
        if ($col === 'walkin' && $isEdit && (float) ($p->price ?? 0) > $final) $base = (float) $p->price;
        $pricing[$col] = $saved[$col] ?? ['base' => $base, 'disc_pct' => null, 'disc_amt' => $base > $final ? round($base - $final, 2) : null, 'chg_pct' => null, 'chg_amt' => null];
    }
    $pricing = old('pricing', $pricing);

    // Product type rows: variants, or one plain row for a product without variations.
    if ($isEdit && $p->has_variants) {
        $rows = $p->variants->where('is_active', true)->map(fn ($v) => [
            'id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'price' => $v->base_price,
            'qty' => (float) $v->stock, 'weight' => $v->weight, 'reorder' => $v->reorder_level,
            'images' => collect($v->images ?? [])->map(fn ($i) => ['path' => $i, 'url' => shop_image($i)])->values(),
        ])->values()->all();
    } else {
        $rows = [[
            'id' => null, 'size' => null, 'color' => null, 'price' => null,
            'qty' => $isEdit ? (float) ($p->branch_stock ?? $p->stock_quantity ?? 0) : 0,
            'weight' => $isEdit ? $p->weight : null, 'reorder' => $isEdit ? $p->reorder_level : 5, 'images' => [],
        ]];
    }

    $charges = $isEdit ? ($p->other_charges ?? []) : [];
    if ($isEdit && empty($charges) && (float) ($p->packing_charge ?? 0) > 0) {
        $charges = [['title' => $p->packing_label ?: 'Packing charges', 'amount' => (float) $p->packing_charge]];
    }

    $gallery = $isEdit ? collect([$p->image])->merge($p->gallery ?? [])->filter()->unique()->values() : collect();
    $selectedExtra = old('categories', $isEdit ? $p->categories->pluck('id')->all() : []);
    $inp = 'block w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm focus:ring-blue-500 focus:border-blue-500';
@endphp

<div x-data="productForm({
        pricing: @js($pricing),
        rows: @js($rows),
        charges: @js(array_values($charges)),
        children: @js($children),
        category: @js((string) old('category_id', $p->category_id ?? '')),
        subcategory: @js((string) old('subcategory_id', $p->subcategory_id ?? '')),
     })" class="space-y-6">

    @if ($errors->any())
        <div class="rounded-md bg-red-50 border border-red-200 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- ── Product details ── --}}
    <div class="rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-2 bg-gray-50 border-b font-semibold text-gray-800">Product details</div>
        <div class="p-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600">Name (English) *</label>
                    <input type="text" name="name" value="{{ old('name', $p->name ?? '') }}" required class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Name (Urdu)</label>
                    <input type="text" name="name_ur" dir="rtl" value="{{ old('name_ur', $p->name_ur ?? '') }}" class="{{ $inp }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Barcode</label>
                <input type="text" name="barcode" value="{{ old('barcode', $p->barcode ?? '') }}" class="{{ $inp }}">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600">Category *</label>
                <select name="category_id" x-model="category" @change="subcategory = ''" required class="{{ $inp }}">
                    <option value="">Select category</option>
                    @foreach ($roots as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Sub category</label>
                <select name="subcategory_id" x-model="subcategory" class="{{ $inp }}" :disabled="!subOptions.length">
                    <option value="" x-text="subOptions.length ? 'Select sub category' : 'No sub categories'"></option>
                    <template x-for="s in subOptions" :key="s.id">
                        <option :value="String(s.id)" x-text="s.name" :selected="String(s.id) === subcategory"></option>
                    </template>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600">Unit</label>
                    <select name="unit_id" class="{{ $inp }}">
                        <option value="">—</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id', $p->unit_id ?? '') == $unit->id)>{{ $unit->name }}{{ $unit->abbreviation ? ' (' . $unit->abbreviation . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Box / Place</label>
                    <input type="text" name="rank" value="{{ old('rank', $p->rank ?? '') }}" placeholder="e.g. A1, Shelf 3" class="{{ $inp }}">
                </div>
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-medium text-gray-600">Also show in categories <span class="text-gray-400 font-normal">(optional — hold Ctrl/Cmd to select several)</span></label>
                <select name="categories[]" multiple size="3" class="{{ $inp }}">
                    @foreach ($categories->sortBy('name') as $c)
                        <option value="{{ $c->id }}" @selected(in_array($c->id, (array) $selectedExtra))>{{ $c->parent_id ? '— ' : '' }}{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600">Note (English)</label>
                    <textarea name="note" rows="2" placeholder="Important note, e.g. handle with care" class="{{ $inp }} bg-amber-50 border-amber-200">{{ old('note', $p->note ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Note (Urdu)</label>
                    <textarea name="note_ur" rows="2" dir="rtl" class="{{ $inp }} bg-amber-50 border-amber-200">{{ old('note_ur', $p->note_ur ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Description (English)</label>
                    <textarea name="description" rows="3" class="{{ $inp }}">{{ old('description', $p->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600">Description (Urdu)</label>
                    <textarea name="description_ur" rows="3" dir="rtl" class="{{ $inp }}">{{ old('description_ur', $p->description_ur ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Price grid: Price − Discount + Charges = Final (internal calculation) ── --}}
    <div class="rounded-lg border border-gray-200 overflow-x-auto">
        <div class="px-4 py-2 bg-gray-50 border-b flex flex-wrap items-center justify-between gap-2">
            <span class="font-semibold text-gray-800">Price</span>
            <button type="button" @click="copyBaseToAll()" class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100">Copy walk-in price to all columns</button>
        </div>
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="text-xs text-gray-600">
                    <th class="px-3 py-2 text-left w-36"></th>
                    @foreach (['cost' => 'Cost price', 'wholesale' => 'Wholesale price', 'resale' => 'Resale price', 'walkin' => 'Walk-in price'] as $col => $label)
                        <th class="px-2 py-2 text-left">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr class="border-t">
                    <td class="px-3 py-2 font-medium text-gray-700">Price <span class="text-xs text-gray-400">Rs</span></td>
                    @foreach (array_keys($cols) as $col)
                        <td class="px-2 py-1.5"><input type="number" step="0.01" min="0" x-model.number="pricing.{{ $col }}.base" @input="recalc('{{ $col }}')" name="pricing[{{ $col }}][base]" class="{{ $inp }}"></td>
                    @endforeach
                </tr>
                <tr class="border-t bg-blue-50/60">
                    <td class="px-3 py-2 font-medium text-blue-800">Discount</td>
                    @foreach (array_keys($cols) as $col)
                        <td class="px-2 py-1.5">
                            <div class="flex gap-1">
                                <div class="relative w-1/2"><input type="number" step="0.01" min="0" x-model.number="pricing.{{ $col }}.disc_pct" @input="pctToAmt('{{ $col }}', 'disc')" name="pricing[{{ $col }}][disc_pct]" class="{{ $inp }} pr-5"><span class="absolute right-1.5 top-1.5 text-xs text-gray-400">%</span></div>
                                <div class="relative w-1/2"><input type="number" step="0.01" min="0" x-model.number="pricing.{{ $col }}.disc_amt" @input="amtToPct('{{ $col }}', 'disc')" name="pricing[{{ $col }}][disc_amt]" class="{{ $inp }} pr-6"><span class="absolute right-1.5 top-1.5 text-xs text-gray-400">Rs</span></div>
                            </div>
                        </td>
                    @endforeach
                </tr>
                <tr class="border-t">
                    <td class="px-3 py-2 font-medium text-gray-700">Charges</td>
                    @foreach (array_keys($cols) as $col)
                        <td class="px-2 py-1.5">
                            <div class="flex gap-1">
                                <div class="relative w-1/2"><input type="number" step="0.01" min="0" x-model.number="pricing.{{ $col }}.chg_pct" @input="pctToAmt('{{ $col }}', 'chg')" name="pricing[{{ $col }}][chg_pct]" class="{{ $inp }} pr-5"><span class="absolute right-1.5 top-1.5 text-xs text-gray-400">%</span></div>
                                <div class="relative w-1/2"><input type="number" step="0.01" min="0" x-model.number="pricing.{{ $col }}.chg_amt" @input="amtToPct('{{ $col }}', 'chg')" name="pricing[{{ $col }}][chg_amt]" class="{{ $inp }} pr-6"><span class="absolute right-1.5 top-1.5 text-xs text-gray-400">Rs</span></div>
                            </div>
                        </td>
                    @endforeach
                </tr>
                <tr class="border-t bg-green-100">
                    <td class="px-3 py-2 font-bold text-red-700">Final price (auto)</td>
                    @foreach (array_keys($cols) as $col)
                        <td class="px-2 py-1.5">
                            <input type="number" step="0.01" min="0" :value="finalOf('{{ $col }}')" @change="setFinal('{{ $col }}', $event.target.value)" class="{{ $inp }} font-bold bg-white">
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <p class="px-4 py-2 text-xs text-gray-500 border-t">Final = Price − Discount + Charges. Type a % or an amount (the other fills in), or type the final price and the discount is worked out. The walk-in discount shows on the website as “% OFF”.</p>
    </div>

    {{-- ── Product types (size / colour rows) ── --}}
    <div class="rounded-lg border border-gray-200 overflow-x-auto">
        <div class="px-4 py-2 bg-gray-50 border-b font-semibold text-gray-800">Add product type</div>
        <table class="w-full text-sm min-w-[900px]">
            <thead class="text-xs text-gray-600">
                <tr>
                    <th class="px-2 py-2 text-left">Size</th>
                    <th class="px-2 py-2 text-left">Color</th>
                    <th class="px-2 py-2 text-left">Price <span class="font-normal text-gray-400">(optional)</span></th>
                    <th class="px-2 py-2 text-left">Qty</th>
                    <th class="px-2 py-2 text-left">Weight kg</th>
                    <th class="px-2 py-2 text-left">Gram</th>
                    <th class="px-2 py-2 text-left">Reorder level</th>
                    <th class="px-2 py-2 text-left">Images</th>
                    <th class="px-2 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(r, i) in rows" :key="r.key">
                    <tr class="border-t align-top">
                        <td class="px-2 py-1.5 w-28">
                            <input type="hidden" :name="'rows[' + i + '][id]'" :value="r.id || ''">
                            <select :name="'rows[' + i + '][size]'" x-model="r.size" class="{{ $inp }}">
                                <option value="">—</option>
                                @foreach ($sizes as $s)<option value="{{ $s->name }}">{{ $s->name }}</option>@endforeach
                                <template x-if="r.size && !sizeList.includes(r.size)"><option :value="r.size" x-text="r.size"></option></template>
                            </select>
                        </td>
                        <td class="px-2 py-1.5 w-32">
                            <select :name="'rows[' + i + '][color]'" x-model="r.color" class="{{ $inp }}">
                                <option value="">—</option>
                                @foreach ($colors as $c)<option value="{{ $c->name }}">{{ $c->name }}</option>@endforeach
                                <template x-if="r.color && !colorList.includes(r.color)"><option :value="r.color" x-text="r.color"></option></template>
                            </select>
                        </td>
                        <td class="px-2 py-1.5 w-24"><input type="number" step="0.01" min="0" :name="'rows[' + i + '][price]'" x-model="r.price" placeholder="same" class="{{ $inp }}" title="Walk-in list price for this type. Leave empty to use the product price."></td>
                        <td class="px-2 py-1.5 w-20"><input type="number" step="0.01" min="0" :name="'rows[' + i + '][qty]'" x-model="r.qty" class="{{ $inp }}"></td>
                        <td class="px-2 py-1.5 w-24"><input type="number" step="0.001" min="0" :name="'rows[' + i + '][weight]'" x-model="r.weight" @input="r.gram = r.weight === '' ? '' : Math.round(parseFloat(r.weight) * 1000)" class="{{ $inp }}"></td>
                        <td class="px-2 py-1.5 w-24"><input type="number" step="1" min="0" x-model="r.gram" @input="r.weight = r.gram === '' ? '' : (parseFloat(r.gram) / 1000)" class="{{ $inp }}"></td>
                        <td class="px-2 py-1.5 w-20"><input type="number" step="0.01" min="0" :name="'rows[' + i + '][reorder]'" x-model="r.reorder" class="{{ $inp }}"></td>
                        <td class="px-2 py-1.5 w-44">
                            <div class="flex flex-wrap gap-1 mb-1" x-show="r.images.length">
                                <template x-for="img in r.images" :key="img.path">
                                    <label class="relative" title="Tick to remove">
                                        <img :src="img.url" class="h-9 w-9 object-cover rounded border">
                                        <input type="checkbox" :name="'rows[' + i + '][remove_images][]'" :value="img.path" class="absolute -top-1 -right-1 h-3 w-3">
                                    </label>
                                </template>
                            </div>
                            <input type="file" multiple accept="image/*" :name="'rows[' + i + '][images][]'" class="w-full text-[11px]">
                        </td>
                        <td class="px-2 py-1.5 whitespace-nowrap text-center">
                            <button type="button" @click="removeRow(i)" class="text-red-500 hover:text-red-700 px-1" title="Delete row"><i class="fas fa-trash-alt"></i></button>
                            <button type="button" @click="addRow(i)" class="text-gray-600 hover:text-blue-700 px-1" title="Add a row"><i class="fas fa-plus"></i></button>
                            <button type="button" @click="copyRow(i)" class="text-gray-500 hover:text-blue-700 px-1" title="Copy this row"><i class="far fa-copy"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <p class="px-4 py-2 text-xs text-gray-500 border-t">
            One row with no size / color = a product without variations. For variations add one row per size / color (use <i class="fas fa-plus"></i>).
            Sizes and colors come from <a href="{{ route('product-options.index') }}" target="_blank" class="text-blue-600 underline">Sizes &amp; Colors</a>.
            A row price is the walk-in list price for that type; its reseller / wholesale prices follow the same discounts as the grid above.
            Stock total: <b x-text="totalQty()"></b>
        </p>
    </div>

    {{-- ── Other charges billed to the customer (per unit) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-4 py-2 bg-gray-50 border-b font-semibold text-gray-800">Add other charges</div>
            <table class="w-full text-sm">
                <thead class="text-xs text-gray-600"><tr><th class="px-2 py-2 w-8">#</th><th class="px-2 py-2 text-left">Title</th><th class="px-2 py-2 text-left w-32">Amount (Rs)</th><th class="px-2 py-2 w-20 text-center">Action</th></tr></thead>
                <tbody>
                    <template x-for="(c, i) in charges" :key="c.key">
                        <tr class="border-t">
                            <td class="px-2 py-1.5 text-center text-gray-400" x-text="i + 1"></td>
                            <td class="px-2 py-1.5"><input type="text" :name="'charges[' + i + '][title]'" x-model="c.title" placeholder="e.g. Packing charges" maxlength="120" class="{{ $inp }}"></td>
                            <td class="px-2 py-1.5"><input type="number" step="0.01" min="0" :name="'charges[' + i + '][amount]'" x-model="c.amount" class="{{ $inp }}"></td>
                            <td class="px-2 py-1.5 text-center whitespace-nowrap">
                                <button type="button" @click="charges.splice(i, 1)" class="text-red-500 hover:text-red-700 px-1" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                <button type="button" @click="addCharge(i)" class="text-gray-600 hover:text-blue-700 px-1" title="Add another charge"><i class="fas fa-plus"></i></button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!charges.length"><td colspan="4" class="px-3 py-3 text-center"><button type="button" @click="addCharge(-1)" class="text-sm text-blue-600 hover:underline"><i class="fas fa-plus"></i> Add a charge</button></td></tr>
                </tbody>
            </table>
            <p class="px-4 py-2 text-xs text-gray-500 border-t">Added to the customer's bill for each unit sold (e.g. fragile packing). After adding one charge, press <i class="fas fa-plus"></i> to add another.</p>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 space-y-3">
            @foreach (['is_active' => 'Active product', 'track_inventory' => 'Track inventory', 'show_on_website' => 'Show on website'] as $f => $label)
                <label class="flex items-center justify-between text-sm text-gray-700">
                    <span>{{ $label }}</span>
                    <input type="checkbox" name="{{ $f }}" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                        {{ old($f, $p->$f ?? true) ? 'checked' : '' }}>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ── Images & video ── --}}
    <div class="rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-2 bg-gray-50 border-b font-semibold text-gray-800">Add more</div>
        <div class="p-4 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm">
                @if ($gallery->isNotEmpty())
                    <div class="flex flex-wrap gap-3 mt-3">
                        @foreach ($gallery as $g)
                            <div class="text-[11px] text-gray-600 text-center">
                                <img src="{{ shop_image($g) }}" class="h-16 w-16 object-cover rounded border {{ $g === ($p->image ?? null) ? 'ring-2 ring-blue-500' : '' }}">
                                <label class="flex items-center gap-1 mt-1"><input type="radio" name="main_image" value="{{ $g }}" @checked($g === ($p->image ?? null)) class="h-3 w-3"> Main</label>
                                <label class="flex items-center gap-1"><input type="checkbox" name="remove_gallery[]" value="{{ $g }}" class="h-3 w-3"> Remove</label>
                            </div>
                        @endforeach
                    </div>
                @endif
                <p class="mt-1 text-xs text-gray-500">Select several images at once. The first image is the main one unless you choose another.</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Video</label>
                <input type="file" name="video" accept="video/mp4,video/webm,video/quicktime" class="w-full text-sm">
                <input type="url" name="video_url" value="{{ old('video_url', $p->video_url ?? '') }}" placeholder="…or a YouTube / video link" class="{{ $inp }} mt-2">
                @if ($isEdit && ($p->video || $p->video_url))
                    <div class="mt-2 text-xs text-gray-600 flex items-center gap-2">
                        <i class="fas fa-video text-gray-400"></i>
                        @if ($p->video)<a href="{{ shop_image($p->video) }}" target="_blank" class="text-blue-600 underline">Current video file</a>@endif
                        <label class="flex items-center gap-1"><input type="checkbox" name="remove_video" value="1" class="h-3 w-3"> Remove video</label>
                    </div>
                @endif
                <p class="mt-1 text-xs text-gray-500">Shown at the end of the product page on the website (e.g. how to use / wear). MP4 up to 50 MB, or paste a link.</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-md">
            {{ $isEdit ? 'Update Product' : 'Create Product' }}
        </button>
    </div>
</div>

<script>
    window.productForm = function (cfg) {
        let seq = 0;
        const num = v => (v === '' || v === null || v === undefined || isNaN(parseFloat(v))) ? null : parseFloat(v);
        const r2 = v => Math.round(v * 100) / 100;
        return {
            pricing: cfg.pricing,
            rows: (cfg.rows || []).map(r => ({
                key: ++seq, id: r.id || null, size: r.size || '', color: r.color || '',
                price: r.price ?? '', qty: r.qty ?? 0, weight: r.weight ?? '',
                gram: (r.weight !== null && r.weight !== '' && r.weight !== undefined) ? Math.round(parseFloat(r.weight) * 1000) : '',
                reorder: r.reorder ?? '', images: r.images || [],
            })),
            charges: (cfg.charges || []).map(c => ({ key: ++seq, title: c.title || '', amount: c.amount ?? '' })),
            children: cfg.children || {},
            category: cfg.category || '',
            subcategory: cfg.subcategory || '',
            sizeList: @js($sizes->pluck('name')),
            colorList: @js($colors->pluck('name')),
            get subOptions() { return this.children[this.category] || []; },

            // ── price grid ──
            base(col) { return num(this.pricing[col].base) || 0; },
            pctToAmt(col, k) {
                const p = num(this.pricing[col][k + '_pct']);
                this.pricing[col][k + '_amt'] = p === null ? '' : r2(this.base(col) * p / 100);
            },
            amtToPct(col, k) {
                const a = num(this.pricing[col][k + '_amt']), b = this.base(col);
                this.pricing[col][k + '_pct'] = (a === null || !b) ? '' : r2(a / b * 100);
            },
            // Price changed: keep the % and recompute the amounts.
            recalc(col) {
                ['disc', 'chg'].forEach(k => { if (num(this.pricing[col][k + '_pct']) !== null) this.pctToAmt(col, k); });
            },
            finalOf(col) {
                const c = this.pricing[col];
                return r2(Math.max(0, this.base(col) - (num(c.disc_amt) || 0) + (num(c.chg_amt) || 0)));
            },
            // Final typed in: work the discount out from it (charges stay).
            setFinal(col, v) {
                const f = num(v); if (f === null) return;
                const c = this.pricing[col];
                if (!this.base(col)) { c.base = f; c.disc_amt = ''; c.disc_pct = ''; return; }
                const d = r2(this.base(col) + (num(c.chg_amt) || 0) - f);
                c.disc_amt = d > 0 ? d : '';
                if (d < 0) { c.chg_amt = r2((num(c.chg_amt) || 0) - d); this.amtToPct(col, 'chg'); }
                this.amtToPct(col, 'disc');
            },
            copyBaseToAll() {
                const b = this.pricing.walkin.base;
                ['cost', 'wholesale', 'resale'].forEach(col => { this.pricing[col].base = b; this.recalc(col); });
            },

            // ── rows ──
            blankRow() { return { key: ++seq, id: null, size: '', color: '', price: '', qty: 0, weight: '', gram: '', reorder: '', images: [] }; },
            addRow(i) { this.rows.splice(i + 1, 0, this.blankRow()); },
            copyRow(i) { const r = this.rows[i]; this.rows.splice(i + 1, 0, Object.assign(this.blankRow(), { size: r.size, color: r.color, price: r.price, weight: r.weight, gram: r.gram, reorder: r.reorder })); },
            removeRow(i) { this.rows.splice(i, 1); if (!this.rows.length) this.rows.push(this.blankRow()); },
            totalQty() { return this.rows.reduce((t, r) => t + (num(r.qty) || 0), 0); },

            addCharge(i) { this.charges.splice(i + 1, 0, { key: ++seq, title: '', amount: '' }); },
        };
    };
</script>
