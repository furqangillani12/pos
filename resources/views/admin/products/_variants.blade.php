{{-- Colour × size variants (Daraz-style). Each combination has its own prices,
     stock, barcode and on/off; each colour can have its own photo. --}}
@php
    $variantRows = old('variants', isset($product) && $product->exists
        ? $product->variants->map(fn ($v) => [
            'id' => $v->id, 'color' => $v->color, 'size' => $v->size, 'barcode' => $v->barcode,
            'sale_price' => $v->sale_price, 'resale_price' => $v->resale_price, 'wholesale_price' => $v->wholesale_price,
            'stock' => (float) $v->stock, 'is_active' => $v->is_active,
        ])->values()->all()
        : []);
    $hasVariants = (bool) old('has_variants', $product->has_variants ?? false);
    $colorImages = (isset($product) && $product->exists) ? ($product->color_images ?? []) : [];
@endphp

<div class="mt-2 rounded-md border border-pink-200 bg-pink-50/40 p-4"
     x-data="productVariants({
        enabled: @js($hasVariants),
        rows: @js($variantRows),
        colorImages: @js(collect($colorImages)->map(fn ($p) => shop_image($p))),
     })">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="hidden" name="has_variants" value="0">
        <input type="checkbox" name="has_variants" value="1" x-model="enabled" class="h-4 w-4 text-pink-600 border-gray-300 rounded">
        <span class="text-sm font-semibold text-gray-800"><i class="fas fa-palette text-pink-500 mr-1"></i> This product has colors / sizes</span>
        <span class="text-xs text-gray-500">— each color/size combination gets its own price and stock</span>
    </label>

    <div x-show="enabled" x-cloak class="mt-4 space-y-4">
        {{-- Colours & sizes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <template x-for="dim in ['colors', 'sizes']" :key="dim">
                <div>
                    <label class="block text-sm font-medium text-gray-700" x-text="dim === 'colors' ? 'Colors' : 'Sizes'"></label>
                    <div class="mt-1 flex flex-wrap items-center gap-1.5 border border-gray-300 rounded-md bg-white px-2 py-1.5 min-h-[42px]">
                        <template x-for="(t, i) in $data[dim]" :key="t">
                            <span class="inline-flex items-center gap-1 rounded-full bg-pink-100 text-pink-800 text-xs font-semibold px-2 py-0.5">
                                <span x-text="t"></span>
                                <button type="button" @click="$data[dim].splice(i, 1); build()" class="text-pink-500 hover:text-pink-800">&times;</button>
                            </span>
                        </template>
                        <input type="text" class="flex-1 min-w-[120px] border-0 p-1 text-sm focus:ring-0"
                               :placeholder="dim === 'colors' ? 'e.g. Black, Red… (Enter)' : 'e.g. S, M, L / 52, 54… (Enter)'"
                               @keydown.enter.prevent="addTag(dim, $event)" @keydown.comma.prevent="addTag(dim, $event)" @blur="addTag(dim, $event)">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1" x-text="dim === 'colors' ? 'Leave empty if the product only has sizes.' : 'Leave empty if the product only has colors.'"></p>
                </div>
            </template>
        </div>

        {{-- Colour photos --}}
        <div x-show="colors.length" class="rounded-md bg-white border border-gray-200 p-3">
            <div class="text-sm font-medium text-gray-700 mb-2"><i class="fas fa-image text-gray-400"></i> Photo for each color <span class="text-xs text-gray-400 font-normal">(shown on the website when the customer picks that color)</span></div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <template x-for="(c, i) in colors" :key="c">
                    <div class="border border-gray-200 rounded-md p-2 text-xs">
                        <div class="font-semibold text-gray-700 mb-1" x-text="c"></div>
                        <template x-if="colorImages[c]">
                            <div class="mb-1">
                                <img :src="colorImages[c]" class="h-14 w-14 object-cover rounded border">
                                <label class="flex items-center gap-1 mt-1 text-gray-500"><input type="checkbox" name="remove_color_image[]" :value="c" class="h-3 w-3"> Remove</label>
                            </div>
                        </template>
                        <input type="hidden" :name="'color_image_names[' + i + ']'" :value="c">
                        <input type="file" accept="image/*" :name="'color_image[' + i + ']'" class="w-full text-[11px]">
                    </div>
                </template>
            </div>
        </div>

        {{-- Combinations --}}
        <div x-show="rows.length" class="rounded-md bg-white border border-gray-200 overflow-x-auto">
            <div class="flex flex-wrap items-end gap-2 p-3 border-b border-gray-100 bg-gray-50 text-xs">
                <span class="font-semibold text-gray-700 mr-1">Apply to all:</span>
                <input type="number" step="0.01" min="0" x-model="bulk.sale_price" placeholder="Walk-in price" class="w-28 border-gray-300 rounded px-2 py-1">
                <input type="number" step="0.01" min="0" x-model="bulk.resale_price" placeholder="Reseller price" class="w-28 border-gray-300 rounded px-2 py-1">
                <input type="number" step="0.01" min="0" x-model="bulk.wholesale_price" placeholder="Wholesale price" class="w-28 border-gray-300 rounded px-2 py-1">
                <input type="number" step="0.01" min="0" x-model="bulk.stock" placeholder="Stock" class="w-20 border-gray-300 rounded px-2 py-1">
                <button type="button" @click="applyBulk()" class="px-3 py-1 rounded bg-pink-600 text-white font-semibold">Apply</button>
                <span class="text-gray-400">Leave a price empty to use the product's price above.</span>
            </div>
            <table class="w-full text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-2 py-2 text-left">Color</th>
                        <th class="px-2 py-2 text-left">Size</th>
                        <th class="px-2 py-2 text-left">Walk-in Rs</th>
                        <th class="px-2 py-2 text-left">Reseller Rs</th>
                        <th class="px-2 py-2 text-left">Wholesale Rs</th>
                        <th class="px-2 py-2 text-left">Stock</th>
                        <th class="px-2 py-2 text-left">Barcode</th>
                        <th class="px-2 py-2 text-center">On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(r, i) in rows" :key="r.key">
                        <tr :class="r.is_active ? '' : 'opacity-50'">
                            <td class="px-2 py-1.5 font-semibold" x-text="r.color || '—'"></td>
                            <td class="px-2 py-1.5 font-semibold" x-text="r.size || '—'"></td>
                            <td class="px-1 py-1"><input type="number" step="0.01" min="0" :name="'variants[' + i + '][sale_price]'" x-model="r.sale_price" class="w-24 border-gray-300 rounded px-2 py-1 text-xs"></td>
                            <td class="px-1 py-1"><input type="number" step="0.01" min="0" :name="'variants[' + i + '][resale_price]'" x-model="r.resale_price" class="w-24 border-gray-300 rounded px-2 py-1 text-xs"></td>
                            <td class="px-1 py-1"><input type="number" step="0.01" min="0" :name="'variants[' + i + '][wholesale_price]'" x-model="r.wholesale_price" class="w-24 border-gray-300 rounded px-2 py-1 text-xs"></td>
                            <td class="px-1 py-1"><input type="number" step="0.01" min="0" :name="'variants[' + i + '][stock]'" x-model.number="r.stock" class="w-20 border-gray-300 rounded px-2 py-1 text-xs"></td>
                            <td class="px-1 py-1"><input type="text" :name="'variants[' + i + '][barcode]'" x-model="r.barcode" class="w-28 border-gray-300 rounded px-2 py-1 text-xs" placeholder="optional"></td>
                            <td class="px-2 py-1 text-center">
                                <input type="hidden" :name="'variants[' + i + '][is_active]'" :value="r.is_active ? 1 : 0">
                                <input type="checkbox" x-model="r.is_active" class="h-4 w-4 text-pink-600 rounded">
                                <input type="hidden" :name="'variants[' + i + '][id]'" :value="r.id || ''">
                                <input type="hidden" :name="'variants[' + i + '][color]'" :value="r.color || ''">
                                <input type="hidden" :name="'variants[' + i + '][size]'" :value="r.size || ''">
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr><td colspan="5" class="px-2 py-2 text-right font-semibold text-gray-600">Total stock</td><td class="px-2 py-2 font-bold" x-text="totalStock()"></td><td colspan="2" class="px-2 py-2 text-gray-400">→ becomes the product's stock</td></tr>
                </tfoot>
            </table>
        </div>
        <p x-show="!rows.length" class="text-xs text-gray-500">Add colors and/or sizes — a row is created automatically for each combination.</p>
    </div>
</div>

<script>
    window.productVariants = function (cfg) {
        return {
            enabled: !!cfg.enabled,
            rows: [],
            colors: [],
            sizes: [],
            colorImages: cfg.colorImages || {},
            bulk: { sale_price: '', resale_price: '', wholesale_price: '', stock: '' },
            init() {
                this.rows = (cfg.rows || []).map(r => this.norm(r));
                this.colors = [...new Set(this.rows.map(r => r.color).filter(Boolean))];
                this.sizes  = [...new Set(this.rows.map(r => r.size).filter(Boolean))];
                this.$watch('enabled', () => this.syncMainStock());
                this.$watch('rows', () => this.syncMainStock(), { deep: true });
                this.syncMainStock();
            },
            norm(r) {
                return {
                    key: (r.color || '') + '|' + (r.size || ''),
                    id: r.id || null, color: r.color || '', size: r.size || '', barcode: r.barcode || '',
                    sale_price: r.sale_price ?? '', resale_price: r.resale_price ?? '', wholesale_price: r.wholesale_price ?? '',
                    stock: Number(r.stock) || 0,
                    is_active: r.is_active === undefined ? true : (r.is_active === true || r.is_active === 1 || r.is_active === '1'),
                };
            },
            addTag(dim, e) {
                const parts = String(e.target.value || '').split(',').map(s => s.trim()).filter(Boolean);
                parts.forEach(p => { if (!this[dim].some(x => x.toLowerCase() === p.toLowerCase())) this[dim].push(p); });
                e.target.value = '';
                if (parts.length) this.build();
            },
            // Rebuild the combination list, keeping values already typed for a combination.
            build() {
                const old = Object.fromEntries(this.rows.map(r => [r.key, r]));
                const cs = this.colors.length ? this.colors : [''];
                const ss = this.sizes.length ? this.sizes : [''];
                const next = [];
                cs.forEach(c => ss.forEach(s => {
                    if (!c && !s) return;
                    const key = c + '|' + s;
                    next.push(old[key] || this.norm({ color: c, size: s }));
                }));
                this.rows = next;
            },
            applyBulk() {
                this.rows.forEach(r => {
                    ['sale_price', 'resale_price', 'wholesale_price'].forEach(f => { if (this.bulk[f] !== '') r[f] = this.bulk[f]; });
                    if (this.bulk.stock !== '') r.stock = Number(this.bulk.stock) || 0;
                });
            },
            totalStock() { return this.rows.reduce((t, r) => t + (Number(r.stock) || 0), 0); },
            // With variants, the product's own stock = sum of its variants (read-only).
            syncMainStock() {
                const el = document.getElementById('stock_quantity');
                if (!el) return;
                if (this.enabled && this.rows.length) { el.value = this.totalStock(); el.readOnly = true; el.classList.add('bg-gray-100'); }
                else { el.readOnly = false; el.classList.remove('bg-gray-100'); }
            },
        };
    };
</script>
