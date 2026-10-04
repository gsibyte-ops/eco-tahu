@extends('layouts.admin')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 txt-brand-hover px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold txt-primary">Daftar Kategori</h2>
        <p class="text-sm txt-secondary">Kelola kategori produk tahu dan limbah.</p>
    </div>
    <button type="button" onclick="openCreateModal()"
            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
        + Tambah Kategori
    </button>
</div>

<div class="glass-card overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold txt-muted uppercase tracking-wider bg-soft border-b bd-soft">
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Nama Kategori</th>
                <th class="px-6 py-3">Tipe</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kategori as $k)
                <tr class="border-b bd-soft hover:bg-soft transition">
                    <td class="px-6 py-4 text-sm txt-secondary">{{ $loop->iteration + ($kategori->currentPage() - 1) * $kategori->perPage() }}</td>
                    <td class="px-6 py-4 text-sm font-semibold txt-primary">{{ $k->nama_kategori }}</td>
                    <td class="px-6 py-4">
                        @if ($k->tipe === 'produk_tahu')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#d1fae5; color:#047857;">Produk Tahu</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#fef3c7; color:#b45309;">Produk Limbah</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button type="button" onclick="openEditModal({{ $k->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 txt-info rounded-lg text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        <form action="{{ route('admin.kategori.destroy', $k->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 txt-danger rounded-lg text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-10 text-center txt-muted">Belum ada kategori</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $kategori->links() }}</div>

