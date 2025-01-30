<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get(){
        $pageLength = request('pageLength') ?? 10;
        $users = User::filtered();
        return response()->json($users->paginate($pageLength), 200);
    }

    public function update(Request $request, User $user){
        $validator = Validator::make($request->all(),[
            'name'=> 'required|string|max:255',
            'email'=> 'required|string|max:255|unique:users,email',
        ]); 

        if($validator -> fails()){
         return response()->json([
            'messege' => "All fields are mandetoory",
            'error' => $validator -> messages(),
         ], 422);
        }

        $user->update([
            'name' => $request ->name,
            'email'=> $request ->email,
        ]);

        return response()->json([
            'messege'=> 'User Updated Succesfully',
            'data' => new UserResource($user)
        ], 200);
    }

    public function destroy(User $user){
        $user ->delete();

        return response()->json([
            'messege'=> 'User Deleted Succesfully',
        ], 200);
    }

    public function list(){

        $draw = request('draw');
        $start = request('start', 0); // Beri nilai default
        $length = request('length', 10); // Beri nilai default
        $search = request('search');
        $columns = request('columns', []); // Defaultkan ke array kosong
        $order = request('order', []); // Defaultkan ke array kosong
    
        $users = User::query();
    
        $recordsTotal = $users->count('id');
    
        $recordsFiltered = 0;
        if ($search && !empty($columns)) {
            $firstColumn = true;
            foreach ($columns as $column) {
                if (isset($column['searchable']) && $column['searchable'] === 'true') {
                    if ($firstColumn) {
                        $users->where($column['data'], 'LIKE', "%{$search}%");
                        $firstColumn = false;
                    } else {
                        $users->orWhere($column['data'], 'LIKE', "%{$search}%");
                    }
                }
            }
            $recordsFiltered = $users->count('id');
        } else {
            $recordsFiltered = $recordsTotal;
        }
    
        // Pastikan $order memiliki struktur yang benar dan $columns[$order['column']] valid
        if (!empty($order) && isset($order[0]) && isset($columns[$order[0]['column']]) && $columns[$order[0]['column']]['orderable'] == 'true') {
            $users->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
        }
    
        $users->skip($start);
        $users->limit($length);

        $data = $users->get();

        // Periksa apakah data kosong
        if ($data->isEmpty()) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => [],
                'message' => 'No records found' 
            ], 200);
        }
    
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users->get()
        ], 200);
    }
    
}