@extends('partial.main')

@section('body')
    <div class="container d-flex justify-content-center align-items-center min-vh-100 bg-light">
        <div class="row w-100" style="max-width: 900px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2); border-radius: 15px; overflow: hidden;">
            <!-- Kolom Kiri: Form Login dengan warna pink pastel -->
            <div class="col-md-6 p-4 d-flex justify-content-center align-items-center" style="background-color: #fb9393;">
                <div style="width: 100%; max-width: 400px;">
                    <h3 class="card-title text-center mb-4">{{ $judul }}</h3>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Oops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.submit') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Masukkan username Anda" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Masukkan kata sandi Anda" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" style="border-radius: 25px;">Login</button>
                    
                        {{--  <div class="text-center mt-2">
                            <a href="{{ route('password.request') }}" class="text-white">Lupa Kata Sandi?</a>
                        </div>                          --}}
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Logo dan Deskripsi Aplikasi dengan warna pink pastel -->
            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-white p-4" style="background-color: #f88585;">
                <img src="{{ asset('asset/image/LOGO.png') }}" alt="Logo SIPAGI" class="mb-3" style="width: 100px; height: auto;">
                <h4 class="text-center mb-3">Sistem Informasi Pendataan Status Gizi Balita</h4>
                <p class="text-center">SIPAGI membantu Anda memantau dan mencatat data kesehatan balita secara efisien dan terstruktur. Aplikasi ini dirancang untuk memberikan informasi yang akurat dan mudah diakses mengenai status gizi balita.</p>
            </div>
        </div>
    </div>
@endsection