{{-- ============================================ --}}
{{-- MODAL: CREATE --}}
{{-- ============================================ --}}
<div id="createModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 520px;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Tambah Kategori</h3>
            </div>
            <button onclick="closeCreateModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'create' && $errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="createForm" method="POST" action="{{ route('admin.kategori.store') }}"
              novalidate x-data="createFormData()">
            @csrf

            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                           minlength="3" maxlength="100"
                           oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Contoh: Tahu Putih">
                    <p class="text-xs txt-secondary mt-1">Hanya huruf & spasi. Minimal 3 karakter.</p>
                </div>

                <div class="relative" @click.away="tipeOpen = false">
                    <label class="block text-sm font-medium txt-primary mb-1">Tipe <span class="text-red-500">*</span></label>
                    <button type="button" @click="tipeOpen = !tipeOpen"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                        <span class="txt-primary" id="createTipeLabel" x-text="tipeLabel">-- Pilih Tipe --</span>
                        <svg class="w-4 h-4 txt-muted" :class="tipeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="tipeOpen" x-cloak
                         class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                        <button type="button" @click="selectTipe('produk_tahu', 'Produk Tahu')"
                                class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Produk Tahu</button>
                        <button type="button" @click="selectTipe('limbah', 'Produk Limbah')"
                                class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Produk Limbah</button>
                    </div>
                    <input type="hidden" name="tipe" id="createTipeHidden" :value="tipeValue">
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 border-t bd-soft bg-soft flex-shrink-0">
            <button type="button" onclick="closeCreateModal()"
                    class="px-5 py-2.5 bg-soft hover:bg-gray-200 txt-primary text-sm font-medium rounded-xl transition">Batal</button>
            <button type="submit" form="createForm"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Simpan Kategori
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: EDIT --}}
{{-- ============================================ --}}
<div id="editModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 520px;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Edit Kategori</h3>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'edit' && $errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="editForm" method="POST" novalidate x-data="editFormData()">
            @csrf @method('PUT')
            <input type="hidden" name="edit_id" id="editIdInput" value="{{ session('open_modal') === 'edit' ? session('edit_id') : '' }}">

            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kategori"
                           value="{{ session('open_modal') === 'edit' ? old('nama_kategori') : '' }}"
                           minlength="3" maxlength="100"
                           oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="text-xs txt-secondary mt-1">Hanya huruf & spasi. Minimal 3 karakter.</p>
                </div>

                {{-- TIPE --}}
                <div class="relative" @click.away="tipeOpen = false">
                    <label class="block text-sm font-medium txt-primary mb-1">Tipe <span class="text-red-500">*</span></label>
                    <button type="button" @click="tipeOpen = !tipeOpen"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                        <span class="txt-primary" id="editTipeLabel" x-text="tipeLabel">-- Pilih Tipe --</span>
                        <svg class="w-4 h-4 txt-muted" :class="tipeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="tipeOpen" x-cloak
                         class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                        <button type="button" @click="selectTipe('produk_tahu', 'Produk Tahu')"
                                class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Produk Tahu</button>
                        <button type="button" @click="selectTipe('limbah', 'Produk Limbah')"
                                class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Produk Limbah</button>
                    </div>
                    <input type="hidden" name="tipe" id="editTipeHidden" :value="tipeValue">
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 border-t bd-soft bg-soft flex-shrink-0">
            <button type="button" onclick="closeEditModal()"
                    class="px-5 py-2.5 bg-soft hover:bg-gray-200 txt-primary text-sm font-medium rounded-xl transition">Batal</button>
            <button type="submit" form="editForm"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                Update Kategori
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const KATEGORI_DATA = @json($kategoriJson);

    // ==== CREATE MODAL ====
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // ==== EDIT MODAL ====
    function openEditModal(id) {
        const data = KATEGORI_DATA.find(k => k.id === id);
        if (!data) return;

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        document.getElementById('editForm').action = '/admin/kategori/' + id;

        // Set nama kategori
        const namaInput = modal.querySelector('input[name="nama_kategori"]');
        if (!namaInput.value || namaInput.value.trim() === '') {
            namaInput.value = data.nama_kategori;
        }

        // Set edit_id
        const editIdInput = modal.querySelector('#editIdInput');
        if (editIdInput) editIdInput.value = id;

        const newTipeLabel = data.tipe === 'produk_tahu' ? 'Produk Tahu' : 'Produk Limbah';

        // === FALLBACK 1: Set DOM langsung (paling reliable) ===
        const tipeLabelSpan = modal.querySelector('#editTipeLabel');
        if (tipeLabelSpan) tipeLabelSpan.textContent = newTipeLabel;

        const tipeHiddenInput = modal.querySelector('#editTipeHidden');
        if (tipeHiddenInput) tipeHiddenInput.value = data.tipe;

        // === FALLBACK 2-4: Update Alpine state ===
        const form = document.getElementById('editForm');
        const updateState = (state) => {
            if (!state) return;
            state.currentId = id;
            state.tipeValue = data.tipe;
            state.tipeLabel = newTipeLabel;
        };

        if (form.__x && form.__x.$data) {
            updateState(form.__x.$data);
        } else if (form._x_dataStack && form._x_dataStack[0]) {
            updateState(form._x_dataStack[0]);
        } else if (window.Alpine && typeof window.Alpine.$data === 'function') {
            updateState(window.Alpine.$data(form));
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // ==== ALPINE FUNCTIONS ====
    function createFormData() {
        return {
            tipeOpen: false,
            tipeValue: '{{ old('tipe') }}',
            tipeLabel: '{{ old('tipe') === 'produk_tahu' ? 'Produk Tahu' : (old('tipe') === 'limbah' ? 'Produk Limbah' : '-- Pilih Tipe --') }}',

            selectTipe(value, label) {
                this.tipeValue = value;
                this.tipeLabel = label;
                this.tipeOpen = false;
            }
        };
    }

    function editFormData() {
        return {
            tipeOpen: false,
            tipeValue: '{{ session('open_modal') === 'edit' ? old('tipe') : '' }}',
            tipeLabel: '{{ session('open_modal') === 'edit' && old('tipe') ? (old('tipe') === 'produk_tahu' ? 'Produk Tahu' : 'Produk Limbah') : '-- Pilih Tipe --' }}',
            currentId: null,

            selectTipe(value, label) {
                this.tipeValue = value;
                this.tipeLabel = label;
                this.tipeOpen = false;
            }
        };
    }

    // ==== AUTO OPEN MODAL ON ERROR ====
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('open_modal') === 'create')
            openCreateModal();
        @elseif (session('open_modal') === 'edit' && session('edit_id'))
            openEditModal({{ session('edit_id') }});
        @endif
    });

    // ==== CLOSE ON OUTSIDE CLICK ====
    document.getElementById('createModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush