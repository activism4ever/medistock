@extends('layouts.app')
@section('title','New Sale') @section('page-title','New Sale / Dispense')
@section('content')

@if(auth()->user()->drawer_number)
<div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3 flex items-center gap-2">
  <span class="text-yellow-600 font-bold text-sm">🗄 You are on Drawer {{ auth()->user()->drawer_number }}</span>
</div>
@endif

<div class="pt-2" x-data="posSystem()">
  <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- ── Left: Stock browser ─────────────────────────── --}}
    <div class="lg:col-span-3">
      <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <div class="mb-4">
          <input type="text" x-model="search" placeholder="&#x1F50D; Search medicine by name..."
            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="space-y-1 max-h-[32rem] overflow-y-auto pr-1">
          <template x-for="item in filteredStock" :key="item.id">
            <div class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-blue-50 cursor-pointer border border-transparent hover:border-blue-200 transition"
              @click="addToCart(item)">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate" x-text="item.medicine_name"></p>
                <p class="text-xs text-gray-400 truncate"
                  x-text="'Batch: ' + item.batch_number + '  |  Exp: ' + item.expiry_date + '  |  Stock: ' + item.qty"></p>
              </div>
              <div class="text-right ml-4 flex-shrink-0">
                <p class="text-sm font-bold text-green-700">&#x20A6;<span x-text="parseFloat(item.price).toFixed(2)"></span></p>
                <p class="text-xs text-blue-500 font-medium">+ Add</p>
              </div>
            </div>
          </template>
          <div x-show="filteredStock.length===0" class="py-12 text-center text-sm text-gray-400">
            No available stock found
          </div>
        </div>
      </div>
    </div>

    {{-- ── Right: Cart ──────────────────────────────────── --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl border border-gray-200 sticky top-20">
        <div class="px-5 py-4 border-b border-gray-100">
          <h3 class="font-semibold text-gray-800">Current Sale</h3>
        </div>

        <form action="{{ route('sales.store') }}" method="POST">
          @csrf
          <div class="px-5 py-4 space-y-4">

            {{-- Patient Info --}}
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Patient Name *</label>
              <input type="text" name="patient_name" required
                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Full patient name">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Patient ID</label>
              <input type="text" name="patient_id"
                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Hospital file / ID number">
            </div>

            {{-- ── Sale Type Toggle ─────────────────────── --}}
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Sale Type</label>
              <div class="flex rounded-xl overflow-hidden border border-gray-300">
                <label class="flex-1 text-center cursor-pointer">
                  <input type="radio" name="sale_type" value="normal" x-model="saleType" class="sr-only">
                  <span class="block px-3 py-2 text-sm font-medium transition"
                    :class="saleType==='normal' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'">
                    Normal
                  </span>
                </label>
                <label class="flex-1 text-center cursor-pointer border-l border-gray-300">
                  <input type="radio" name="sale_type" value="insurance" x-model="saleType" class="sr-only">
                  <span class="block px-3 py-2 text-sm font-medium transition"
                    :class="saleType==='insurance' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'">
                    🏥 Insurance
                  </span>
                </label>
              </div>
            </div>

            {{-- ── Insurance Fields ─────────────────────── --}}
            <div x-show="saleType==='insurance'" x-transition class="space-y-3 bg-green-50 border border-green-200 rounded-xl p-4">
              <p class="text-xs font-bold text-green-700 uppercase tracking-wide">Insurance Details</p>

              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Insurance Scheme *</label>
                <select name="insurance_scheme_id"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                  <option value="">-- Select Scheme --</option>
                  @foreach($schemes as $scheme)
                  <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                  @endforeach
                </select>
                @error('insurance_scheme_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Enrolee Name *</label>
                <input type="text" name="enrolee_name"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Full name on insurance card">
                @error('enrolee_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Enrolee ID *</label>
                <input type="text" name="enrolee_id"
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Insurance ID number">
                @error('enrolee_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>

              {{-- Co-payment preview --}}
              <div x-show="total > 0" class="bg-white rounded-xl border border-green-200 p-3 space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-600">
                  <span>Total Drug Cost</span>
                  <span class="font-semibold">&#x20A6;<span x-text="total.toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between text-green-700">
                  <span>Insurance Covers (90%)</span>
                  <span class="font-semibold">&#x20A6;<span x-text="(total*0.9).toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between text-red-600 font-bold border-t border-green-100 pt-1.5">
                  <span>Patient Pays (10%)</span>
                  <span>&#x20A6;<span x-text="(total*0.1).toFixed(2)"></span></span>
                </div>
              </div>
            </div>

            {{-- Cart items --}}
            <div>
              <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Items</p>
              <div class="space-y-2 max-h-60 overflow-y-auto" x-show="cart.length>0">
                <template x-for="(item,index) in cart" :key="item.batch_id">
                  <div class="bg-gray-50 rounded-xl px-3 py-3">
                    <input type="hidden" :name="'items['+index+'][batch_id]'" :value="item.batch_id">
                    <div class="flex justify-between items-start mb-2">
                      <p class="text-xs font-semibold text-gray-700 flex-1 pr-2 leading-tight" x-text="item.medicine_name"></p>
                      <button type="button" @click="remove(index)" class="text-red-400 hover:text-red-600 text-sm leading-none">&times;</button>
                    </div>
                    <div class="flex items-center gap-2">
                      <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                        <button type="button" @click="dec(index)" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-sm">−</button>
                        <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.qty" min="1" :max="item.max"
                          @change="totals()"
                          class="w-14 text-center text-sm py-1 border-0 focus:outline-none focus:ring-0">
                        <button type="button" @click="inc(index)" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-sm">+</button>
                      </div>
                      <span class="text-xs text-gray-400">× &#x20A6;<span x-text="parseFloat(item.price).toFixed(2)"></span></span>
                      <span class="ml-auto text-sm font-bold text-gray-800">&#x20A6;<span x-text="(item.price*item.qty).toFixed(2)"></span></span>
                    </div>
                  </div>
                </template>
              </div>
              <div x-show="cart.length===0"
                class="py-8 text-center text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                Click items on the left to add them to the cart
              </div>
            </div>

            {{-- Total --}}
            <div x-show="cart.length>0">
              <template x-if="saleType==='normal'">
                <div class="bg-blue-600 rounded-xl px-4 py-3 flex justify-between items-center">
                  <span class="text-white font-semibold text-sm">TOTAL</span>
                  <span class="text-white font-bold text-xl">&#x20A6;<span x-text="total.toFixed(2)"></span></span>
                </div>
              </template>
              <template x-if="saleType==='insurance'">
                <div class="bg-green-600 rounded-xl px-4 py-3 flex justify-between items-center">
                  <span class="text-white font-semibold text-sm">PATIENT PAYS (10%)</span>
                  <span class="text-white font-bold text-xl">&#x20A6;<span x-text="(total*0.1).toFixed(2)"></span></span>
                </div>
              </template>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Notes</label>
              <textarea name="notes" rows="2"
                class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Optional notes..."></textarea>
            </div>

          </div>

          <div class="px-5 pb-5">
            <button type="submit" :disabled="cart.length===0"
              class="w-full disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition text-sm shadow-lg disabled:shadow-none"
              :class="saleType==='insurance' ? 'bg-green-600 hover:bg-green-700 shadow-green-500/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20'">
              <span x-text="saleType==='insurance' ? 'Complete Insurance Sale →' : 'Complete Sale →'"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const _stock = @json($stockData);
function posSystem() {
    return {
        search:   '',
        cart:     [],
        total:    0,
        saleType: 'normal',
        stock:    _stock,
        get filteredStock() {
            if (!this.search) return this.stock;
            const q = this.search.toLowerCase();
            return this.stock.filter(i => i.medicine_name.toLowerCase().includes(q));
        },
        addToCart(item) {
            const existing = this.cart.find(c => c.batch_id === item.batch_id);
            if (existing) {
                if (existing.qty < item.qty) { existing.qty++; this.totals(); }
                return;
            }
            this.cart.push({ batch_id: item.batch_id, medicine_name: item.medicine_name, price: item.price, qty: 1, max: item.qty });
            this.totals();
        },
        remove(i)  { this.cart.splice(i,1); this.totals(); },
        inc(i)     { if(this.cart[i].qty < this.cart[i].max) { this.cart[i].qty++; this.totals(); } },
        dec(i)     { if(this.cart[i].qty > 1) { this.cart[i].qty--; this.totals(); } },
        totals()   { this.total = this.cart.reduce((s,i) => s + i.price*i.qty, 0); }
    }
}
</script>
@endpush
@endsection