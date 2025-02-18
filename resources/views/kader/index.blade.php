@extends('partial.main')

@section('body')
    <div class="content mt-0">
        <div class="container">
            <h1 class="text-center">Profil Balita</h1>
            <div class="d-flex justify-content-between align-items-center mb-3">
                @if(auth()->user()->hasRole(['Admin', 'Kader']))
                    <a class="btn btn-primary" href="{{ route('profiles.create') }}">Tambah Profil</a>
                @endif
                <form action="{{ route('profiles.index') }}" method="GET" class="mb-0" style="width: 300px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari...." value="{{ request()->query('search') }}">
                </form>
            </div>

            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Anak</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Asal Desa</th>
                                    <th>Anak Ke</th>
                                    <th>Status ASI</th>
                                    <th>Status Imunisasi</th>
                                    <th>BB Lahir</th>
                                    <th>TB Lahir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($profiles as $profile)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $profile->nama_anak }}</td>
                                    <td>{{ $profile->jenis_kelamin }}</td>
                                    <td>{{ $profile->tanggal_lahir }}</td>
                                    <td>{{ $profile->alamat }}</td>
                                    <td>{{ $profile->anak_ke }}</td>
                                    <td>{{ $profile->status_asi }}</td>
                                    <td>{{ $profile->status_imunisasi }}</td>
                                    <td>{{ number_format($profile->bb_lahir, 2) }}</td>
                                    <td>{{ number_format($profile->tb_lahir, 2) }}</td>
                                    <td>
                                        <a class="fa-solid fa-eye me-2" style="color: #63E6BE;" href="{{ route('profiles.show', $profile->id) }}"></a>
                                        
                                        @if(auth()->user()->hasRole(['Admin', 'Kader']))
                                            <a class="fa-solid fa-pen-to-square me-2" style="color: #74C0FC;" href="{{ route('profiles.edit', $profile->id) }}"></a>
                                            
                                            <form action="{{ route('profiles.destroy', $profile->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="fa-solid fa-trash" style="border: none; background: none; color: #fb2828; cursor: pointer;" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus profil ini?')"></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Tampilkan Pagination --}}
                    <div class="d-flex justify-content-center">
                        {{ $profiles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM content loaded');
            @if (session('success'))
                console.log('Success message found');
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
            @endif
        });
</script>
@endsection
