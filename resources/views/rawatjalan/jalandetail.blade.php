@extends('layouts.app')

@section('title', 'Bil jalan')

@section('content_header')
    <div class="d-flex align-items-center">

        <a href="{{ route('rawatjalan.index') }}" class="btn btn-secondary btn-sm mr-3">

            <i class="fas fa-arrow-left fa-2x"></i>

        </a>

        <h1 class="mb-0">
            Billing Rawat Jalan
        </h1>

    </div>
@stop

@section('page-content')

    @if (!$pasien)

        <div class="alert alert-warning">
            Data pasien tidak ditemukan.
        </div>
    @else
        <div class="card shadow-sm">

            <div class="card-header bg-info">
                <h3 class="card-title mb-0">
                    <i class="fas fa-user-injured mr-2"></i>
                    {{ $pasien->Nama }} --- {{ $pasien->Addr }}
                </h3>
            </div>

            <div class="card-body py-2">

                <div class="row align-items-center text-sm">

                    <div class="col">
                        <small class="text-muted d-block" style="font-size:13px">
                            ID
                        </small>

                        <div class="font-weight-bold" style="font-size:16px">
                            {{ $pasien->ID }}
                        </div>
                    </div>


                    <div class="col">
                        <small class="text-muted d-block" style="font-size:13px">
                            No SEP
                        </small>

                        <div class="font-weight-bold text-info" style="font-size:16px; cursor:pointer;"
                            onclick="showSepDetail('{{ $pasien->NoSEP }}')">
                            {{ $pasien->NoSEP ?? '-' }}
                        </div>
                    </div>

                    {{-- Modal SEP --}}
                    <div class="modal fade" id="modalSep" tabindex="-1">

                        <div class="modal-dialog modal-xl modal-dialog-scrollable">

                            <div class="modal-content border-0 shadow-lg">

                                <div class="modal-header bg-info text-white">

                                    <h5 class="modal-title font-weight-bold">
                                        <i class="fas fa-id-card mr-2"></i>
                                        Detail SEP BPJS
                                    </h5>

                                    <button type="button" class="close text-white" data-dismiss="modal">

                                        <span>&times;</span>

                                    </button>

                                </div>

                                <div class="modal-body p-0" id="sepContent">

                                    <div class="text-center text-muted py-5">

                                        <i class="fas fa-spinner fa-spin"></i>

                                        Memuat data SEP...

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- JS SEP --}}
                    <script>
                        function showSepDetail(noSep) {
                            if (!noSep || noSep === '-') return;

                            $('#modalSep').modal('show');

                            $('#sepContent').html(`
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat data SEP...
                                </div>
                            `);

                            $.getJSON("{{ route('sep.detail') }}?nosep=" + encodeURIComponent(noSep), function(res) {

                                if (!res || res.metaData?.code != 200) {
                                    $('#sepContent').html(`
                                        <div class="alert alert-danger mb-0">
                                            Data SEP tidak ditemukan.
                                        </div>
                                    `);
                                    return;
                                }

                                let d = res.response || {};
                                let p = d.peserta || {};
                                let dpjp = d.dpjp || {};
                                let tujuan = d.tujuanKunj || {};

                                $('#sepContent').html(`
                                    <div class="sep-print-preview">
                        
                                        <div class="sep-header">
                                            <div class="sep-logo">
                                                <img src="{{ asset('img/BPJS_Kesehatan_logo.svg') }}" alt="BPJS">
                                            </div>
                        
                                            <div class="sep-title">
                                                <div>SURAT ELEGIBILITAS PESERTA</div>
                                                <small>RS AISYIYAH BOJONEGORO</small>
                                            </div>
                                        </div>
                        
                                        <div class="row mt-3">
                        
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless sep-table">
                                                    <tr><td>No. SEP</td><td>:</td><td><strong>${d.noSep ?? '-'}</strong></td></tr>
                                                    <tr><td>Tgl SEP</td><td>:</td><td>${d.tglSep ?? '-'}</td></tr>
                                                    <tr><td>No Kartu</td><td>:</td><td>${p.noKartu ?? '-'} (${p.noMr ?? '-'})</td></tr>
                                                    <tr><td>Nama Peserta</td><td>:</td><td>${p.nama ?? '-'}</td></tr>
                                                    <tr><td>Tgl. Lahir</td><td>:</td><td>${p.tglLahir ?? '-'} &nbsp;&nbsp; Kelamin : ${p.kelamin ?? '-'}</td></tr>
                                                    <tr><td>No. Telepon</td><td>:</td><td>-</td></tr>
                                                    <tr><td>Sub/Spesialis</td><td>:</td><td>${d.poli ?? '-'}</td></tr>
                                                    <tr><td>Dokter</td><td>:</td><td>${dpjp.nmDPJP ?? '-'}</td></tr>
                                                    <tr><td>Faskes Perujuk</td><td>:</td><td>RS AISYIYAH BOJONEGORO</td></tr>
                                                    <tr><td>Diagnosa Awal</td><td>:</td><td>${d.diagnosa ?? '-'}</td></tr>
                                                </table>
                                            </div>
                        
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless sep-table">
                                                    <tr><td>Peserta</td><td>:</td><td>${p.jnsPeserta ?? '-'}</td></tr>
                                                    <tr><td>Jns. Rawat</td><td>:</td><td>${d.jnsPelayanan ?? '-'}</td></tr>
                                                    <tr><td>Jns Kunjungan</td><td>:</td><td>${tujuan.nama ?? '-'}</td></tr>
                                                    <tr><td>Poli Perujuk</td><td>:</td><td>${d.poli ?? '-'}</td></tr>
                                                    <tr><td>Kls. Hak</td><td>:</td><td>${p.hakKelas ?? '-'}</td></tr>
                                                    <tr><td>Kls. Rawat</td><td>:</td><td>${d.kelasRawat ?? '-'}</td></tr>
                                                    <tr><td>Penjamin</td><td>:</td><td>${d.penjamin ?? '-'}</td></tr>
                                                    <tr><td>Catatan</td><td>:</td><td>${d.catatan ?? '-'}</td></tr>
                                                </table>
                                            </div>
                        
                                        </div>
                        
                                        <hr>
                        
                                        <div class="sep-note">
                                            <em>*Saya menyetujui BPJS Kesehatan untuk:</em><br>
                                            a. membuka dan atau menggunakan informasi medis pasien untuk keperluan administrasi, pembayaran asuransi atau jaminan pembiayaan kesehatan.<br>
                                            b. memberikan akses informasi medis atau riwayat kepada dokter/tenaga medis pada RS AISYIYAH BOJONEGORO untuk kepentingan pemeliharaan kesehatan, pengobatan, penyembuhan, dan perawatan pasien.
                                        </div>
                        
                                    </div>
                                `);

                            }).fail(function() {
                                $('#sepContent').html(`
                                    <div class="alert alert-danger mb-0">
                                        Gagal mengambil data SEP dari server BPJS.
                                    </div>
                                `);
                            });
                        }
                    </script>

                    <div class="col">
                        <small class="text-muted d-block" style="font-size:13px">
                            No RM
                        </small>

                        <div class="font-weight-bold" style="font-size:16px">
                            {{ $pasien->RegNum }}
                        </div>
                    </div>

                    <div class="col">
                        <small class="text-muted d-block" style="font-size:13px">
                            PxRS
                        </small>

                        <div class="font-weight-bold" id="textPxRS" style="font-size:16px; color:#198754; cursor:pointer;"
                            data-toggle="modal" data-target="#modalUpdatePxRS">
                            {{ $pasien->PxRS }}
                        </div>
                    </div>

                    {{-- Modal Update PXRS --}}
                    <div class="modal fade" id="modalUpdatePxRS" tabindex="-1">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content border-0 shadow-lg">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title font-weight-bold">
                                        <i class="fas fa-user-edit mr-2"></i>
                                        Ubah PxRS
                                    </h5>

                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <form id="formUpdatePxRS" action="{{ route('rawatinap.updatePxRS', $pasien->ID) }}"
                                    method="POST">
                                    @csrf

                                    <div class="modal-body">

                                        <label>Pilih PxRS</label>

                                        <select name="uPx" id="uPx" class="form-control" required>
                                            <option value="">-- Pilih PxRS --</option>

                                            @foreach ($upxList as $u)
                                                <option value="{{ $u->ID }}"
                                                    {{ ($pasien->uPx ?? '') == $u->ID ? 'selected' : '' }}>
                                                    {{ $u->PxRS }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Batal
                                        </button>

                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <small class="text-muted d-block" style="font-size:13px">
                            Tanggal Masuk
                        </small>

                        <div style="font-size:16px">
                            {{ $pasien->Tanggal ? date('d/m/Y', strtotime($pasien->Tanggal)) : '-' }}
                        </div>
                    </div>

                    <div class="col">
                        <small class="text-muted d-block" style="font-size:13px">
                            Jam Masuk
                        </small>

                        <div style="font-size:16px">
                            {{ $pasien->Jam_masuk ? date('H:i', strtotime($pasien->Jam_masuk)) : '-' }}
                        </div>
                    </div>

                    <div class="col">

                        <small class="text-muted d-block" style="font-size:13px">
                            Tanggal Bayar
                        </small>

                        <div class="input-group input-group-sm">

                            <input type="date" class="form-control" id="TglByr"
                                value="{{ !empty($pasien->TglByr) ? date('Y-m-d', strtotime($pasien->TglByr)) : '' }}">

                            <div class="input-group-append">

                                {{-- Simpan --}}
                                <button type="button" class="btn btn-success" onclick="updateTglBayar()" title="Simpan">

                                    <i class="fas fa-save"></i>

                                </button>

                                {{-- Hapus --}}
                                <button type="button" class="btn btn-danger" onclick="hapusTglBayar()" title="Hapus">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                    {{-- Js TGL bayar --}}
                    <script>
                        function updateTglBayar() {

                            $.ajax({
                                url: "{{ route('rawatinap.updateTglBayar', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    TglByr: $('#TglByr').val()
                                },

                                success: function(response) {

                                    location.reload();

                                },

                                error: function(xhr) {

                                    alert('Gagal menyimpan tanggal bayar');

                                }
                            });

                        }
                    </script>

                    <script>
                        function hapusTglBayar() {

                            if (!confirm('Hapus tanggal bayar ?')) {
                                return;
                            }

                            $.ajax({
                                url: "{{ route('rawatinap.hapusTglBayar', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function() {

                                    $('#TglByr').val('');

                                    toastr.success(
                                        'Tanggal bayar berhasil dihapus'
                                    );

                                },
                                error: function() {

                                    toastr.error(
                                        'Gagal menghapus tanggal bayar'
                                    );

                                }
                            });
                        }
                    </script>

                </div>

                {{--  Baris ke-2 --}}
                <div class="mt-3">

                    {{-- Cek Kartu BPJS --}}
                    <button type="button" class="btn btn-success btn-sm"
                        onclick="showBpjsPeserta('{{ $pasien->NoJKN ?? '' }}')">
                        <i class="fas fa-id-card"></i> Cek BPJS
                    </button>

                    {{-- Modal Cek Kartu BPJS --}}
                    <div class="modal fade" id="modalBpjsPeserta" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title font-weight-bold">
                                        <i class="fas fa-id-card mr-2"></i>
                                        Detail Peserta BPJS
                                    </h5>

                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body" id="bpjsPesertaContent">
                                    <div class="text-center text-muted py-4">
                                        Memuat data peserta...
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- JS Cek Kartu BPJS --}}
                    <script>
                        function showBpjsPeserta(noKartu) {
                            if (!noKartu || noKartu === '-') {
                                alert('Nomor kartu BPJS tidak tersedia.');
                                return;
                            }

                            $('#modalBpjsPeserta').modal('show');

                            $('#bpjsPesertaContent').html(`
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat data peserta BPJS...
                                </div>
                            `);

                            $.getJSON("{{ route('bpjs.peserta') }}?noKartu=" + encodeURIComponent(noKartu), function(res) {

                                if (!res || res.metaData?.code != 200) {
                                    $('#bpjsPesertaContent').html(`
                                        <div class="alert alert-danger mb-0">
                                            Data peserta tidak ditemukan.
                                        </div>
                                    `);
                                    return;
                                }

                                let p = res.response?.peserta || {};

                                $('#bpjsPesertaContent').html(`
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body">
                                            <h5 class="font-weight-bold text-success mb-1">
                                                ${p.nama ?? '-'}
                                            </h5>
                                            <small class="text-muted">
                                                No Kartu: ${p.noKartu ?? '-'} | NIK: ${p.nik ?? '-'}
                                            </small>
                                        </div>
                                    </div>
                        
                                    <table class="table table-sm table-bordered">
                                        <tr><th width="220">Status Peserta</th><td>
                                            <span class="badge badge-success px-3">
                                                ${p.statusPeserta?.keterangan ?? '-'}
                                            </span>
                                        </td></tr>
                        
                                        <tr><th>No MR</th><td>${p.mr?.noMR ?? '-'}</td></tr>
                                        <tr><th>No Telepon</th><td>${p.mr?.noTelepon ?? '-'}</td></tr>
                                        <tr><th>Jenis Kelamin</th><td>${p.sex ?? '-'}</td></tr>
                                        <tr><th>Tanggal Lahir</th><td>${p.tglLahir ?? '-'}</td></tr>
                                        <tr><th>Umur</th><td>${p.umur?.umurSekarang ?? '-'}</td></tr>
                                        <tr><th>Jenis Peserta</th><td>${p.jenisPeserta?.keterangan ?? '-'}</td></tr>
                                        <tr><th>Hak Kelas</th><td>${p.hakKelas?.keterangan ?? '-'}</td></tr>
                                        <tr><th>Faskes Provider</th><td>${p.provUmum?.nmProvider ?? '-'}</td></tr>
                                        <tr><th>Tanggal TMT</th><td>${p.tglTMT ?? '-'}</td></tr>
                                        <tr><th>Tanggal TAT</th><td>${p.tglTAT ?? '-'}</td></tr>
                                        <tr><th>COB</th><td>${p.cob?.nmAsuransi ?? '-'}</td></tr>
                                        <tr><th>Prolanis/PRB</th><td>${p.informasi?.prolanisPRB ?? '-'}</td></tr>
                                    </table>
                                `);

                            }).fail(function() {
                                $('#bpjsPesertaContent').html(`
                                    <div class="alert alert-danger mb-0">
                                        Gagal mengambil data peserta BPJS.
                                    </div>
                                `);
                            });
                        }
                    </script>

                    {{-- Print Rekening --}}
                    <button type="button" class="btn btn-info btn-sm" onclick="previewBilling()">
                        <i class="fas fa-print"></i> Rekening
                    </button>

                    <script>
                        function previewBilling() {
                            window.open(
                                "{{ route('rawatjalan.rekeningPrint', $pasien->ID) }}",
                                "_blank",
                                "height=800,width=1000"
                            );
                        }
                    </script>

                    {{-- Print Kwitansi --}}
                    <button type="button" class="btn btn-info btn-sm" onclick="previewKwitansi()">
                        <i class="fas fa-print"></i> Kwitansi
                    </button>

                    <script>
                        function previewKwitansi() {
                            window.open(
                                "{{ route('rawatinap.kwitansiPrint', $pasien->ID) }}",
                                "_blank",
                                "height=800,width=1000"
                            );
                        }
                    </script>

                    {{-- print Obat All --}}
                    <div class="dropdown d-inline">

                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fas fa-print"></i>
                            Obat All
                        </button>

                        <div class="dropdown-menu">

                            @foreach ($roomObatList as $room)
                                <a href="javascript:void(0)" class="dropdown-item"
                                    onclick="printObatRinci('{{ $pasien->ID }}', '{{ $room->RoomID }}')">

                                    {{ $room->RoomName }}

                                </a>
                            @endforeach

                        </div>

                    </div>

                    <script>
                        function printObatRinci(id, roomId) {

                            let url = "{{ url('/rawatinap') }}/" + id + "/obat-rinci/" + roomId;

                            window.open(
                                url,
                                "_blank",
                                "height=800,width=1000"
                            );
                        }
                    </script>

                    {{-- Print ObaPay --}}
                    <button type="button" class="btn btn-info btn-sm" onclick="printObaPay('{{ $pasien->ID }}')">

                        <i class="fas fa-print"></i>
                        ObaPay All
                    </button>

                    <script>
                        function printObaPay(id) {

                            let url = "{{ url('/rawatinap') }}/" + id + "/obapay-print";

                            window.open(
                                url,
                                "_blank",
                                "height=800,width=1000"
                            );
                        }
                    </script>

                </div>

            </div>

        </div>

        <div class="card shadow-sm">

            <div class="card-header p-0 pt-1">

                <ul class="nav nav-tabs" id="billingTabs" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#tab-kamar">
                            <i class="fas fa-bed"></i> Kamar
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-visit">
                            <i class="fas fa-user-md"></i> Visit Dokter
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-utilitas">
                            <i class="fas fa-notes-medical"></i> Utilitas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-radiologi">
                            <i class="fas fa-x-ray"></i> Radiologi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-lab">
                            <i class="fas fa-vials"></i> Lab
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-lain">
                            <i class="fas fa-list-alt"></i> Lain-lain
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-operasi">
                            <i class="fas fa-procedures"></i> Operasi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-farmasi">
                            <i class="fas fa-pills"></i> Farmasi
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#tab-kasir">
                            <i class="fas fa-cash-register"></i>
                            Kasir
                        </a>
                    </li>

                </ul>

            </div>

            <div class="card-body p-0">

                <div class="tab-content">

                    {{-- Tab Kamar --}}
                    <div class="tab-pane fade show active" id="tab-kamar">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>Kls/Ruang</th>
                                        <th>Biaya/hr</th>
                                        <th>Dokter</th>
                                        <th>T. Masuk</th>
                                        <th>T. Keluar</th>
                                        <th>J. Klr</th>
                                        <th>Pot Hr</th>
                                        <th>Status</th>
                                        <th>Kamar</th>
                                        <th>Pot(%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($kamar as $k)
                                        <tr style="cursor:pointer"
                                            onclick="openEditPasInap(
                                        '{{ $k->Nomer }}',
                                        '{{ $k->KelasID }}',
                                        '{{ $k->RoomID }}',
                                        '{{ $k->DokterID }}',
                                        '{{ $k->TMasuk ? date('Y-m-d', strtotime($k->TMasuk)) : '' }}',
                                        '{{ $k->JMasuk ? date('H:i', strtotime($k->JMasuk)) : '' }}',
                                        '{{ $k->TKeluar ? date('Y-m-d', strtotime($k->TKeluar)) : '' }}',
                                        '{{ $k->JKeluar ? date('H:i', strtotime($k->JKeluar)) : '' }}',
                                        '{{ $k->Status }}',
                                        '{{ $k->Pot }}',
                                        '{{ $k->PotDay }}',
                                        '{{ $k->Askep }}',
                                        '{{ $k->Pot2 }}',
                                        '{{ $k->KodeBed }}',
                                        '{{ $k->Biaya }}',
                                        '{{ $k->kamar }}',
                                        '{{ $k->japel }}',
                                        '{{ $k->alat }}'
                                    )">

                                            <td>
                                                {{ $k->NamaKamar ?? '-' }}

                                            </td>

                                            <td>Rp {{ number_format($k->Biaya ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $k->NamaDokter ?? '-' }}</td>
                                            <td>{{ $k->TMasuk ? date('d/m/Y', strtotime($k->TMasuk)) : '-' }}</td>
                                            <td>{{ $k->TKeluar ? date('d/m/Y', strtotime($k->TKeluar)) : '-' }}</td>
                                            <td>{{ $k->JKeluar ? date('H:i', strtotime($k->JKeluar)) : '-' }}</td>
                                            <td>{{ $k->PotDay ?? 0 }}</td>
                                            <td>{{ $k->Status }}</td>
                                            <td>Rp {{ number_format($k->TotalSewa ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ number_format(($k->Pot ?? 0) * 100, 2) }}%</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                Data kamar belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr class="font-weight-bold bg-light">

                                        <td colspan="6">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="openInsertPasInap()">

                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Tambah Kamar

                                            </button>
                                        </td>

                                        <td colspan="2" class="text-right align-middle">
                                            Total Kamar
                                        </td>

                                        <td class="align-middle">
                                            Rp {{ number_format(collect($kamar)->sum('TotalSewa'), 0, ',', '.') }}
                                        </td>

                                        <td></td>

                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                        <script>
                            function openInsertPasInap() {

                                $('#editNomer').val('');

                                $('#editKamar').val('').trigger('change');
                                $('#editDokterID').val('').trigger('change');
                                $('#editStatus').val('Dirawat').trigger('change');

                                $('#editTMasuk').val('{{ date('Y-m-d') }}');
                                $('#editJMasuk').val('{{ date('H:i') }}');

                                $('#editTKeluar').val('');
                                $('#editJKeluar').val('');

                                $('#editPotDay').val(0);

                                $('#editBiaya').val('Rp 0');
                                $('#editKamarTarif').val('Rp 0');
                                $('#editJapel').val('Rp 0');
                                $('#editAlat').val('Rp 0');

                                $('#modalEditPasInapLabel').html(
                                    '<i class="fas fa-plus-circle mr-2"></i> Tambah Kamar Rawat Inap'
                                );

                                $('#btnSimpanPasInap')
                                    .attr('onclick', 'simpanInsertPasInap()');

                                $('#modalEditPasInap').modal('show');
                                $('#btnHapusPasInap').hide();
                            }

                            function simpanInsertPasInap() {
                                let kamarValue = $('#editKamar').val();

                                if (!kamarValue) {
                                    alert('Pilih kamar terlebih dahulu.');
                                    return;
                                }

                                let parts = kamarValue.split('|');

                                $.ajax({
                                    url: "{{ route('rawatinap.insertPasInap') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",

                                        ID: "{{ $pasien->ID }}",
                                        KelasID: parts[0],
                                        RoomID: parts[1],

                                        DokterID: $('#editDokterID').val(),
                                        TMasuk: $('#editTMasuk').val(),
                                        JMasuk: $('#editJMasuk').val(),
                                        TKeluar: $('#editTKeluar').val(),
                                        JKeluar: $('#editJKeluar').val(),
                                        Status: $('#editStatus').val(),

                                        Pot: $('#editPot').val() || 0,
                                        PotDay: $('#editPotDay').val() || 0,
                                        Askep: $('#editAskep').val() || 0,
                                        Pot2: $('#editPot2').val() || 0,

                                        Biaya: $('#editBiaya').val(),
                                        Kamar: $('#editKamarTarif').val(),
                                        Japel: $('#editJapel').val(),
                                        Alat: $('#editAlat').val(),

                                        KodeBed: $('#editKodeBed').val()
                                    },
                                    success: function(res) {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert('Gagal menambahkan data kamar rawat inap.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function openEditPasInap(
                                nomer,
                                kelasId,
                                roomId,
                                dokterId,
                                tMasuk,
                                jMasuk,
                                tKeluar,
                                jKeluar,
                                status,
                                pot,
                                potDay,
                                askep,
                                pot2,
                                kodeBed,
                                biaya,
                                kamarTarif,
                                japel,
                                alat
                            ) {

                                $('#editNomer').val(nomer);

                                $('#editKamar').val(kelasId + '|' + roomId).trigger('change');
                                $('#editDokterID').val(dokterId).trigger('change');
                                $('#editStatus').val(status).trigger('change');

                                $('#editTMasuk').val(tMasuk);
                                $('#editJMasuk').val(jMasuk);
                                $('#editTKeluar').val(tKeluar);
                                $('#editJKeluar').val(jKeluar);

                                $('#editPot').val(pot);
                                $('#editPotDay').val(potDay);
                                $('#editAskep').val(askep);
                                $('#editPot2').val(pot2);
                                $('#editKodeBed').val(kodeBed);

                                $('#editBiaya').val(
                                    formatRupiah(biaya)
                                );

                                $('#editKamarTarif').val(
                                    formatRupiah(kamarTarif)
                                );

                                $('#editJapel').val(
                                    formatRupiah(japel)
                                );

                                $('#editAlat').val(
                                    formatRupiah(alat)
                                );

                                $('#modalEditPasInap').modal('show');
                                $('#btnHapusPasInap').show();
                                $('#btnHapusPasInap')
                                    .off('click')
                                    .on('click', hapusPasInap);
                            }

                            function simpanEditPasInap() {
                                let kamarValue = $('#editKamar').val();

                                if (!kamarValue) {
                                    alert('Pilih kamar terlebih dahulu.');
                                    return;
                                }

                                let parts = kamarValue.split('|');

                                $.ajax({
                                    url: "{{ route('rawatinap.updatePasInap') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        Nomer: $('#editNomer').val(),
                                        KelasID: parts[0],
                                        RoomID: parts[1],
                                        DokterID: $('#editDokterID').val(),
                                        TMasuk: $('#editTMasuk').val(),
                                        JMasuk: $('#editJMasuk').val(),
                                        TKeluar: $('#editTKeluar').val(),
                                        JKeluar: $('#editJKeluar').val(),
                                        Status: $('#editStatus').val(),
                                        Pot: $('#editPot').val(),
                                        PotDay: $('#editPotDay').val(),
                                        Askep: $('#editAskep').val(),
                                        Pot2: $('#editPot2').val(),
                                        KodeBed: $('#editKodeBed').val()
                                    },
                                    success: function() {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert('Gagal mengubah data PasInap.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function formatRupiah(angka) {

                                angka = parseFloat(angka || 0);

                                return 'Rp ' + angka.toLocaleString('id-ID');

                            }
                            $(function() {

                                $(document).ready(function() {

                                    $('#modalEditPasInap').on('shown.bs.modal', function() {

                                        if ($('#editKamar').hasClass("select2-hidden-accessible")) {
                                            $('#editKamar').select2('destroy');
                                        }

                                        $('#editKamar').select2({
                                            dropdownParent: $('#modalEditPasInap'),
                                            width: '100%',
                                            placeholder: 'Cari nama kamar...',
                                            allowClear: true,
                                            minimumResultsForSearch: 0
                                        });

                                    });

                                });

                            });

                            function hapusPasInap() {

                                let nomer = $('#editNomer').val();

                                if (!nomer) {
                                    alert('Data belum tersimpan.');
                                    return;
                                }

                                if (!confirm('Yakin ingin menghapus kamar ini ?')) {
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.deletePasInap') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        Nomer: nomer
                                    },
                                    success: function(res) {

                                        $('#modalEditPasInap').modal('hide');

                                        location.reload();

                                    },
                                    error: function(xhr) {

                                        alert(
                                            xhr.responseJSON?.message ??
                                            'Gagal menghapus kamar.'
                                        );

                                    }
                                });
                            }
                        </script>

                        {{-- Modal Edit PasInap --}}
                        <div class="modal fade" id="modalEditPasInap" tabindex="-1" role="dialog"
                            aria-labelledby="modalEditPasInapLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold" id="modalEditPasInapLabel">
                                            <i class="fas fa-procedures mr-2"></i>
                                            Perubahan Data Kamar Rawat Inap
                                        </h5>

                                        <button type="button" class="close text-white" data-dismiss="modal"
                                            aria-label="Tutup">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" id="editNomer">

                                        <div class="card card-outline card-info mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="fas fa-hospital-user mr-1"></i>
                                                    Informasi Perawatan
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editKamar">
                                                                <i class="fas fa-bed text-info mr-1"></i>
                                                                Kelas Perawatan & Ruangan
                                                            </label>

                                                            <select id="editKamar" class="form-control select2-kamar"
                                                                style="width:100%;">
                                                                <option value="">-- Pilih Kamar / Ruangan --</option>

                                                                @foreach ($kamarList as $km)
                                                                    <option
                                                                        value="{{ $km->ID }}|{{ $km->RoomID }}">
                                                                        {{ $km->Nama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editDokterID">Dokter</label>
                                                            <select id="editDokterID" class="form-control select2">
                                                                <option value="">-- Pilih DPJP --</option>
                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->ID }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editStatus">Status Perawatan Pasien</label>
                                                            <select id="editStatus" class="form-control select2">
                                                                <option value="">-- Pilih Status Perawatan --
                                                                </option>
                                                                @foreach ($pxStateList as $st)
                                                                    <option value="{{ $st->pxState }}">
                                                                        {{ $st->pxState }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-outline card-secondary mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="far fa-clock mr-1"></i>
                                                    Periode Rawat Inap
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">
                                                <div class="row">

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="editTMasuk">Tanggal Masuk</label>
                                                            <input type="date" id="editTMasuk" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="editJMasuk">Jam Masuk</label>
                                                            <input type="time" id="editJMasuk" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="editTKeluar">Tanggal Keluar</label>
                                                            <input type="date" id="editTKeluar" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="editJKeluar">Jam Keluar</label>
                                                            <input type="time" id="editJKeluar" class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-outline card-success mb-3">

                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="fas fa-file-invoice-dollar mr-1"></i>
                                                    Informasi Tarif & Potongan
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">

                                                <div class="row">

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Biaya Kamar</label>
                                                            <input type="text" id="editBiaya"
                                                                class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Kamar</label>
                                                            <input type="text" id="editKamarTarif"
                                                                class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Japel</label>
                                                            <input type="text" id="editJapel"
                                                                class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Alat</label>
                                                            <input type="text" id="editAlat"
                                                                class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Potongan Hari Rawat</label>
                                                            <input type="number" id="editPotDay" class="form-control"
                                                                placeholder="0">
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="alert alert-light border mb-0">
                                            <i class="fas fa-info-circle text-info mr-1"></i>
                                            Perubahan kelas perawatan akan otomatis memperbarui tarif kamar, jasa pelayanan,
                                            alat, dan komponen biaya terkait sesuai master tarif yang berlaku.
                                        </div>

                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-dismiss="modal">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tutup
                                        </button>

                                        <button type="button" id="btnSimpanPasInap" class="btn btn-success btn-sm"
                                            onclick="simpanEditPasInap()">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>

                                        <button type="button" id="btnHapusPasInap" class="btn btn-danger btn-sm">

                                            <i class="fas fa-trash"></i>
                                            Hapus

                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Tab Visite --}}
                    <div class="tab-pane fade" id="tab-visit">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>Dokter</th>
                                        <th>Tgl Visit</th>
                                        <th>Tarif</th>
                                        <th>Pot (%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($visitdokter as $v)
                                        <tr style="cursor:pointer"
                                            onclick="openEditVisit(
                                                '{{ $v->Visit_ID }}',
                                                '{{ $v->DokterID }}',
                                                '{{ $v->TglVisit ? date('Y-m-d', strtotime($v->TglVisit)) : '' }}',
                                                '{{ $v->BiayaVisit ?? 0 }}',
                                                '{{ ($v->Pot ?? 0) * 100 }}'
                                            )">

                                            <td>{{ $v->NamaDokter ?? '-' }}</td>
                                            <td>{{ $v->TglVisit ? date('d/m/Y', strtotime($v->TglVisit)) : '-' }}</td>
                                            <td>Rp {{ number_format($v->BiayaVisit ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ number_format(($v->Pot ?? 0) * 100, 2) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                Data visit dokter belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="openInsertVisit()">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Tambah Visit
                                            </button>
                                        </td>

                                        <td class="text-right">
                                            Total Visit
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($visitdokter)->sum('TotalVisit'), 0, ',', '.') }}
                                        </td>

                                        <td></td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                        <script>
                            function openEditVisit(
                                visitId,
                                dokterId,
                                tanggal,
                                biaya,
                                pot
                            ) {

                                $('#editVisitID').val(visitId);

                                $('#editVisitDokterID')
                                    .val(dokterId)
                                    .trigger('change');

                                $('#editVisitTanggal').val(tanggal);

                                $('#editVisitBiaya').val(
                                    formatRupiahVisit(biaya)
                                );

                                $('#editVisitPot').val(pot);

                                $('#btnHapusVisit')
                                    .show()
                                    .off('click')
                                    .on('click', hapusVisit);

                                $('#modalEditVisit').modal('show');
                            }

                            function simpanEditVisit() {
                                $.ajax({
                                    url: "{{ route('rawatinap.updateVisit') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        Visit_ID: $('#editVisitID').val(),
                                        DokterID: $('#editVisitDokterID').val(),
                                        TglVisit: $('#editVisitTanggal').val(),
                                        Pot: $('#editVisitPot').val() || 0
                                    },
                                    success: function(res) {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(
                                            xhr.responseJSON?.message ??
                                            'Gagal mengubah data visit dokter.'
                                        );
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function formatRupiahVisit(angka) {
                                angka = parseFloat(angka || 0);
                                return 'Rp ' + angka.toLocaleString('id-ID');
                            }

                            $(function() {
                                $('#modalEditVisit').on('shown.bs.modal', function() {
                                    if ($('#editVisitDokterID').hasClass("select2-hidden-accessible")) {
                                        $('#editVisitDokterID').select2('destroy');
                                    }

                                    $('#editVisitDokterID').select2({
                                        dropdownParent: $('#modalEditVisit'),
                                        width: '100%',
                                        placeholder: 'Pilih dokter...',
                                        allowClear: true
                                    });
                                });
                            });

                            function openInsertVisit() {

                                $('#editVisitID').val('');

                                $('#editVisitDokterID')
                                    .val('')
                                    .trigger('change');

                                $('#editVisitTanggal').val('{{ date('Y-m-d') }}');

                                $('#editVisitBiaya').val('Rp 0');

                                $('#editVisitPot').val(0);

                                $('#btnSimpanVisit')
                                    .attr('onclick', 'simpanInsertVisit()');

                                $('#btnHapusVisit').hide();

                                $('#modalEditVisit').modal('show');
                            }

                            function simpanInsertVisit() {

                                if (!$('#editVisitDokterID').val()) {
                                    alert('Pilih dokter terlebih dahulu.');
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.insertVisit') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        DokterID: $('#editVisitDokterID').val(),
                                        TglVisit: $('#editVisitTanggal').val(),
                                        Pot: $('#editVisitPot').val() || 0
                                    },
                                    success: function(res) {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(
                                            xhr.responseJSON?.message ??
                                            'Gagal menambahkan data visit dokter.'
                                        );
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function hapusVisit() {

                                let visitID = $('#editVisitID').val();

                                if (!visitID) {
                                    alert('Data belum tersimpan.');
                                    return;
                                }

                                if (!confirm('Yakin ingin menghapus visit dokter ini ?')) {
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.deleteVisit') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        Visit_ID: visitID
                                    },
                                    success: function(res) {

                                        $('#modalEditVisit').modal('hide');

                                        location.reload();

                                    },
                                    error: function(xhr) {

                                        alert(
                                            xhr.responseJSON?.message ??
                                            'Gagal menghapus visit dokter.'
                                        );

                                    }
                                });
                            }
                        </script>

                        {{-- Modal Edit Visit --}}
                        <div class="modal fade" id="modalEditVisit" tabindex="-1" role="dialog"
                            aria-labelledby="modalEditVisitLabel" aria-hidden="true">

                            <div class="modal-dialog modal-lg" role="document">

                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold" id="modalEditVisitLabel">
                                            <i class="fas fa-user-md mr-2"></i>
                                            Perubahan Data Visit Dokter
                                        </h5>

                                        <button type="button" class="close text-white" data-dismiss="modal"
                                            aria-label="Tutup">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" id="editVisitID">

                                        <div class="card card-outline card-info mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="fas fa-user-md mr-1"></i>
                                                    Informasi Visit Dokter
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editVisitDokterID">Dokter</label>
                                                            <select id="editVisitDokterID" class="form-control select2">
                                                                <option value="">-- Pilih Dokter --</option>
                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->ID }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editVisitTanggal">Tanggal Visit</label>
                                                            <input type="date" id="editVisitTanggal"
                                                                class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-outline card-success mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="fas fa-file-invoice-dollar mr-1"></i>
                                                    Informasi Tarif & Potongan
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editVisitBiaya">Tarif Visit</label>
                                                            <input type="text" id="editVisitBiaya"
                                                                class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="editVisitPot">Potongan (%)</label>
                                                            <input type="number" step="0.01" id="editVisitPot"
                                                                class="form-control text-right" placeholder="0">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-light border mb-0">
                                            <i class="fas fa-info-circle text-info mr-1"></i>
                                            Tarif visit otomatis mengikuti dokter dan kelas rawat terakhir pasien.
                                        </div>

                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-dismiss="modal">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tutup
                                        </button>

                                        <button type="button" id="btnSimpanVisit" class="btn btn-success btn-sm"
                                            onclick="simpanEditVisit()">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>

                                        <button type="button" id="btnHapusVisit" class="btn btn-danger btn-sm">

                                            <i class="fas fa-trash mr-1"></i>
                                            Hapus

                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Tab Utilitas --}}
                    <div class="tab-pane fade" id="tab-utilitas">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>Tindakan</th>
                                        <th>Tanggal</th>
                                        <th>Dokter</th>
                                        <th>Biaya</th>
                                        <th>Pot (%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($utilitas as $u)
                                        <tr style="cursor:pointer"
                                            onclick="openEditUtilitas(
                                                '{{ $u->ActID }}',
                                                '{{ $u->TindakID }}',
                                                '{{ $u->DokterID }}',
                                                '{{ $u->Tanggal ? date('Y-m-d', strtotime($u->Tanggal)) : '' }}',
                                                '{{ $u->Jam ? date('H:i', strtotime($u->Jam)) : '' }}',
                                                '{{ $u->Biaya ?? 0 }}',
                                                '{{ ($u->Pot ?? 0) * 100 }}'
                                            )">

                                            <td>{{ $u->NamaTindakan ?? '-' }}</td>
                                            <td>{{ $u->Tanggal ? date('d/m/Y', strtotime($u->Tanggal)) : '-' }}</td>
                                            <td>{{ $u->NamaDokter ?? '-' }}</td>
                                            <td>Rp {{ number_format($u->Biaya ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ number_format(($u->Pot ?? 0) * 100, 2) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Data utilitas / tindakan dokter belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="openInsertUtilitas()">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Tambah Utilitas
                                            </button>
                                        </td>

                                        <td colspan="2" class="text-right">
                                            Total Utilitas
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($utilitas)->sum('TotalUtilitas'), 0, ',', '.') }}
                                        </td>

                                        <td></td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                        {{-- JS Modal Utilitas --}}
                        <script>
                            function openInsertUtilitas() {
                                $('#editUtilitasActID').val('');
                                $('#editUtilitasTindakID').val('').trigger('change');
                                $('#editUtilitasDokterID').val('').trigger('change');
                                $('#editUtilitasTanggal').val('{{ date('Y-m-d') }}');
                                $('#editUtilitasJam').val('{{ date('H:i') }}');
                                $('#editUtilitasBiaya').val('Rp 0');
                                $('#editUtilitasPot').val(0);

                                $('#modalEditUtilitasLabel').html(
                                    '<i class="fas fa-plus-circle mr-2"></i> Tambah Utilitas / Tindakan Dokter'
                                );

                                $('#btnSimpanUtilitas')
                                    .attr('onclick', 'simpanInsertUtilitas()');

                                $('#btnHapusUtilitas').hide();

                                $('#modalEditUtilitas').modal('show');
                            }

                            function openEditUtilitas(
                                actId,
                                tindakId,
                                dokterId,
                                tanggal,
                                jam,
                                biaya,
                                pot
                            ) {
                                $('#editUtilitasActID').val(actId);

                                $('#editUtilitasTindakID')
                                    .val(tindakId)
                                    .trigger('change');

                                $('#editUtilitasDokterID')
                                    .val(dokterId)
                                    .trigger('change');

                                $('#editUtilitasTanggal').val(tanggal);
                                $('#editUtilitasJam').val(jam);
                                $('#editUtilitasBiaya').val(formatRupiahUtilitas(biaya));
                                $('#editUtilitasPot').val(pot);

                                $('#modalEditUtilitasLabel').html(
                                    '<i class="fas fa-stethoscope mr-2"></i> Perubahan Data Utilitas / Tindakan Dokter'
                                );

                                $('#btnSimpanUtilitas')
                                    .attr('onclick', 'simpanEditUtilitas()');

                                $('#btnHapusUtilitas')
                                    .show()
                                    .off('click')
                                    .on('click', hapusUtilitas);

                                $('#modalEditUtilitas').modal('show');
                            }

                            function simpanInsertUtilitas() {
                                if (!$('#editUtilitasTindakID').val()) {
                                    alert('Pilih tindakan terlebih dahulu.');
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.insertUtilitas') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        TindakID: $('#editUtilitasTindakID').val(),
                                        DokterID: $('#editUtilitasDokterID').val(),
                                        Tanggal: $('#editUtilitasTanggal').val(),
                                        Jam: $('#editUtilitasJam').val(),
                                        Pot: $('#editUtilitasPot').val() || 0
                                    },
                                    success: function() {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(xhr.responseJSON?.message ?? 'Gagal menambahkan data utilitas.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function simpanEditUtilitas() {
                                if (!$('#editUtilitasTindakID').val()) {
                                    alert('Pilih tindakan terlebih dahulu.');
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.updateUtilitas') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        ActID: $('#editUtilitasActID').val(),
                                        TindakID: $('#editUtilitasTindakID').val(),
                                        DokterID: $('#editUtilitasDokterID').val(),
                                        Tanggal: $('#editUtilitasTanggal').val(),
                                        Jam: $('#editUtilitasJam').val(),
                                        Pot: $('#editUtilitasPot').val() || 0
                                    },
                                    success: function() {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(xhr.responseJSON?.message ?? 'Gagal mengubah data utilitas.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function hapusUtilitas() {
                                let actID = $('#editUtilitasActID').val();

                                if (!actID) {
                                    alert('Data belum tersimpan.');
                                    return;
                                }

                                if (!confirm('Yakin ingin menghapus utilitas / tindakan dokter ini?')) {
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.deleteUtilitas') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        ActID: actID,
                                        TindakID: $('#editUtilitasTindakID').val()
                                    },
                                    success: function() {
                                        $('#modalEditUtilitas').modal('hide');
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(xhr.responseJSON?.message ?? 'Gagal menghapus data utilitas.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function formatRupiahUtilitas(angka) {
                                angka = parseFloat(angka || 0);
                                return 'Rp ' + angka.toLocaleString('id-ID');
                            }

                            $(function() {
                                $('#modalEditUtilitas').on('shown.bs.modal', function() {
                                    if ($('.select2-utilitas').hasClass("select2-hidden-accessible")) {
                                        $('.select2-utilitas').select2('destroy');
                                    }

                                    $('.select2-utilitas').select2({
                                        dropdownParent: $('#modalEditUtilitas'),
                                        width: '100%',
                                        allowClear: true
                                    });
                                });
                            });
                        </script>

                        {{-- Modal Edit Utilitas --}}
                        <div class="modal fade" id="modalEditUtilitas" tabindex="-1" role="dialog"
                            aria-labelledby="modalEditUtilitasLabel" aria-hidden="true">

                            <div class="modal-dialog modal-lg" role="document">

                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold" id="modalEditUtilitasLabel">
                                            <i class="fas fa-stethoscope mr-2"></i>
                                            Perubahan Data Utilitas / Tindakan Dokter
                                        </h5>

                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" id="editUtilitasActID">

                                        <div class="card card-outline card-info mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="fas fa-notes-medical mr-1"></i>
                                                    Informasi Tindakan
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Tindakan</label>
                                                            <select id="editUtilitasTindakID"
                                                                class="form-control select2-utilitas">
                                                                <option value="">-- Pilih Tindakan --</option>
                                                                @foreach ($tindakanList as $t)
                                                                    <option value="{{ $t->ID }}">
                                                                        {{ $t->Tindak }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Dokter</label>
                                                            <select id="editUtilitasDokterID"
                                                                class="form-control select2-utilitas">
                                                                <option value="">-- Pilih Dokter --</option>
                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->ID }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Tanggal</label>
                                                            <input type="date" id="editUtilitasTanggal"
                                                                class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Jam</label>
                                                            <input type="time" id="editUtilitasJam"
                                                                class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="card card-outline card-success mb-3">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0 font-weight-bold">
                                                    <i class="fas fa-file-invoice-dollar mr-1"></i>
                                                    Informasi Tarif & Potongan
                                                </h6>
                                            </div>

                                            <div class="card-body pb-2">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Biaya</label>
                                                            <input type="text" id="editUtilitasBiaya"
                                                                class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Potongan (%)</label>
                                                            <input type="number" step="0.01" id="editUtilitasPot"
                                                                class="form-control text-right" placeholder="0">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-light border mb-0">
                                            <i class="fas fa-info-circle text-info mr-1"></i>
                                            Tarif tindakan otomatis mengikuti tindakan dan kelas rawat terakhir pasien.
                                        </div>

                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-dismiss="modal">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tutup
                                        </button>

                                        <button type="button" id="btnSimpanUtilitas" class="btn btn-success btn-sm">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>

                                        <button type="button" id="btnHapusUtilitas" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Tab Radiologi --}}
                    <div class="tab-pane fade" id="tab-radiologi">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>IDRad</th>
                                        <th>Tanggal</th>
                                        <th>Dokter</th>
                                        <th>Total</th>

                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($radiologi as $r)
                                        @php
                                            $totalRadiologi = collect($radiologiDetail[$r->IDRad] ?? [])->sum('Biaya');
                                        @endphp

                                        <tr style="cursor:pointer" data-toggle="modal"
                                            data-target="#modalRadiologi{{ $r->IDRad }}">

                                            <td>
                                                {{ $r->IDRad ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $r->TGL ? date('d/m/Y', strtotime($r->TGL)) : '-' }}
                                            </td>

                                            <td>
                                                {{ $r->Dokter ?? '-' }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($totalRadiologi, 0, ',', '.') }}
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                Data radiologi belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>

                        </div>

                        @foreach ($radiologi as $r)
                            @php
                                $detail = $radiologiDetail[$r->IDRad][0] ?? null;
                            @endphp
                            <div class="modal fade" id="modalRadiologi{{ $r->IDRad }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow-lg">

                                        <div class="modal-header bg-info text-white">
                                            <div>
                                                <h5 class="modal-title font-weight-bold mb-0">
                                                    <i class="fas fa-x-ray mr-2"></i>
                                                    Detail Radiologi
                                                </h5>
                                                <small>ID Radiologi: {{ $r->IDRad ?? '-' }}</small>
                                            </div>

                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body" style="background:#f4f6f9; font-size:13px;">

                                            <div class="card border-0 shadow-sm mb-4">
                                                <div class="card-body py-3">

                                                    <div class="row text-sm">

                                                        <div class="col-md-2">
                                                            <small class="text-muted d-block">IDRad</small>
                                                            <span class="font-weight-bold">
                                                                {{ $r->IDRad ?? '-' }}
                                                            </span>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <small class="text-muted d-block">Tanggal</small>
                                                            <span class="font-weight-bold">
                                                                {{ $r->TGL ? date('d/m/Y', strtotime($r->TGL)) : '-' }}
                                                            </span>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <small class="text-muted d-block">Dokter</small>
                                                            <span class="font-weight-bold">
                                                                {{ $r->Dokter ?? '-' }}
                                                            </span>
                                                        </div>



                                                    </div>

                                                    <hr>

                                                    <div class="row text-sm">

                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Alat Radiologi</small>
                                                            <span class="font-weight-bold text">
                                                                {{ $detail->AlatName ?? '-' }}
                                                            </span>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Ruangan</small>
                                                            <span class="font-weight-bold text">
                                                                {{ $detail->RoomName ?? '-' }}
                                                            </span>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>

                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-white font-weight-bold">
                                                    <i class="fas fa-list text-info mr-1"></i>
                                                    Rincian Pemeriksaan
                                                </div>

                                                <div class="card-body p-0">
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Pemeriksaan</th>
                                                                <th class="text-right">Biaya</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @foreach ($radiologiDetail[$r->IDRad] ?? [] as $d)
                                                                <tr>
                                                                    <td>{{ $d->Radio_ID ?? '-' }}</td>
                                                                    <td>{{ $d->Periksa ?? '-' }}</td>
                                                                    <td class="text-right font-weight-bold">
                                                                        Rp
                                                                        {{ number_format($d->Biaya ?? 0, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>

                                                        <tfoot>
                                                            <tr class="font-weight-bold bg-light">
                                                                <td colspan="2" class="text-right">Total Radiologi</td>
                                                                <td class="text-right text-info">
                                                                    Rp
                                                                    {{ number_format(collect($radiologiDetail[$r->IDRad] ?? [])->sum('Biaya'), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- Tab Laboratorium --}}
                    <div class="tab-pane fade" id="tab-lab">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>ID Lab</th>
                                        <th>Tanggal</th>
                                        <th>Dokter</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($lab as $l)
                                        @php
                                            $jumlahLab = count($labDetail[$l->IDLab] ?? []);
                                            $totalLab = collect($labDetail[$l->IDLab] ?? [])->sum('Biaya');
                                        @endphp

                                        <tr style="cursor:pointer" data-toggle="modal"
                                            data-target="#modalLab{{ $l->IDLab }}">

                                            <td>
                                                {{ $l->IDLab ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $l->TLab ? date('d/m/Y', strtotime($l->TLab)) : '-' }}
                                            </td>

                                            <td>
                                                {{ $l->Dokter ?? '-' }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($totalLab, 0, ',', '.') }}
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Data laboratorium belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                        {{-- Modal Laboratorium --}}
                        @foreach ($lab as $l)
                            @php
                                $detailLab = $labDetail[$l->IDLab][0] ?? null;
                            @endphp

                            <div class="modal fade" id="modalLab{{ $l->IDLab }}" tabindex="-1">

                                <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                    <div class="modal-content border-0 shadow-lg">

                                        <div class="modal-header bg-info text-white">

                                            <div>
                                                <h5 class="modal-title font-weight-bold mb-0">
                                                    <i class="fas fa-vials mr-2"></i>
                                                    Detail Laboratorium
                                                </h5>

                                                <small>
                                                    ID Laboratorium : {{ $l->IDLab ?? '-' }}
                                                </small>
                                            </div>

                                            <button type="button" class="close text-white" data-dismiss="modal">

                                                <span>&times;</span>

                                            </button>

                                        </div>

                                        <div class="modal-body" style="background:#f4f6f9; font-size:13px;">

                                            {{-- Header Info --}}
                                            <div class="card border-0 shadow-sm mb-4">

                                                <div class="card-body py-3">

                                                    <div class="row text-sm align-items-center">

                                                        <div class="col-md-2">

                                                            <small class="text-muted d-block">
                                                                ID Lab
                                                            </small>

                                                            <span class="font-weight-bold text-info">
                                                                {{ $l->IDLab ?? '-' }}
                                                            </span>

                                                        </div>

                                                        <div class="col-md-3">

                                                            <small class="text-muted d-block">
                                                                Tanggal
                                                            </small>

                                                            <span class="font-weight-bold">
                                                                {{ $l->TLab ? date('d/m/Y', strtotime($l->TLab)) : '-' }}
                                                            </span>

                                                        </div>

                                                        <div class="col-md-5">

                                                            <small class="text-muted d-block">
                                                                Dokter Pengirim
                                                            </small>

                                                            <span class="font-weight-bold">
                                                                {{ $l->Dokter ?? '-' }}
                                                            </span>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                            {{-- Detail Pemeriksaan --}}
                                            <div class="card border-0 shadow-sm">

                                                <div class="card-header bg-white font-weight-bold">

                                                    <i class="fas fa-list text-info mr-1"></i>

                                                    Rincian Pemeriksaan

                                                </div>

                                                <div class="card-body p-0">

                                                    <table class="table table-sm table-bordered mb-0 hasil-lab-table">

                                                        <thead class="bg-light">

                                                            <tr>

                                                                <th width="60">
                                                                    No
                                                                </th>

                                                                <th width="130">
                                                                    Pemeriksaan
                                                                </th>

                                                                <th width="120" class="text-center hasil-col">

                                                                    Hasil

                                                                </th>

                                                                <th>
                                                                    Nilai Normal
                                                                </th>

                                                                <th width="150" class="text-right">

                                                                    Biaya

                                                                </th>

                                                            </tr>

                                                        </thead>

                                                        <tbody>

                                                            @php
                                                                $noLab = 1;
                                                                $lastKategori = null;
                                                            @endphp

                                                            @foreach ($labDetail[$l->IDLab] ?? [] as $d)
                                                                @if ($lastKategori !== $d->Kategori)
                                                                    <tr class="lab-kategori">

                                                                        <td colspan="5">

                                                                            {{ strtoupper($d->Kategori ?? '-') }}

                                                                        </td>

                                                                    </tr>

                                                                    @php
                                                                        $lastKategori = $d->Kategori;
                                                                    @endphp
                                                                @endif

                                                                <tr>

                                                                    <td>
                                                                        {{ $noLab++ }}
                                                                    </td>

                                                                    <td class="periksa-col">

                                                                        <span class="periksa-text">

                                                                            {{ $d->Perik ?? '-' }}

                                                                        </span>

                                                                    </td>

                                                                    <td class="text-center font-weight-bold hasil-value">

                                                                        {{ $d->Levels ?? '-' }}

                                                                    </td>

                                                                    <td>

                                                                        {{ $d->NorL ?? '-' }}

                                                                    </td>

                                                                    <td class="text-right font-weight-bold">

                                                                        Rp
                                                                        {{ number_format($d->Biaya ?? 0, 0, ',', '.') }}

                                                                    </td>

                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                        <tfoot class="bg-light">

                                                            <tr class="font-weight-bold">

                                                                <td colspan="4" class="text-right"
                                                                    style="
                                                                        font-size:14px;
                                                                        vertical-align:middle;
                                                                    ">

                                                                    <i class="fas fa-calculator text-info mr-1"></i>

                                                                    Total Laboratorium

                                                                </td>

                                                                <td class="text-right text-info"
                                                                    style="
                                                                        font-size:16px;
                                                                        font-weight:700;
                                                                        background:#f8fbfc;
                                                                    ">

                                                                    Rp
                                                                    {{ number_format(collect($labDetail[$l->IDLab] ?? [])->sum('Biaya'), 0, ',', '.') }}

                                                                </td>

                                                            </tr>

                                                        </tfoot>

                                                    </table>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Tab Lain-lain --}}
                    <div class="tab-pane fade" id="tab-lain">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>No</th>
                                        <th>nama</th>
                                        <th>Tanggal</th>
                                        {{-- <th>Ruang</th> --}}
                                        <th>Tarif</th>
                                        <th>Pot (%)</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($lainlain as $l)
                                        <tr style="cursor:pointer"
                                            onclick="openEditLain(
                                        '{{ $l->Lain_ID }}',
                                        '{{ $l->TGL ? date('Y-m-d', strtotime($l->TGL)) : '' }}',
                                        '{{ addslashes($l->Lain ?? '') }}',
                                        '{{ $l->BiayaLain ?? 0 }}',
                                        '{{ ($l->Pot ?? 0) * 100 }}'
                                    )">
                                            <td>{{ $l->Lain_ID ?? '-' }}</td>
                                            <td>{{ $l->Lain ?? '-' }}</td>
                                            <td>{{ $l->TGL ? date('d/m/Y', strtotime($l->TGL)) : '-' }}</td>
                                            {{--   <td>{{ $l->NamaKamar ?? '-' }}</td> --}}
                                            <td>Rp {{ number_format($l->BiayaLain ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ number_format(($l->Pot ?? 0) * 100, 2) }}%</td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Data biaya lain-lain belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                                <tfoot>
                                    <tr class="font-weight-bold bg-light">

                                        <td colspan="2">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="openInsertLain()">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Tambah Lain-lain
                                            </button>
                                        </td>

                                        <td class="text-right">
                                            Total Lain-lain
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($lainlain)->sum('TotalLain'), 0, ',', '.') }}
                                        </td>

                                        <td></td>

                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                        {{-- Modal Biaya Lain --}}
                        <div class="modal fade" id="modalInsertLain" tabindex="-1">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            Biaya Lain-lain
                                        </h5>

                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" id="lainID">

                                        <div class="form-group">
                                            <label>Tanggal</label>
                                            <input type="date" id="lainTgl" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Nama Biaya</label>
                                            <input type="text" id="lainNama" class="form-control"
                                                placeholder="Contoh: Administrasi, Materai, Ambulance">
                                        </div>

                                        <div class="form-group">
                                            <label>Tarif</label>
                                            <input type="number" id="lainBiaya" class="form-control" placeholder="0">
                                        </div>

                                        <div class="form-group">
                                            <label>Potongan (%)</label>
                                            <input type="number" step="0.01" id="lainPot" class="form-control"
                                                value="0">
                                        </div>

                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-dismiss="modal">
                                            Tutup
                                        </button>

                                        <button type="button" id="btnSimpanLain" class="btn btn-success btn-sm"
                                            onclick="simpanInsertLain()">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>

                                        <button type="button" id="btnHapusLain" class="btn btn-danger btn-sm"
                                            onclick="hapusLain()" style="display:none;">
                                            <i class="fas fa-trash mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <script>
                        function openInsertLain() {
                            $('#lainTgl').val('{{ date('Y-m-d') }}');
                            $('#lainNama').val('');
                            $('#lainBiaya').val(0);
                            $('#lainPot').val(0);

                            $('#modalInsertLain').modal('show');
                        }

                        function simpanInsertLain() {
                            if (!$('#lainNama').val()) {
                                alert('Nama biaya lain-lain wajib diisi.');
                                return;
                            }

                            $.ajax({
                                url: "{{ route('rawatinap.insertLain') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    ID: "{{ $pasien->ID }}",
                                    TGL: $('#lainTgl').val(),
                                    Lain: $('#lainNama').val(),
                                    BiayaLain: $('#lainBiaya').val(),
                                    Pot: $('#lainPot').val() || 0,
                                    KlasID: null,
                                    RoomID: null
                                },
                                success: function(res) {
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert(xhr.responseJSON?.message ?? 'Gagal menambahkan biaya lain-lain.');
                                    console.log(xhr.responseText);
                                }
                            });
                        }

                        function openInsertLain() {
                            $('#lainID').val('');
                            $('#lainTgl').val('{{ date('Y-m-d') }}');
                            $('#lainNama').val('');
                            $('#lainBiaya').val(0);
                            $('#lainPot').val(0);

                            $('#btnHapusLain').hide();
                            $('#btnSimpanLain')
                                .attr('onclick', 'simpanInsertLain()')
                                .html('<i class="fas fa-save mr-1"></i> Simpan');

                            $('#modalInsertLain').modal('show');
                        }

                        function openEditLain(lainID, tgl, nama, biaya, pot) {
                            $('#lainID').val(lainID);
                            $('#lainTgl').val(tgl);
                            $('#lainNama').val(nama);
                            $('#lainBiaya').val(biaya);
                            $('#lainPot').val(pot);

                            $('#btnHapusLain').show();
                            $('#btnSimpanLain')
                                .attr('onclick', 'simpanEditLain()')
                                .html('<i class="fas fa-save mr-1"></i> Simpan Perubahan');

                            $('#modalInsertLain').modal('show');
                        }

                        function simpanEditLain() {
                            $.ajax({
                                url: "{{ route('rawatinap.updateLain') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    ID: "{{ $pasien->ID }}",
                                    Lain_ID: $('#lainID').val(),
                                    TGL: $('#lainTgl').val(),
                                    Lain: $('#lainNama').val(),
                                    BiayaLain: $('#lainBiaya').val(),
                                    Pot: $('#lainPot').val() || 0
                                },
                                success: function() {
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert(xhr.responseJSON?.message ?? 'Gagal mengubah biaya lain-lain.');
                                }
                            });
                        }

                        function hapusLain() {
                            if (!confirm('Yakin ingin menghapus biaya lain-lain ini?')) return;

                            $.ajax({
                                url: "{{ route('rawatinap.deleteLain') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    ID: "{{ $pasien->ID }}",
                                    Lain_ID: $('#lainID').val()
                                },
                                success: function() {
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert(xhr.responseJSON?.message ?? 'Gagal menghapus biaya lain-lain.');
                                }
                            });
                        }
                    </script>

                    {{-- Tab Operasi --}}
                    <div class="tab-pane fade" id="tab-operasi">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">
                                <thead class="bg-info">
                                    <tr>
                                        <th>Operasi/ Tindakan</th>
                                        <th>Tanggal</th>
                                        <th>Operator</th>
                                        <th>Jml Netto</th>
                                        <th>Di OK</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($operasi as $o)
                                        <tr style="cursor:pointer"
                                            onclick="openEditOperasi(
                                '{{ $o->Ope_ID }}',
                                '{{ $o->JenisOp }}',
                                '{{ $o->JenisOperasi ?? '' }}',
                                '{{ $o->TgOp ? date('Y-m-d', strtotime($o->TgOp)) : '' }}',
                                '{{ $o->StartOp ? date('H:i', strtotime($o->StartOp)) : '' }}',
                                '{{ $o->EndOp ? date('H:i', strtotime($o->EndOp)) : '' }}',
                                '{{ $o->Op ?? '' }}',
                                '{{ $o->Ass ?? '' }}',
                                '{{ $o->Anes ?? '' }}',
                                '{{ $o->AssAnes ?? '' }}',
                                '{{ $o->BiayaOp ?? 0 }}',
                                '{{ $o->BiayaAss ?? 0 }}',
                                '{{ $o->BiayaAnes ?? 0 }}',
                                '{{ $o->BiayaAssAnes ?? 0 }}',
                                '{{ $o->SewaAlat ?? 0 }}',
                                '{{ $o->Bahan ?? 0 }}',
                                '{{ $o->SewaOK ?? 0 }}',
                                '{{ $o->Jasa ?? 0 }}',
                                '{{ $o->Cssd ?? 0 }}',
                                '{{ $o->PotOp ?? 0 }}',
                                '{{ $o->PotAss ?? 0 }}',
                                '{{ $o->PotAnes ?? 0 }}',
                                '{{ $o->PotAssAnes ?? 0 }}',
                                '{{ $o->PotAlat ?? 0 }}',
                                '{{ $o->PotBahan ?? 0 }}',
                                '{{ $o->PotOk ?? 0 }}',
                                '{{ $o->PotJasa ?? 0 }}',
                                '{{ $o->Brutto ?? 0 }}',
                                '{{ $o->Discount ?? 0 }}',
                                '{{ $o->Netto ?? 0 }}',
                                '{{ $o->AtOk ?? 0 }}',
                                '{{ $o->ProsenOp ?? 0 }}',
                                '{{ $o->ProsenAss ?? 0 }}',
                                '{{ $o->ProsenAnes ?? 0 }}',
                                '{{ $o->ProsenAssAnes ?? 0 }}',
                                '{{ $o->ProsenAlat ?? 0 }}',
                                '{{ $o->ProsenBahan ?? 0 }}',
                                '{{ $o->ProsenOk ?? 0 }}',
                                '{{ $o->ProsenJasa ?? 0 }}',
                                '{{ $o->Note ?? '' }}'
                            )">

                                            <td>{{ $o->JenisOperasi ?? '-' }}</td>
                                            <td>{{ $o->TgOp ? date('d/m/Y', strtotime($o->TgOp)) : '-' }}</td>
                                            <td>{{ $o->Op ?? '-' }}</td>
                                            <td>Rp {{ number_format($o->Netto ?? 0, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($o->AtOk == 1)
                                                    <i class="fas fa-check text-success"></i>
                                                @else
                                                    <i class="fas fa-times text-danger"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                Data operasi belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="openInsertOperasi()">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Tambah Operasi
                                            </button>
                                        </td>

                                        <td colspan="2" class="text-right">
                                            Total Operasi
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($operasi)->sum('Netto'), 0, ',', '.') }}
                                        </td>

                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>

                        {{-- Modal Operasi 1 Pintu --}}
                        <div class="modal fade" id="modalOperasi" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <div>
                                            <h5 class="modal-title font-weight-bold mb-0" id="modalOperasiTitle">
                                                <i class="fas fa-procedures mr-2"></i>
                                                Detail Operasi / Tindakan
                                            </h5>
                                            <small id="modalOperasiSubTitle">
                                                No. Operasi: -
                                            </small>
                                        </div>

                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body" style="background:#f4f6f9; font-size:13px;">

                                        <input type="hidden" id="operasiMode" value="insert">
                                        <input type="hidden" id="operasiOpeID">

                                        {{-- Ringkasan --}}
                                        <div class="card border-0 shadow-sm mb-3">
                                            <div class="card-body py-3">
                                                <div class="row">

                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Operasi / Tindakan</small>
                                                        <select id="operasiJenisOp" class="form-control select2-operasi">
                                                            <option value="">-- Pilih Jenis Operasi --</option>
                                                            @foreach ($jenisOpList as $j)
                                                                <option value="{{ $j->Kode_jenis }}">
                                                                    {{ $j->Nama_jenis }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block">Tanggal</small>
                                                        <input type="date" id="operasiTgOp" class="form-control">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block">Jam Operasi</small>
                                                        <div class="d-flex">
                                                            <input type="time" id="operasiStartOp"
                                                                class="form-control mr-1">
                                                            <input type="time" id="operasiEndOp"
                                                                class="form-control ml-1">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <small class="text-muted d-block">Di OK</small>
                                                        <div class="form-control d-flex align-items-center">
                                                            <input type="checkbox" id="operasiAtOk" class="mr-2">
                                                            <span>Ya</span>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <small class="text-muted d-block">Catatan</small>
                                                        <input type="text" id="operasiNote" class="form-control"
                                                            maxlength="80">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            {{-- Tim Operasi --}}
                                            <div class="col-md-4">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-header bg-white font-weight-bold">
                                                        <i class="fas fa-users text-info mr-1"></i>
                                                        Tim Operasi
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label>Operator</label>

                                                            <select id="operasiOp" class="form-control select2-dokter">

                                                                <option value="">-- Pilih Operator --</option>

                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->DokterAlias }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach

                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Asisten</label>

                                                            <select id="operasiAss" class="form-control select2-dokter">

                                                                <option value="">-- Pilih Asisten --</option>

                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->DokterAlias }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach

                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Anestesi</label>

                                                            <select id="operasiAnes" class="form-control select2-dokter">

                                                                <option value="">-- Pilih Dokter Anestesi --</option>

                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->DokterAlias }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach

                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Asisten Anestesi</label>

                                                            <select id="operasiAssAnes"
                                                                class="form-control select2-dokter">

                                                                <option value="">-- Pilih Asisten Anestesi --
                                                                </option>

                                                                @foreach ($dokterList as $d)
                                                                    <option value="{{ $d->DokterAlias }}">
                                                                        {{ $d->DokterAlias }}
                                                                    </option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Rincian Biaya --}}
                                            <div class="col-md-5">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-header bg-white font-weight-bold">
                                                        <i class="fas fa-file-invoice-dollar text-info mr-1"></i>
                                                        Rincian Biaya
                                                    </div>

                                                    <div class="card-body p-0">
                                                        <table class="table table-sm mb-0 detail-table">
                                                            <tr>
                                                                <th>Honor Operator</th>
                                                                <td><input type="text" id="biayaOp"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Honor Ass. Op</th>
                                                                <td><input type="text" id="biayaAss"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Honor dr Anestesi</th>
                                                                <td><input type="text" id="biayaAnes"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Ass. Anestesi</th>
                                                                <td><input type="text" id="biayaAssAnes"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Biaya Alat</th>
                                                                <td><input type="text" id="biayaAlat"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Bahan</th>
                                                                <td><input type="text" id="biayaBahan"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Sewa Ruang Operasi</th>
                                                                <td><input type="text" id="biayaOk"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Jasa Rumah Sakit</th>
                                                                <td><input type="text" id="biayaJasa"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <th>CSSD</th>
                                                                <td><input type="text" id="biayaCssd"
                                                                        class="form-control form-control-sm text-right"
                                                                        readonly></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Discount --}}
                                            <div class="col-md-3">
                                                <div class="card border-0 shadow-sm h-100">

                                                    <div class="card-header bg-white font-weight-bold py-2">
                                                        <i class="fas fa-percent text-info mr-1"></i>
                                                        Disc (%)
                                                    </div>

                                                    <div class="card-body p-2">

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">Op</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenOp"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">Ass</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenAss"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">Anes</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenAnes"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">Ass Anes</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenAssAnes"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">Alat</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenAlat"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">Bahan</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenBahan"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-1">
                                                            <label class="col-7 col-form-label-sm">OK</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenOk"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row mb-0">
                                                            <label class="col-7 col-form-label-sm">Jasa</label>
                                                            <div class="col-5">
                                                                <input type="number" step="0.01" id="prosenJasa"
                                                                    class="form-control form-control-sm text-right">
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- Total --}}
                                        <div class="card border-0 shadow-sm mt-3">
                                            <div class="card-body py-3">
                                                <div class="row text-center">

                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Brutto</small>
                                                        <h5 class="mb-0" id="totalBrutto">Rp 0</h5>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Discount</small>
                                                        <h5 class="mb-0 text-danger" id="totalDiscount">Rp 0</h5>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <small class="text-muted d-block">Netto</small>
                                                        <h4 class="mb-0 text-info font-weight-bold" id="totalNetto">Rp 0
                                                        </h4>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                            data-dismiss="modal">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Tutup
                                        </button>

                                        <button type="button" id="btnSimpanOperasi" class="btn btn-success btn-sm">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>

                                        <button type="button" id="btnHapusOperasi" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash mr-1"></i>
                                            Hapus
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <script>
                            function openInsertOperasi() {
                                $('#operasiMode').val('insert');
                                $('#operasiOpeID').val('');

                                $('#modalOperasiTitle').html('<i class="fas fa-plus-circle mr-2"></i>Tambah Operasi / Tindakan');
                                $('#modalOperasiSubTitle').html('No. Operasi: Baru');

                                $('#operasiJenisOp').val('').trigger('change');
                                $('#operasiTgOp').val('{{ date('Y-m-d') }}');
                                const now = new Date();
                                const jam = now.toTimeString().slice(0, 5);

                                $('#operasiStartOp').val(jam);
                                $('#operasiEndOp').val(jam);
                                $('#operasiAtOk').prop('checked', false);
                                $('#operasiNote').val('');

                                $('#operasiOp').val('');
                                $('#operasiAss').val('');
                                $('#operasiAnes').val('');
                                $('#operasiAssAnes').val('');

                                resetOperasiBiaya();
                                resetOperasiPotongan();
                                resetOperasiTotal();

                                $('#btnSimpanOperasi')
                                    .off('click')
                                    .on('click', function() {
                                        simpanOperasi("{{ route('rawatinap.insertOperasi') }}", false);
                                    });

                                $('#btnHapusOperasi').hide();

                                $('#modalOperasi').modal('show');
                            }

                            function openEditOperasi(
                                opeID,
                                jenisOp,
                                jenisOperasi,
                                tgOp,
                                startOp,
                                endOp,
                                op,
                                ass,
                                anes,
                                assAnes,
                                biayaOp,
                                biayaAss,
                                biayaAnes,
                                biayaAssAnes,
                                biayaAlat,
                                biayaBahan,
                                biayaOk,
                                biayaJasa,
                                biayaCssd,
                                potOp,
                                potAss,
                                potAnes,
                                potAssAnes,
                                potAlat,
                                potBahan,
                                potOk,
                                potJasa,
                                brutto,
                                discount,
                                netto,
                                atOk,
                                prosenOp,
                                prosenAss,
                                prosenAnes,
                                prosenAssAnes,
                                prosenAlat,
                                prosenBahan,
                                prosenOk,
                                prosenJasa,
                                note
                            ) {
                                $('#operasiMode').val('edit');
                                $('#operasiOpeID').val(opeID);

                                $('#modalOperasiTitle').html('<i class="fas fa-procedures mr-2"></i>Detail / Edit Operasi');
                                $('#modalOperasiSubTitle').html('No. Operasi: ' + opeID);

                                $('#operasiJenisOp').val(jenisOp).trigger('change');
                                $('#operasiTgOp').val(tgOp);
                                $('#operasiStartOp').val(startOp);
                                $('#operasiEndOp').val(endOp);
                                $('#operasiAtOk').prop('checked', atOk == 1);
                                $('#operasiNote').val(note);

                                $('#operasiOp').val(op);
                                $('#operasiAss').val(ass);
                                $('#operasiAnes').val(anes);
                                $('#operasiAssAnes').val(assAnes);

                                $('#biayaOp').val(formatRupiahOperasi(biayaOp));
                                $('#biayaAss').val(formatRupiahOperasi(biayaAss));
                                $('#biayaAnes').val(formatRupiahOperasi(biayaAnes));
                                $('#biayaAssAnes').val(formatRupiahOperasi(biayaAssAnes));
                                $('#biayaAlat').val(formatRupiahOperasi(biayaAlat));
                                $('#biayaBahan').val(formatRupiahOperasi(biayaBahan));
                                $('#biayaOk').val(formatRupiahOperasi(biayaOk));
                                $('#biayaJasa').val(formatRupiahOperasi(biayaJasa));
                                $('#biayaCssd').val(formatRupiahOperasi(biayaCssd));

                                $('#prosenOp').val(prosenOp);
                                $('#prosenAss').val(prosenAss);
                                $('#prosenAnes').val(prosenAnes);
                                $('#prosenAssAnes').val(prosenAssAnes);
                                $('#prosenAlat').val(prosenAlat);
                                $('#prosenBahan').val(prosenBahan);
                                $('#prosenOk').val(prosenOk);
                                $('#prosenJasa').val(prosenJasa);

                                $('#totalBrutto').html(formatRupiahOperasi(brutto));
                                $('#totalDiscount').html(formatRupiahOperasi(discount));
                                $('#totalNetto').html(formatRupiahOperasi(netto));

                                $('#btnSimpanOperasi')
                                    .off('click')
                                    .on('click', function() {
                                        simpanOperasi("{{ route('rawatinap.updateOperasi') }}", true);
                                    });

                                $('#btnHapusOperasi')
                                    .show()
                                    .off('click')
                                    .on('click', function() {
                                        hapusOperasi(opeID, $('#operasiJenisOp').val());
                                    });

                                $('#modalOperasi').modal('show');
                            }

                            function simpanOperasi(url, isEdit) {
                                if (!$('#operasiJenisOp').val()) {
                                    alert('Pilih jenis operasi terlebih dahulu.');
                                    return;
                                }

                                if (!$('#operasiOp').val()) {
                                    alert('Operator wajib diisi.');
                                    return;
                                }

                                let data = {
                                    _token: "{{ csrf_token() }}",
                                    ID: "{{ $pasien->ID }}",
                                    JenisOp: $('#operasiJenisOp').val(),
                                    TgOp: $('#operasiTgOp').val(),
                                    StartOp: $('#operasiStartOp').val(),
                                    EndOp: $('#operasiEndOp').val(),
                                    Op: $('#operasiOp').val(),
                                    Ass: $('#operasiAss').val(),
                                    Anes: $('#operasiAnes').val(),
                                    AssAnes: $('#operasiAssAnes').val(),
                                    ProsenOp: $('#prosenOp').val() || 0,
                                    ProsenAss: $('#prosenAss').val() || 0,
                                    ProsenAnes: $('#prosenAnes').val() || 0,
                                    ProsenAssAnes: $('#prosenAssAnes').val() || 0,
                                    ProsenAlat: $('#prosenAlat').val() || 0,
                                    ProsenBahan: $('#prosenBahan').val() || 0,
                                    ProsenOk: $('#prosenOk').val() || 0,
                                    ProsenJasa: $('#prosenJasa').val() || 0,
                                    AtOk: $('#operasiAtOk').is(':checked') ? 1 : 0,
                                    Note: $('#operasiNote').val()
                                };

                                if (isEdit) {
                                    data.Ope_ID = $('#operasiOpeID').val();
                                }

                                $.ajax({
                                    url: url,
                                    type: "POST",
                                    data: data,
                                    success: function() {
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(xhr.responseJSON?.message ?? 'Gagal menyimpan data operasi.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function hapusOperasi(opeID, jenisOp) {
                                if (!confirm('Yakin ingin menghapus data operasi ini?')) {
                                    return;
                                }

                                $.ajax({
                                    url: "{{ route('rawatinap.deleteOperasi') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ID: "{{ $pasien->ID }}",
                                        Ope_ID: opeID,
                                        JenisOp: jenisOp
                                    },
                                    success: function() {
                                        $('#modalOperasi').modal('hide');
                                        location.reload();
                                    },
                                    error: function(xhr) {
                                        alert(xhr.responseJSON?.message ?? 'Gagal menghapus data operasi.');
                                        console.log(xhr.responseText);
                                    }
                                });
                            }

                            function resetOperasiBiaya() {
                                $('#biayaOp').val('Rp 0');
                                $('#biayaAss').val('Rp 0');
                                $('#biayaAnes').val('Rp 0');
                                $('#biayaAssAnes').val('Rp 0');
                                $('#biayaAlat').val('Rp 0');
                                $('#biayaBahan').val('Rp 0');
                                $('#biayaOk').val('Rp 0');
                                $('#biayaJasa').val('Rp 0');
                                $('#biayaCssd').val('Rp 0');
                            }

                            function resetOperasiPotongan() {
                                $('#prosenOp').val(0);
                                $('#prosenAss').val(0);
                                $('#prosenAnes').val(0);
                                $('#prosenAssAnes').val(0);
                                $('#prosenAlat').val(0);
                                $('#prosenBahan').val(0);
                                $('#prosenOk').val(0);
                                $('#prosenJasa').val(0);
                            }

                            function resetOperasiTotal() {
                                $('#totalBrutto').html('Rp 0');
                                $('#totalDiscount').html('Rp 0');
                                $('#totalNetto').html('Rp 0');
                            }

                            function formatRupiahOperasi(angka) {
                                angka = parseFloat(angka || 0);
                                return 'Rp ' + angka.toLocaleString('id-ID');
                            }

                            $(function() {
                                $('#modalOperasi').on('shown.bs.modal', function() {
                                    if ($('.select2-operasi').hasClass("select2-hidden-accessible")) {
                                        $('.select2-operasi').select2('destroy');
                                    }

                                    $('.select2-operasi').select2({
                                        dropdownParent: $('#modalOperasi'),
                                        width: '100%',
                                        allowClear: true
                                    });
                                });
                            });

                            $('#modalOperasi').on('shown.bs.modal', function() {

                                $('.select2-dokter').select2({
                                    dropdownParent: $('#modalOperasi'),
                                    width: '100%'
                                });

                            });
                        </script>

                    </div>

                    {{-- Tab Farmasi --}}
                    <div class="tab-pane fade" id="tab-farmasi">

                        {{-- Obat Billing --}}
                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>Tanggal Invoice</th>
                                        <th>Ruangan</th>
                                        <th>Jumlah Hutang Obat</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($obat as $o)
                                        <tr>
                                            <td>
                                                {{ $o->{'Invoice date'} ? date('d/m/Y', strtotime($o->{'Invoice date'})) : '-' }}
                                            </td>
                                            <td> {{ $o->RoomName ?? '-' }} </td>
                                            <td>
                                                Rp {{ number_format($o->HutangObat ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                Data obat belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                                <tfoot>

                                    <tr class="font-weight-bold bg-light">

                                        <td colspan="2" class="text-right">
                                            Total Obat Billing
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($obat)->sum('HutangObat'), 0, ',', '.') }}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>
                        {{-- Jarak --}}
                        <div class="mt-4"></div>

                        {{-- Oba Pay --}}
                        <div class="table-responsive mt-4">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>
                                        <th>Tanggal Invoice</th>
                                        <th>No Transaksi</th>
                                        <th>Jumlah Hutang Obat</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($salesFarmasi as $index => $sale)
                                        <tr data-toggle="collapse" data-target="#detailObat{{ $index }}"
                                            style="cursor: pointer;">
                                            <td>
                                                {{ !empty($sale['date']) ? date('d/m/Y', strtotime($sale['date'])) : '-' }}
                                            </td>

                                            <td>
                                                {{ $sale['transaction_no'] ?? '-' }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($sale['summary']['total'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>

                                        <tr class="collapse" id="detailObat{{ $index }}">
                                            <td colspan="3" class="bg-light">

                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Obat</th>
                                                            <th>Qty</th>
                                                            <th>Satuan</th>
                                                            <th>Harga</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($sale['items'] ?? [] as $item)
                                                            <tr>
                                                                <td>{{ $item['name'] ?? '-' }}</td>
                                                                <td>{{ $item['qty'] ?? 0 }}</td>
                                                                <td>{{ $item['unit'] ?? '-' }}</td>
                                                                <td>
                                                                    Rp
                                                                    {{ number_format($item['unit_price'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>
                                                                    Rp
                                                                    {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                Data ObaPay belum tersedia.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="2" class="text-right">
                                            Total Obat Pay
                                        </td>

                                        <td>
                                            Rp {{ number_format($grandTotalFarmasiApi ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>

                    {{-- Tab Kasir --}}
                    <div class="tab-pane fade" id="tab-kasir">

                        @php
                            $k = $kasir[0] ?? null;
                            $sudahAdaKasir = !empty($k);
                        @endphp

                        <form id="formKasir" action="{{ route('rawatinap.simpanKasir', $pasien->ID) }}" method="POST">

                            @csrf

                            <div class="card card-outline card-info shadow-sm">

                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-cash-register mr-1"></i>
                                        Informasi Kasir Pembayaran
                                    </h3>
                                </div>

                                <div class="card-body">

                                    @if ($sudahAdaKasir)
                                        <div class="alert alert-warning">
                                            <i class="fas fa-lock mr-1"></i>
                                            Data kasir sudah tersimpan dan tidak dapat diubah.
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Kasir</label>

                                                <div class="col-sm-8">
                                                    <select class="form-control form-control-sm" name="KasirID"
                                                        id="KasirID" {{ $sudahAdaKasir ? 'disabled' : '' }} required>

                                                        <option value="">-- Pilih Kasir --</option>

                                                        @foreach ($kasirList as $ks)
                                                            <option value="{{ $ks->KasirID }}"
                                                                {{ ($k->Kasir ?? '') == $ks->KasirID ? 'selected' : '' }}>
                                                                {{ $ks->Kasir }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Dibayar Oleh</label>

                                                <div class="col-sm-8">
                                                    <input type="text" name="payBy"
                                                        class="form-control form-control-sm"
                                                        value="{{ $k->payBy ?? '' }}"
                                                        placeholder="Nama pembayar / keluarga pasien" maxlength="60"
                                                        {{ $sudahAdaKasir ? 'readonly' : '' }}>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Shift Kasir</label>

                                                <div class="col-sm-8">
                                                    <select name="Shift" class="form-control form-control-sm"
                                                        {{ $sudahAdaKasir ? 'disabled' : '' }}>

                                                        <option value="">-- Pilih Shift --</option>

                                                        <option value="P"
                                                            {{ ($k->Shift ?? '') == 'P' ? 'selected' : '' }}>
                                                            Pagi
                                                        </option>

                                                        <option value="S"
                                                            {{ ($k->Shift ?? '') == 'S' ? 'selected' : '' }}>
                                                            Sore
                                                        </option>

                                                        <option value="M"
                                                            {{ ($k->Shift ?? '') == 'M' ? 'selected' : '' }}>
                                                            Malam
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Data kasir hanya dapat disimpan satu kali untuk setiap registrasi pasien.
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                @if (!$sudahAdaKasir)
                                    <div class="card-footer text-left">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan
                                        </button>
                                    </div>
                                @endif

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        @php
            $grandTotal =
                ($pasien->Biaya ?? 0) +
                ($pasien->JasaPrk ?? 0) +
                collect($kamar)->sum('TotalSewa') +
                collect($kamar)->sum('TotalAskep') +
                collect($visitdokter)->sum('TotalVisit') +
                collect($utilitas)->sum('TotalUtilitas') +
                collect($radiologiDetailFlat ?? [])->sum('Biaya') +
                collect($labDetailFlat ?? [])->sum('Biaya') +
                collect($lainlain)->sum('TotalLain') +
                collect($operasi)->sum('Netto') +
                collect($obat)->sum('HutangObat') +
                ($grandTotalFarmasiApi ?? 0);

            $hutangObat = collect($obat)->sum('HutangObat') + ($grandTotalFarmasiApi ?? 0);
            $dijamin = $pasien->DownPay ?? 0;
            $plafon = $pasien->Phk3 ?? 0;
            $totalDiscount = 0;
            $netto = $grandTotal - $dijamin;
        @endphp

        <div class="card border-0 shadow-sm billing-total-card">

            <div class="card-body pt-3">

                <div class="row">

                    <div class="col-md-4">
                        <div class="billing-box position-relative">

                            <div class="billing-row billing-clickable" data-toggle="modal"
                                data-target="#modalKarcisJasa">
                                <span>Karcis</span>
                                <strong id="labelBiaya">
                                    Rp {{ number_format($pasien->Biaya ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>

                            <div class="billing-row billing-clickable" data-toggle="modal"
                                data-target="#modalKarcisJasa">
                                <span>Jasa</span>
                                <strong id="labelJasa">
                                    Rp {{ number_format($pasien->JasaPrk ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>

                            <div class="billing-row">
                                <span>Hutang Obat</span>
                                <strong>
                                    Rp {{ number_format($hutangObat, 0, ',', '.') }}
                                </strong>
                            </div>

                        </div>
                    </div>

                    {{-- Modal Edit Karcis Dan Jasa --}}
                    <div class="modal fade" id="modalKarcisJasa" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow">

                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit Karcis & Jasa
                                    </h5>

                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <input type="hidden" id="editID" value="{{ $pasien->ID }}">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Karcis</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>

                                            <input type="text" class="form-control text-right rupiah-input"
                                                id="editKarcis"
                                                value="{{ number_format($pasien->Biaya ?? 0, 0, ',', '.') }}">
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Jasa</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>

                                            <input type="text" class="form-control text-right rupiah-input"
                                                id="editJasaPrk"
                                                value="{{ number_format($pasien->JasaPrk ?? 0, 0, ',', '.') }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="button" class="btn btn-info" onclick="autoKarcisJasa()">
                                        <i class="fas fa-sync"></i> Auto Tarif
                                    </button>

                                    <button type="button" class="btn btn-success" onclick="simpanKarcisJasa()">
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-danger" onclick="hapusKarcisJasa()">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- JS Edit Karcis Dan jasa --}}
                    <script>
                        function onlyNumber(value) {
                            return value.toString().replace(/\D/g, '');
                        }

                        function formatRupiah(value) {
                            value = onlyNumber(value);

                            if (!value) {
                                return '0';
                            }

                            return new Intl.NumberFormat('id-ID').format(value);
                        }

                        $('.rupiah-input').on('input', function() {
                            this.value = formatRupiah(this.value);
                        });

                        function simpanKarcisJasa() {

                            $.ajax({
                                url: "{{ route('rawatjalan.updateKarcisJasa', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    biaya: onlyNumber($('#editKarcis').val()),
                                    jasa: onlyNumber($('#editJasaPrk').val())
                                },
                                success: function(response) {

                                    $('#modalKarcisJasa').modal('hide');

                                    setTimeout(function() {
                                        location.reload();
                                    }, 300);

                                },
                                error: function(xhr) {
                                    alert(xhr.responseText);
                                    console.log(xhr.responseText);
                                }
                            });

                        }

                        function hapusKarcisJasa() {

                            if (!confirm('Yakin ingin menghapus Karcis dan Jasa?')) {
                                return;
                            }

                            $.ajax({
                                url: "{{ route('rawatjalan.hapusKarcisJasa', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },

                                success: function(response) {

                                    $('#modalKarcisJasa').modal('hide');

                                    location.reload();

                                },

                                error: function(xhr) {

                                    alert(xhr.responseText);

                                }
                            });

                        }

                        function autoKarcisJasa() {

                            $.ajax({
                                url: "{{ route('rawatjalan.autoKarcisJasa', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function() {
                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert(xhr.responseText);
                                }
                            });

                        }
                    </script>

                    <div class="col-md-4">
                        <div class="billing-box">
                            <div class="billing-row highlight">
                                <span>Grand Total</span>
                                <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </div>

                            <div class="billing-row billing-clickable" data-toggle="modal"
                                data-target="#modalDijaminPlafon">
                                <span>Dijamin / Dibayar</span>
                                <strong id="labelDibyr">
                                    Rp {{ number_format($pasien->DownPay ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>

                            <div class="billing-row billing-clickable" data-toggle="modal"
                                data-target="#modalDijaminPlafon">
                                <span>Plafon PHK3</span>
                                <strong id="labelPhk3">
                                    Rp {{ number_format($pasien->Phk3 ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Edit Dijamin Dan PHK3 --}}
                    <div class="modal fade" id="modalDijaminPlafon" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                            <div class="modal-content border-0 shadow">

                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit Dijamin & Plafon PHK3
                                    </h5>

                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <input type="hidden" id="editID" value="{{ $pasien->ID }}">

                                    <div class="form-group">
                                        <label class="font-weight-bold">Dijamin / DownPay</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>

                                            <input type="text" class="form-control text-right rupiah-input"
                                                id="editDownPay"
                                                value="{{ number_format($pasien->DownPay ?? 0, 0, ',', '.') }}">
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Plafon PHK3</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>

                                            <input type="text" class="form-control text-right rupiah-input"
                                                id="editPhk3"
                                                value="{{ number_format($pasien->Phk3 ?? 0, 0, ',', '.') }}">
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Batal
                                    </button>

                                    <button type="button" class="btn btn-success" onclick="simpanDijaminPlafon()">
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-danger" onclick="hapusDijaminPlafon()">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- JS Edit Dijamin Dan PHK3 --}}
                    <script>
                        function simpanDijaminPlafon() {

                            $.ajax({
                                url: "{{ route('rawatinap.updateDijaminPlafon', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    downpay: onlyNumber($('#editDownPay').val()),
                                    phk3: onlyNumber($('#editPhk3').val())
                                },
                                success: function(response) {

                                    $('#modalDijaminPlafon').modal('hide');

                                    setTimeout(function() {
                                        location.reload();
                                    }, 300);
                                },
                                error: function(xhr) {
                                    alert(xhr.responseText);
                                    console.log(xhr.responseText);
                                }
                            });
                        }

                        function hapusDijaminPlafon() {

                            if (!confirm('Yakin ingin menghapus Dijamin dan Plafon PHK3?')) {
                                return;
                            }

                            $.ajax({
                                url: "{{ route('rawatinap.hapusDijaminPlafon', $pasien->ID) }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {

                                    $('#modalDijaminPlafon').modal('hide');

                                    location.reload();
                                },
                                error: function(xhr) {
                                    alert(xhr.responseText);
                                }
                            });
                        }
                    </script>

                    <div class="col-md-4">
                        <div class="billing-box billing-final">
                            <div class="billing-row">
                                <span>Total Discount</span>
                                <strong>Rp {{ number_format($totalDiscount, 0, ',', '.') }}</strong>
                            </div>

                            <div class="billing-row netto">
                                <span>Netto</span>
                                <strong>Rp {{ number_format($netto, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    @endif

@stop

<style>
    .detail-table th {
        width: 55%;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
        padding: 7px 10px !important;
        vertical-align: middle;
    }

    .detail-table td {
        padding: 7px 10px !important;
        vertical-align: middle;
        background: #ffffff;
    }

    .modal-xl {
        max-width: 1100px;
    }

    .hasil-lab-table {
        background: #ffffff;
        font-size: 13px;
    }

    .hasil-lab-table thead th {
        border-top: 2px solid #2f2f2f !important;
        border-bottom: 2px solid #2f2f2f !important;
        color: #111;
        font-weight: 700;
        font-size: 12px;
        padding: 8px 10px !important;
        text-transform: uppercase;
    }

    .hasil-lab-table td {
        border-top: none !important;
        padding: 6px 10px !important;
        color: #111;
        vertical-align: middle;
    }

    .lab-kategori td {
        padding-top: 14px !important;
        padding-bottom: 3px !important;
        font-weight: 800;
        text-transform: uppercase;
        background: #ffffff !important;
        color: #111;
    }

    .hasil-lab-table tfoot td {
        border-top: 2px solid #2f2f2f !important;
        padding: 10px !important;
        background: #f8f9fa;
    }

    .modal-xl {
        max-width: 1150px;
    }

    .hasil-lab-table {
        table-layout: fixed;
        width: 100%;
    }

    .hasil-col {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;
    }

    .hasil-value {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        font-size: 14px;
        color: #0A7C86;
    }

    .hasil-lab-table td:nth-child(4) {
        word-break: break-word;
        white-space: normal;
    }



    .periksa-col {
        font-size: 12px !important;
        line-height: 1.2;
    }

    .periksa-text {
        font-style: italic;
        font-weight: 600;
        color: #2f2f2f;

        display: inline-block;

        max-width: 100%;
        word-break: break-word;
    }


    .sep-print-preview {
        background: #fff;
        color: #000;
        padding: 20px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
    }

    .sep-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .sep-logo img {
        width: 240px;
        height: auto;
    }

    .sep-title {
        flex: 1;
        text-align: center;
        line-height: 1.2;
        font-weight: 800;
    }

    .sep-title div {
        font-size: 28px;
    }

    .sep-title small {
        display: block;
        font-size: 22px;
        margin-top: 5px;
        font-weight: 800;
    }

    .sep-table td {
        border: none !important;
        padding: 2px 4px !important;
        vertical-align: top;
        font-size: 15px;
    }

    .sep-table td:first-child {
        width: 140px;
        white-space: nowrap;
    }

    .sep-table td:nth-child(2) {
        width: 10px;
    }

    .sep-note {
        margin-top: 10px;
        font-size: 13px;
        line-height: 1.6;
    }

    #modalSep .modal-dialog {
        max-width: 1200px;
    }


    .billing-total-card {
        border-radius: 14px;
        overflow: hidden;
    }

    .billing-box {
        background: #f8fbfc;
        border: 1px solid #e3eef1;
        border-radius: 12px;
        padding: 10px;
        height: 100%;
    }

    .billing-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 6px;
        border-bottom: 1px dashed #d9e6e9;
    }

    .billing-row:last-child {
        border-bottom: none;
    }

    .billing-row span {
        font-size: 13px;
        color: #6c757d;
        font-weight: 600;
    }

    .billing-row strong {
        font-size: 14px;
        color: #212529;
        font-weight: 700;
        text-align: right;
    }

    .billing-row.highlight strong {
        color: #0A7C86;
        font-size: 15px;
    }

    .billing-final {
        background: #eefafa;
        border-color: #b9e8ec;
    }

    .billing-row.netto {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .billing-row.netto span {
        color: #0A7C86;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .billing-row.netto strong {
        color: #0A7C86;
        font-size: 19px;
    }

    .billing-clickable {
        cursor: pointer;
        transition: .2s;
        border-radius: 6px;
        padding: 6px 8px;
    }

    .billing-clickable:hover {
        background: #eef9fb;
        color: #0c7c89;
    }
</style>


{{-- JS Modal Update PXRS --}}
@push('scripts')
    <script>
        $(function() {

            $(document).on('submit', '#formUpdatePxRS', function(e) {
                e.preventDefault();

                let form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function() {

                        $('#modalUpdatePxRS').modal('hide');

                        $('#modalUpdatePxRS').on('hidden.bs.modal', function() {
                            window.location.href = window.location.pathname;
                        });
                    },
                    error: function(xhr) {
                        alert('Gagal memperbarui PxRS.');
                        console.log(xhr.responseText);
                    }
                });
            });

        });
    </script>
@endpush
