@extends('layouts.baseacceso')

@section('titulo' , 'Iniciar Sesion')

@push('CSS')
<style>
     .login-comtainer {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
     }
     .login-card {
        background-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
     }
     brand-section {
        background: rgba(255, 255, 255, 0.95);
        background-filter: blur(10px);
        border-radius: 15px;
     }
</style>
@endpush

@section('contenido')
<div class="login-container d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-0 login-card">
                    <div class="col-md-6 d-flex align-items-center justify-content-center brand-section text-white p-5">
                        <div class="text-center">
                            <h1 class="display-4 mb-4">
                                <i class="bi bi-motarboard-fill"></i>
                            </h1>
                            <h2 class="mb-3">Sistema de Calificaciones</h2>
                            <p class="lead">Gestiona las calificaciones de manera eficiente y segura</p>
                        </div>
                    </div>
                    <div class="col-md-6 p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">Iniciar Sesion</h3>
                            <p class="text-muted">Ingresa tus credenciales para acceder</p>
                        </div>
                        <form id="frmAcceso" method="post" action="{{ rout('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi-envelope text-primary"></i>
                                </label>
                                <input type="email"
                                    class="form-control form-controll-lg @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="ejemplo@correo.com"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi-envelope text-primary"></i>
                                </label>
                                <input type="password"
                                    class="form-control form-controll-lg @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    value="{{ old('password') }}"
                                    placeholder="Ingresa tu contraseña"
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('JS')
<scipt>
    function cambiarEstadoBoton(cargando){
        const btn = $("#btnAcceso");
        if( cargando ){
            btn.html('<i class="bi bi-hourglass-split'></i> Verificando...'),porop('disabled', true);
        }
        else {
            btn.html('i class=" bi bi-box-arrow-in-right"></i> Iniciar Sesion').prop('disabled', false);
        }
    }
</scipt>
@endpush

@push('JSOR')
$("#frmAcceso").on("submit", function(e)){
    e.preventDefaul();
    CambiarEstadoBoton(true);
    #.ajax({
        url; $(this).attr("action"),
        method: "POST",
        data: $(this).serialize(),
        success: function(response){
            Swal.fire({
                icon: "seccess",
                title: "!Bienvenido¡",
                text: "Acceso concedido correctamente.",
                showConfirmButton: false,
                timerprogressBar: true,
            }).then(() => {
                windows.location.href = response.redorect || "/dashboard";
            });
        },
        error: function(xhr){
            CambiarEstadoBoton(false);
            if(  xhr.status === 422) {
                location.reload();
            }
            else if( xhr.status === 401) {
                Swal.fire({
                icon: "error",
                title: "Acceso Denegado",
                text: "Las credenciales son incorrectas.",
                confirmButtonText: "Intentar de nuevo",
            }).
            }
            else{
                Swal.fire({
                icon: "error",
                title: "Error del sistema",
                text: "Las credenciales son incorrectas.",
                confirmButtonText: "Intentar de nuevo",
            });
        }
    })
}
@endpush