@extends('adminlte::page')

@section('title', 'Rawat Inap')

@section('content_header')
    <h1>Data Rawat Inap</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header bg-info">
        <h3 class="card-title">
            Pasien Rawat Inap Aktif
        </h3>
    </div>

    <div class="card-body">

        <table id="tbl" class="table table-bordered table-striped">

            <thead class="bg-info">

                <tr>
                    <th>ID</th>
                    <th>No RM</th>
                    <th>Nama</th>
                    <th>Kamar</th>
                    <th>Kelas</th>
                    <th>Penjamin</th>
                    <th>Tanggal Masuk</th>
                    <th>Telepon</th>
                </tr>

            </thead>

            <tbody>

                @foreach($data as $d)

                <tr>
                    <td>{{ $d->ID }}</td>
                    <td>{{ $d->RegNum }}</td>
                    <td>{{ $d->Nama }}</td>
                    <td>{{ $d->RoomName }}</td>
                    <td>{{ $d->Kelas }}</td>
                    <td>{{ $d->PxRS }}</td>
                    <td>{{ date('d-m-Y H:i', strtotime($d->TIN)) }}</td>
                    <td>{{ $d->Telepon }}</td>
                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@stop