<div x-data="posSystem()" class="h-[calc(100vh-8.5rem)] flex flex-col lg:flex-row gap-6">
    <!-- Left Column: Product Search & Quick Add -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl border border-slate-100 shadow-sm p-6 overflow-hidden">
        <!-- Search bar -->
        <div class="relative mb-4">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text"
                   x-model="searchQuery"
                   @input.debounce.300ms="searchProducts()"
                   placeholder="Buscar producto por nombre o código de barra (ej. BEB-001)..."
                   class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-sm">
        </div>

        <!-- Search Results Grid -->
        <div class="flex-1 overflow-y-auto pr-1">
            <template x-if="loading">
                <div class="flex items-center justify-center h-48 text-slate-400 text-sm">
                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Buscando productos...
                </div>
            </template>

            <template x-if="!loading && searchResults.length === 0 && searchQuery.length >= 2">
                <div class="flex items-center justify-center h-48 text-slate-400 text-sm">
                    No se encontraron productos disponibles con ese término.
                </div>
            </template>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <template x-for="product in searchResults" :key="product.id">
                    <button @click="addToCart(product)"
                            class="text-left p-3.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 transition group flex flex-col justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-mono" x-text="product.code || 'S/C'"></div>
                            <div class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 line-clamp-2 mt-1" x-text="product.name"></div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-slate-500">Stock: <b x-text="product.stock"></b></span>
                            <span class="text-sm font-bold text-emerald-600" x-text="'$' + parseFloat(product.sale_price).toFixed(2)"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Right Column: Cart / Checkout -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-cart-shopping mr-2 text-emerald-500"></i> Carrito Actual</span>
            <span class="text-xs font-normal text-slate-400" x-text="cart.length + ' ítems'"></span>
        </h3>

        <!-- Customer selection -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Cliente</label>
            <select x-model="customerId" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Cliente General</option>
                <?php foreach ($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> <?= $c['cedula'] ? '(' . htmlspecialchars($c['cedula']) . ')' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Cart items list -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100 pr-1 -mr-1">
            <template x-if="cart.length === 0">
                <div class="flex flex-col items-center justify-center h-48 text-slate-300 text-sm">
                    <i class="fa-solid fa-basket-shopping text-3xl mb-2"></i>
                    El carrito está vacío
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.product_id">
                <div class="py-3 flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <div class="text-sm font-semibold text-slate-800 truncate" x-text="item.name"></div>
                        <div class="text-xs text-slate-400" x-text="'$' + item.unit_price.toFixed(2) + ' c/u'"></div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                            <button @click="updateQty(index, -1)" class="px-2 py-0.5 hover:bg-slate-200 text-xs text-slate-600">-</button>
                            <span class="px-2 text-xs font-bold text-slate-700" x-text="item.quantity"></span>
                            <button @click="updateQty(index, 1)" class="px-2 py-0.5 hover:bg-slate-200 text-xs text-slate-600">+</button>
                        </div>
                        <div class="text-sm font-bold text-slate-800 w-16 text-right" x-text="'$' + (item.quantity * item.unit_price).toFixed(2)"></div>
                        <button @click="removeItem(index)" class="text-slate-300 hover:text-rose-500 text-xs p-1">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Totals & Payment -->
        <div class="pt-4 border-t border-slate-100 space-y-2 text-sm">
            <div class="flex justify-between text-slate-500">
                <span>Subtotal</span>
                <span x-text="'$' + subtotal.toFixed(2)"></span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>ITBIS (18%)</span>
                <span x-text="'$' + tax.toFixed(2)"></span>
            </div>
            <div class="flex justify-between text-lg font-bold text-slate-800 pt-2 border-t border-slate-100">
                <span>Total a Pagar</span>
                <span class="text-emerald-600" x-text="'$' + total.toFixed(2)"></span>
            </div>

            <div class="pt-2">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Monto Pagado</label>
                <input type="number" step="0.01" x-model.number="amountPaid" placeholder="0.00"
                       class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex justify-between text-sm font-semibold text-slate-700">
                <span>Cambio:</span>
                <span class="text-blue-600" x-text="'$' + changeAmount.toFixed(2)"></span>
            </div>

            <button @click="processSale()" :disabled="cart.length === 0 || processing"
                    class="w-full mt-4 py-3 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold rounded-xl shadow transition duration-150 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-check" x-show="!processing"></i>
                <i class="fa-solid fa-spinner fa-spin" x-show="processing"></i>
                <span x-text="processing ? 'Procesando...' : 'Completar Venta'"></span>
            </button>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        searchQuery: '',
        searchResults: [],
        loading: false,
        cart: [],
        customerId: '',
        amountPaid: 0,
        processing: false,

        init() {
            this.searchProducts();
        },

        searchProducts() {
            this.loading = true;
            fetch('/products/search?q=' + encodeURIComponent(this.searchQuery))
                .then(r => r.json())
                .then(data => {
                    this.searchResults = data;
                    this.loading = false;
                })
                .catch(() => this.loading = false);
        },

        addToCart(product) {
            const found = this.cart.find(i => i.product_id === product.id);
            if (found) {
                if (found.quantity < product.stock) {
                    found.quantity++;
                } else {
                    alert('Stock máximo disponible alcanzado.');
                }
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    unit_price: parseFloat(product.sale_price),
                    quantity: 1,
                    stock: product.stock
                });
            }
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            if (delta > 0 && item.quantity >= item.stock) {
                alert('No hay más stock disponible');
                return;
            }
            item.quantity += delta;
            if (item.quantity <= 0) {
                this.cart.splice(index, 1);
            }
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        },

        get tax() {
            return Math.round(this.subtotal * 0.18 * 100) / 100;
        },

        get total() {
            return Math.round((this.subtotal + this.tax) * 100) / 100;
        },

        get changeAmount() {
            return this.amountPaid >= this.total ? Math.round((this.amountPaid - this.total) * 100) / 100 : 0;
        },

        processSale() {
            if (this.cart.length === 0) return;
            if (this.amountPaid < this.total && this.amountPaid > 0) {
                if (!confirm('El monto pagado es menor al total. ¿Deseas continuar?')) return;
            }

            this.processing = true;
            fetch('/pos/store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    customer_id: this.customerId || null,
                    payment_method: 'efectivo',
                    amount_paid: this.amountPaid || this.total,
                    items: this.cart
                })
            })
            .then(r => r.json())
            .then(res => {
                this.processing = false;
                if (res.success) {
                    alert('¡Venta realizada con éxito! Factura: ' + res.invoice_number);
                    window.location.href = '/sales/' + res.sale_id;
                } else {
                    alert('Error: ' + res.message);
                }
            })
            .catch(err => {
                this.processing = false;
                alert('Error al conectar con el servidor.');
            });
        }
    };
}
</script>
