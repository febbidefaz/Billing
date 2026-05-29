@extends('adminlte::page')

@section('title', 'Detail Billing Rawat Inap')

@section('content_header')
    <div class="d-flex align-items-center">

        <a href="{{ route('rawatinap.index') }}" class="btn btn-secondary btn-sm mr-3">

            <i class="fas fa-arrow-left fa-2x"></i>

        </a>

        <h1 class="mb-0">
            Billing Rawat Inap
        </h1>

    </div>
@stop

@section('content')

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

                        <div style="font-size:16px">
                            {{ $pasien->TglByr ? date('d/m/Y', strtotime($pasien->TglByr)) : '-' }}
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm"
                        onclick="showBpjsPeserta('{{ $pasien->NoJKN ?? '' }}')">
                        <i class="fas fa-id-card mr-1"></i>
                        Cek BPJS
                    </button>


                    {{-- Cek Kartu BPJS --}}
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

                    <button type="button" class="btn btn-info btn-sm" onclick="printBilling()">
                        <i class="fas fa-print"></i> Print
                    </button>

                    <script>
                        function printBilling() {

                            let printWindow = window.open(
                                "{{ route('rawatinap.rekeningPrint', $pasien->ID) }}",
                                "PRINT",
                                "height=800,width=1000"
                            );

                            printWindow.focus();

                            setTimeout(function() {
                                printWindow.print();
                            }, 1000);

                            printWindow.onafterprint = function() {
                                printWindow.close();
                            };
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
                                        <tr>
                                            <td>{{ $k->NamaKamar ?? '-' }}</td>
                                            <td>Rp {{ number_format($k->Biaya ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ $k->NamaDokter ?? '-' }}</td>
                                            <td>{{ $k->TMasuk ? date('d/m/Y', strtotime($k->TMasuk)) : '-' }}</td>
                                            <td>{{ $k->TKeluar ? date('d/m/Y', strtotime($k->TKeluar)) : '-' }}</td>
                                            <td>
                                                {{ $k->JKeluar ? date('H:i', strtotime($k->JKeluar)) : '-' }}
                                            </td>
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
                                        <td colspan="8" class="text-right">Total Kamar</td>
                                        <td>Rp {{ number_format(collect($kamar)->sum('TotalSewa'), 0, ',', '.') }}</td>

                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>

                    {{-- Tab Visite --}}
                    <div class="tab-pane fade" id="tab-visit">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped mb-0">

                                <thead class="bg-info">
                                    <tr>

                                        <th>Dokter</th>
                                        {{-- <th>Ruangan</th> --}}
                                        <th>Tgl Visit</th>
                                        <th>Tarif</th>
                                        <th>Pot (%)</th>

                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($visitdokter as $v)
                                        <tr>
                                            <td>{{ $v->NamaDokter ?? '-' }}</td>
                                            {{-- <td>{{ $v->NamaKamar ?? '-' }}</td> --}}
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

                                        <td colspan="2" class="text-right">
                                            Total Visit
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($visitdokter)->sum('TotalVisit'), 0, ',', '.') }}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

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
                                        {{-- <th>Ruangan</th> --}}
                                        <th>Biaya</th>
                                        <th>Pot (%)</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($utilitas as $u)
                                        <tr>
                                            <td>{{ $u->NamaTindakan ?? '-' }}</td>
                                            <td>{{ $u->Tanggal ? date('d/m/Y', strtotime($u->Tanggal)) : '-' }}</td>
                                            <td>{{ $u->NamaDokter ?? '-' }}</td>
                                            {{--  <td>{{ $u->NamaKamar ?? '-' }}</td> --}}

                                            <td>
                                                Rp {{ number_format($u->Biaya ?? 0, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                {{ number_format(($u->Pot ?? 0) * 100, 2) }}%
                                            </td>
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
                                        <td colspan="3" class="text-right">Total Utilitas</td>
                                        <td>
                                            Rp {{ number_format(collect($utilitas)->sum('TotalUtilitas'), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>

                            </table>

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
                                                                        Rp {{ number_format($d->Biaya ?? 0, 0, ',', '.') }}
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
                                        <tr>
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

                                        <td colspan="3" class="text-right">
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

                    </div>

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
                                        <tr style="cursor:pointer" data-toggle="modal"
                                            data-target="#modalOperasi{{ $o->Ope_ID }}">

                                            <td>{{ $o->JenisOperasi ?? '-' }}</td>
                                            <td>{{ $o->TgOp ? date('d/m/Y', strtotime($o->TgOp)) : '-' }}</td>
                                            <td> {{ $o->Op ?? '-' }} </td>
                                            <td> Rp {{ number_format($o->Netto ?? 0, 0, ',', '.') }} </td>
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

                                        <td colspan="3" class="text-right">
                                            Total Operasi
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($operasi)->sum('Netto'), 0, ',', '.') }}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                            {{-- Modal Tab Operasi --}}
                            @foreach ($operasi as $o)
                                <div class="modal fade" id="modalOperasi{{ $o->Ope_ID }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow-lg">

                                            <div class="modal-header bg-info text-white">
                                                <div>
                                                    <h5 class="modal-title font-weight-bold mb-0">
                                                        <i class="fas fa-procedures mr-2"></i>
                                                        Detail Operasi / Tindakan
                                                    </h5>
                                                    <small>
                                                        No. Operasi: {{ $o->Ope_ID ?? '-' }}
                                                    </small>
                                                </div>

                                                <button type="button" class="close text-white" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body" style="background:#f4f6f9; font-size:13px;">

                                                {{-- Ringkasan --}}
                                                <div class="card border-0 shadow-sm mb-3">
                                                    <div class="card-body py-3">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block">Operasi /
                                                                    Tindakan</small>
                                                                <strong>{{ $o->JenisOperasi ?? '-' }}</strong>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <small class="text-muted d-block">Tanggal</small>
                                                                <strong>{{ $o->TgOp ? date('d/m/Y', strtotime($o->TgOp)) : '-' }}</strong>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <small class="text-muted d-block">Jam Operasi</small>
                                                                <strong>
                                                                    {{ $o->StartOp ? date('H:i', strtotime($o->StartOp)) : '-' }}
                                                                    -
                                                                    {{ $o->EndOp ? date('H:i', strtotime($o->EndOp)) : '-' }}
                                                                </strong>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <small class="text-muted d-block">Di OK</small>
                                                                @if ($o->AtOk == 1)
                                                                    <span class="badge badge-success px-3">Ya</span>
                                                                @else
                                                                    <span class="badge badge-secondary px-3">Tidak</span>
                                                                @endif
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

                                                            <div class="card-body p-0">
                                                                <table class="table table-sm mb-0 detail-table">
                                                                    <tr>
                                                                        <th>Operator</th>
                                                                        <td>{{ $o->Op ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Asisten</th>
                                                                        <td>{{ $o->Ass ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Anestesi</th>
                                                                        <td>{{ $o->Anes ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Asisten Anestesi</th>
                                                                        <td>{{ $o->AssAnes ?? '-' }}</td>
                                                                    </tr>
                                                                </table>
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
                                                                        <td class="text-right">
                                                                            {{ number_format($o->BiayaOp ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Honor Ass. Op</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->BiayaAss ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Honor dr Anestesi</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->BiayaAnes ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ass. Anestesi</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->BiayaAssAnes ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Biaya Alat</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->SewaAlat ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Bahan</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->Bahan ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Sewa Ruang Operasi</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->SewaOK ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Jasa Rumah Sakit</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->Jasa ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>CSSD</th>
                                                                        <td class="text-right">
                                                                            {{ number_format($o->Cssd ?? 0, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Discount --}}
                                                    <div class="col-md-3">
                                                        <div class="card border-0 shadow-sm h-100">
                                                            <div class="card-header bg-white font-weight-bold">
                                                                <i class="fas fa-percent text-info mr-1"></i>
                                                                Discount
                                                            </div>

                                                            <div class="card-body p-0">
                                                                <table class="table table-sm mb-0 detail-table">
                                                                    <tr>
                                                                        <th>Operator</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotOp ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ass. Op</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotAss ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Anestesi</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotAnes ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ass. Anestesi</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotAssAnes ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Alat</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotAlat ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Bahan</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotBahan ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ruang OK</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotOk ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Jasa RS</th>
                                                                        <td class="text-right">
                                                                            {{ number_format(($o->PotJasa ?? 0) * 100, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                </table>
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
                                                                <h5 class="mb-0">
                                                                    Rp {{ number_format($o->Brutto ?? 0, 0, ',', '.') }}
                                                                </h5>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block">Discount</small>
                                                                <h5 class="mb-0 text-danger">
                                                                    Rp {{ number_format($o->Discount ?? 0, 0, ',', '.') }}
                                                                </h5>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block">Netto</small>
                                                                <h4 class="mb-0 text-info font-weight-bold">
                                                                    Rp {{ number_format($o->Netto ?? 0, 0, ',', '.') }}
                                                                </h4>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    {{-- Tab Farmasi --}}
                    <div class="tab-pane fade" id="tab-farmasi">

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
                                            Total Obat
                                        </td>

                                        <td>
                                            Rp {{ number_format(collect($obat)->sum('HutangObat'), 0, ',', '.') }}
                                        </td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

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
                collect($obat)->sum('HutangObat');

            $hutangObat = collect($obat)->sum('HutangObat');
            $dijamin = $pasien->DownPay ?? 0;
            $plafon = $pasien->Phk3 ?? 0;
            $totalDiscount = 0;
            $netto = $grandTotal - $dijamin;
        @endphp

        <div class="card border-0 shadow-sm billing-total-card">

            <div class="card-body pt-3">

                <div class="row">

                    <div class="col-md-4">
                        <div class="billing-box">
                            <div class="billing-row">
                                <span>Karcis</span>
                                <strong>Rp {{ number_format($pasien->Biaya ?? 0, 0, ',', '.') }}</strong>
                            </div>

                            <div class="billing-row">
                                <span>Jasa</span>
                                <strong>Rp {{ number_format($pasien->JasaPrk ?? 0, 0, ',', '.') }}</strong>
                            </div>

                            <div class="billing-row">
                                <span>Hutang Obat</span>
                                <strong>Rp {{ number_format($hutangObat, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="billing-box">
                            <div class="billing-row highlight">
                                <span>Grand Total</span>
                                <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                            </div>

                            <div class="billing-row">
                                <span>Dijamin / Dibayar</span>
                                <strong>Rp {{ number_format($dijamin, 0, ',', '.') }}</strong>
                            </div>

                            <div class="billing-row">
                                <span>Plafon PHK3</span>
                                <strong>Rp {{ number_format($plafon, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

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
</style>


{{-- JS Modal Update PXRS --}}
@section('js')
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
@stop
