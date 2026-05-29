@extends('adminlte::page')

@section('title', 'User Billing')

@section('content_header')
    <h1>User Billing</h1>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-2 text-Left">
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalTambahUser">

            <i class="fas fa-user-plus"></i>
            Tambah User

        </button>
    </div>

    <div class="modal fade" id="modalTambahUser" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-user-plus mr-2"></i>
                        Tambah User Billing
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('userbilling.store') }}">
                    @csrf

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="Nama" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="Username" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="Password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="Role" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="kasir">Kasir</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label>Aktif</label>
                            <select name="Aktif" class="form-control">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i>
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar User</h3>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-info">
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aktif</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $u)
                        <tr style="cursor:pointer" data-toggle="modal" data-target="#modalEditUser{{ $u->ID }}">
                            <td>{{ $u->Nama }}</td>
                            <td>{{ $u->Username }}</td>
                            <td>{{ $u->Role }}</td>
                            <td>
                                @if ($u->Aktif == 1)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Modal Edit User --}}
                        <div class="modal fade" id="modalEditUser{{ $u->ID }}" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content border-0 shadow-lg">

                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-user-edit mr-2"></i>
                                            Edit User Billing
                                        </h5>

                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('userbilling.update', $u->ID) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label>Nama</label>
                                                <input type="text" name="Nama" class="form-control form-control-sm"
                                                    value="{{ $u->Nama }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="Username" class="form-control form-control-sm"
                                                    value="{{ $u->Username }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Password</label>
                                                <input type="password" name="Password" class="form-control form-control-sm"
                                                    placeholder="Kosongkan jika tidak diganti">
                                            </div>

                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="Role" class="form-control form-control-sm" required>
                                                    <option value="admin" {{ $u->Role == 'admin' ? 'selected' : '' }}>
                                                        Admin</option>
                                                    <option value="kasir" {{ $u->Role == 'kasir' ? 'selected' : '' }}>
                                                        Kasir</option>
                                                    <option value="user" {{ $u->Role == 'user' ? 'selected' : '' }}>User
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label>Aktif</label>
                                                <select name="Aktif" class="form-control form-control-sm">
                                                    <option value="1" {{ $u->Aktif == 1 ? 'selected' : '' }}>Ya
                                                    </option>
                                                    <option value="0" {{ $u->Aktif == 0 ? 'selected' : '' }}>Tidak
                                                    </option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">

                                            <form action="{{ route('userbilling.destroy', $u->ID) }}" method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus user ini?')">

                                                    <i class="fas fa-trash"></i>
                                                    Hapus

                                                </button>

                                            </form>

                                            <div>
                                                <button type="button" class="btn btn-secondary btn-sm"
                                                    data-dismiss="modal">
                                                    Batal
                                                </button>

                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fas fa-save"></i>
                                                    Update
                                                </button>
                                            </div>

                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

@stop
