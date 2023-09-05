<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function get(Request $request)
    {
        $search = $request->input('search.value');
        $query = User::with('roles')->select(['id', 'name', 'email']);
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%')
                  ->orWhere('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%');
            });
        }
        //$request->input('start') / $request->input('length') + 1
        $users = $query->paginate($request->input('length'), ['*'], 'page', 1);
        $count = $users->total();
        $data=$users->items();
        for ($i=0;$i<count($data);$i++){
            $data[$i]['edit']='<a href="'.route('users.edit', ['id'=>$data[$i]['id']]).'"><i class="mdi mdi-pencil"></i></a>';
        }
        return response()->json(['data'=>$data,'recordsTotal' => $count,'recordsFiltered' => $count]);
    }
    public function index()
    {
        return view('users.index');
    }
    public function create()
    {
       
        return view('users.form',['title' =>'Crear Usuario']);
    }
    public function profile()
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        $rol = [];
        foreach ($user->roles as $role) {
            $rol[] = $role->name;
        }
        return view('users.form',['title' =>'Mi Perfil','data'=>$user,'rol'=>$rol]);
    }
    public function edit($id)
    {
        $user = User::find($id);
        $rol = [];
        foreach ($user->roles as $role) {
            $rol[] = $role->name;
        }
        return view('users.form',['title' =>'Modificar Usuario','data'=>$user,'rol'=>$rol]);
    }
    public function update($id, Request $request)
    {
        try {
            $user = User::findOrFail($id); 
            $rules=[
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ];
            if ($request->filled('status')) {
                $rules['status'] =  'required|integer';
            }
            if ($request->input('email') !== $user->email) {
                $rules['password'] = 'required|string|min:6';
            }
           
            if ($request->filled('password')) {
                $rules['email'] =  'required|string|email|max:255';
            }
            $validatedData = $request->validate($rules);
        
            if ($request->filled('password')) {
                $validatedData['password'] = bcrypt($request->input('password'));
            }
            User::where('id', $id)->update($validatedData);
            if ($request->filled('rol')) {
                $user->syncRoles([$request->input('rol')]);
            }
            return response()->json(['status' => true, 'msj' => 'Modificado']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msj' => $e->getMessage()]);
        }
    }
    
    
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|max:255',
                'status' => 'required|integer',
            ]);
    
            $register_user = array(
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'status' => $validatedData['status'],
            );
    
            if ($d_user = User::create($register_user)->assignRole($request['rol'])) {
                return response()->json(['status'=>true]);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>false, 'msj'=>$e->getMessage()]);
        }
    }
}
