<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataTables $dataTables)
    {
        if ($request->ajax()) {
            
            $query = User::with('roles');
            
            try {
                $response = $dataTables->eloquent($query)
                    ->addIndexColumn()
                    ->addColumn('roles', function (User $user) {
                        $badges = '';
                        foreach ($user->roles as $role) {
                            $badges .= '<span class="badge bg-primary me-1">' . $role->name . '</span>';
                        }
                        return $badges;
                    })
                    ->addColumn('action', function(User $user){
                        $btn = '<a href="'.route('users.show', $user->id).'" class="btn btn-primary btn-sm me-1"><i class="bi bi-eye"></i></a>';
                        $btn .= '<a href="'.route('users.edit', $user->id).'" class="btn btn-info btn-sm me-1"><i class="bi bi-pencil"></i></a>';
                       $btn .= '<a href="'.route('users.destroy', $user->id).'" class="btn btn-danger btn-sm btn-delete" data-id="'.$user->id.'">
                                    <i class="bi bi-trash"></i>
                                </a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'roles'])
                    ->make(true);
                    
                return $response;
                
            } catch (\Exception $e) {
                
                return response()->json([
                    'error' => $e->getMessage(),
                    'draw' => $request->input('draw', 0),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }
        }

        $roles = \Spatie\Permission\Models\Role::all();
        
        return view('users.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       //
    }

    /**
     * Display the specified resource.
     */
     public function show(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        $userRole = $user->roles->first()->name ?? '';
        
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 422);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}