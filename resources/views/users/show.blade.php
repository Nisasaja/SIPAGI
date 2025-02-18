@extends('lpartial.main')

@section('body')
<div class="container">
    <h1>Detail Pengguna</h1>

    <table class="table table-bordered">
        <tr>
            <th>Username</th>
            <td>{{ $user->username }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Role</th>
            <td>{{ $user->role }}</td>
        </tr>
    </table>

    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
