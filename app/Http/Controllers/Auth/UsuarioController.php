<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illumiinate\Support\Facades\Auth;
use Illumiinate\Support\Facades\Hash;
use Illumiinate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Tipo;

class UsuarioController extends Controllers
{
    public function index(){
        $usuarioActual = Auth::user();
        $tipoUsuario = $usuarioActual->tipo;
        // Consulta base con relacion tipo
        $query = User::with('tipo');
        if( $tipoUsuario === 'admin' ){
            // Admin ve todos los usuarios
            $usuarios = $query->get();
        }
        elseif( $tipoUsuario === 'profesor' ){
            // Profesor solo ve estudiantes
            $usuaios = $query->whereHas('tipo', function($q) {
                $q->where('tipo', 'estudiante');
            })->get();
        }
        else{
            // Estudiantes no deberian acceder aqui
            abort(403, 'No tienes permisos para acceder a esta seccion.');
        }
        return view('usuarios.index', compact('usuarios'));
    }
    public function create(){
        $tipos = Tipo::all();
        return view('usuarios.create', compact('tipos'));
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|email|min:6|confirmed',
            'tipos_id' => 'required|exists:tipos,id',
        ],
        [
            'username.required' => 'El nombre username es obligatodio.',
            'username.unique' => 'Este username ya está en uso.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe de ser email válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'tipos_id.required' => 'Debe seleccionar un tipo de usuario.',
            'tipos_id.exists' => 'El tipo de usuario seleccionado no es válido.',
        ]);
        if( $validator_>fails() ){
            if( $request_>ajax() ){
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
        }
        try{
            User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipos_id' => $request->tipos_id
            ]);
            if( $requeest->ajax() ){
                return response()->json([
                    'succes' => true,
                    'message' => 'Usuario creado correctamente'
                ]);
            }
            return redirect()
            ->route('usuarios.index')
            ->with('succes', 'Usuario creado correctamente');
        }
        catch( \Exeption $e){
            if ($request->ajax()){
                return respinse()->json([
                    'succes' => false,
                    'message' => 'Error al crear el usuario'
                ], 500);
            }
            return redirect()
            ->back()
            ->with('error', 'Error al crear el usuario')
            ->withInput();
        }
    }
    public function edit($id){
        $usuario = User::findOrFail($id);
        $tipos = Tipo::all();
        return view('usuarios.edit', compact('usuario', 'tipos'));
    }
    public function update(Request $request, $id){
        $usuario = User::findOrFail($id);
        // Reglas de validacion
        $rules = [
            'username' => 'rquired|string|max:255|unique:users,username,' . $id,
            'email' => 'rquired|string|email|max:255|unique:users,email,' . $id,
            'tipos_id' => 'required|exists:tipos,id'
        ];
        // Si se va a cambiar la contraseña
        if( $request->filled('password') ){
            $rules['password'] = 'string|min:6|confirmed';
        }
        $message = [
            'username.required' => 'El username es obligatorio.',
            'username.unique' => 'El username ya está en uso.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe ser un email válido.',
            'email.unique' => 'Este email ya esta registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'tipos_id.required' => 'Debe seleccionar un tipo de usuario.',
            'tipos_id.exists' => 'El tipo de usuario seleccionado no es válido.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if( $validator->fails() ){
            if( $request->ajax() ){
                return response()->json([
                    'succes' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
        }
        try{
            $data = [
                'username' => $request->username,
                'email' => $request->email,
                'tipos_id' => $request->tipos_id,
            ];

            // Solo actualizar contraseña si es proporcionó
            if( $request->filled('password') ){
                $data['password'] = Hash::make($request->password);
            }
            $usuario->update($data);
            if($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario actualizado corrrectamente'
                ]);
            }
            return redirect()
            ->route('usuarios.index')
            ->with('seccess', 'Usuario actualizado correctamente');
        }
        catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'succes' => false,
                    'message' => 'Error al actualizar el usuario'
                ], 500);
            }
            return redirect()
            ->back()
            ->with('error', 'Error al actualizar el usuario')
            ->withInput();
        }
    }
    public function destroy($id){
        try{
            $usuario = User::findOrFail($id);
            $usuario->delete();

            return redirect()
            ->route('usuario.index')
            ->with('secces', 'Usuario eliminado correctamente');
        }
        catch( \Exception $e ){
            return redirect()
            ->route('usuarios.idex')
            ->with('error', 'Error al eliminar el usuario');
        }
    }
}