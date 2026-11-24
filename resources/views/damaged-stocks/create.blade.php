@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Input Barang Rusak / Kebocoran Kemasan</h4>
                <p class="text-muted small mb-0">Catat jumlah dus mie instan yang rusak/bocor dan ambil foto bukti langsung
                    melalui kamera.</p>
            </div>
            <a href="{{ route('damaged-stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('damaged-stocks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                {{-- Kolom Kiri: Data Kerusakan --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-semibold">
                            <i class="bi bi-box-seam text-danger me-1"></i> Detail Produk & Jumlah Dus Rusak
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lokasi Rak & Produk Terisi <span
                                        class="text-danger">*</span></label>
                                <select name="storage_placement_id" id="placementSelect" class="form-select" required>
                                    <option value="">-- Pilih Rak & SKU Produk --</option>
                                    @foreach ($placements as $item)
                                        @php
                                            $gross = $item->pallet_quantity * $item->product->cartons_per_pallet;
                                            $net = max(0, $gross - ($item->damaged_cartons ?? 0));
                                        @endphp
                                        <option value="{{ $item->id }}" data-net="{{ $net }}"
                                            {{ old('storage_placement_id') == $item->id ? 'selected' : '' }}>
                                            [{{ $item->rackLevel->rack->area->warehouse->warehouse_code }}]
                                            {{ $item->rackLevel->rack->rack_code }} - Lv
                                            {{ $item->rackLevel->level_number }}
                                            | {{ $item->product->product_code }} - {{ $item->product->product_name }}
                                            (Sisa: {{ $net }} dus)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-primary fw-semibold" id="stockInfo"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Jumlah Dus Rusak <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="damaged_cartons" id="damagedInput" class="form-control"
                                        min="1" value="{{ old('damaged_cartons', 1) }}" required>
                                    <div class="form-text">Satuan dalam dus / karton mie instan.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Kategori Kerusakan <span
                                            class="text-danger">*</span></label>
                                    <select name="damage_category" class="form-select" required>
                                        <option value="Kemasan Bocor / Sobek">Kemasan Bocor / Sobek</option>
                                        <option value="Dus Penyok / Hancur">Dus Penyok / Hancur</option>
                                        <option value="Dus Basah / Lembab">Dus Basah / Lembab</option>
                                        <option value="Kerusakan Handling Forklift">Kerusakan Handling Forklift</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keterangan Tambahan</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Contoh: 3 dus bagian bawah pallet penyok dan bungkus mie bocor saat pengecekan shift pagi">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Kamera Langsung & Preview Bukti --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-camera-fill text-primary me-1"></i> Foto Bukti Kerusakan (Kamera)</span>
                            <span class="badge bg-danger">Wajib</span>
                        </div>
                        <div class="card-body text-center">
                            {{-- Area Live Kamera --}}
                            <div class="border rounded bg-dark mb-3 position-relative overflow-hidden"
                                style="min-height: 240px;">
                                <video id="cameraStream" autoplay playsinline class="w-100 d-none"
                                    style="max-height: 280px; object-fit: cover;"></video>
                                <canvas id="photoCanvas" class="d-none"></canvas>
                                <img id="photoPreview" src="" alt="Preview Foto" class="w-100 d-none"
                                    style="max-height: 280px; object-fit: contain;">
                                <div id="cameraPlaceholder" class="text-white-50 py-5 px-3">
                                    <i class="bi bi-camera fs-1 d-block mb-2"></i>
                                    Klik <strong>"Buka Kamera"</strong> untuk memotret langsung dus mie instan yang rusak,
                                    atau pilih file foto di bawah.
                                </div>
                            </div>

                            <input type="hidden" name="camera_image_base64" id="cameraImageBase64">

                            <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
                                <button type="button" id="btnStartCamera" class="btn btn-primary btn-sm">
                                    <i class="bi bi-camera-video"></i> Buka Kamera
                                </button>
                                <button type="button" id="btnCapturePhoto" class="btn btn-success btn-sm d-none">
                                    <i class="bi bi-camera"></i> Jepret Foto
                                </button>
                                <button type="button" id="btnRetakePhoto" class="btn btn-warning btn-sm d-none">
                                    <i class="bi bi-arrow-repeat"></i> Foto Ulang
                                </button>
                            </div>

                            <hr>

                            <div class="text-start">
                                <label class="form-label small fw-semibold text-muted">Atau Unggah / Ambil Foto via Kamera
                                    HP:</label>
                                <input type="file" name="photo_file" id="photoFileInput" accept="image/*"
                                    capture="environment" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-danger px-4">
                    <i class="bi bi-check-circle me-1"></i> Simpan Laporan & Potong Stok
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const placementSelect = document.getElementById('placementSelect');
            const stockInfo = document.getElementById('stockInfo');
            const damagedInput = document.getElementById('damagedInput');

            placementSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const net = selected.getAttribute('data-net');
                if (net) {
                    stockInfo.textContent = `Maksimal dus yang dapat dilaporkan pada rak ini: ${net} dus.`;
                    damagedInput.max = net;
                } else {
                    stockInfo.textContent = '';
                }
            });

            // Logika WebRTC Camera
            const video = document.getElementById('cameraStream');
            const canvas = document.getElementById('photoCanvas');
            const preview = document.getElementById('photoPreview');
            const placeholder = document.getElementById('cameraPlaceholder');
            const hiddenInput = document.getElementById('cameraImageBase64');
            const fileInput = document.getElementById('photoFileInput');

            const btnStart = document.getElementById('btnStartCamera');
            const btnCapture = document.getElementById('btnCapturePhoto');
            const btnRetake = document.getElementById('btnRetakePhoto');

            let stream = null;

            btnStart.addEventListener('click', async () => {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment'
                        },
                        audio: false
                    });
                    video.srcObject = stream;
                    video.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                    preview.classList.add('d-none');
                    btnStart.classList.add('d-none');
                    btnCapture.classList.remove('d-none');
                    btnRetake.classList.add('d-none');
                } catch (err) {
                    alert(
                        'Kamera tidak dapat diakses di browser ini. Silakan gunakan tombol "Pilih File / Kamera HP" di bawah.');
                }
            });

            btnCapture.addEventListener('click', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);

                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                hiddenInput.value = dataUrl;
                preview.src = dataUrl;

                // Matikan kamera setelah jepret
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                video.classList.add('d-none');
                preview.classList.remove('d-none');
                btnCapture.classList.add('d-none');
                btnRetake.classList.remove('d-none');
                fileInput.value = '';
            });

            btnRetake.addEventListener('click', () => {
                hiddenInput.value = '';
                btnStart.click();
            });

            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                        placeholder.classList.add('d-none');
                        video.classList.add('d-none');
                        hiddenInput.value = '';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
