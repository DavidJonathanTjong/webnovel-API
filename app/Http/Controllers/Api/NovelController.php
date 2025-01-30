<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NovelResource;
use Illuminate\Http\Request;
use App\Models\Novels;
use Illuminate\Support\Facades\Validator;

class NovelController extends Controller
{
    public function index(){
        $novels = Novels::get();
        if($novels -> count()>0){
            return NovelResource::collection($novels);
        }else{
            return response()->json(['messege'=> 'No record available'], 200);
        }
    }

    public function list(){

        $draw = request('draw');
        $start = request('start', 0); // Beri nilai default
        $length = request('length', 10); // Beri nilai default
        $search = request('search');
        $columns = request('columns', []); // Defaultkan ke array kosong
        $order = request('order', []); // Defaultkan ke array kosong
    
        $users = Novels::query();
    
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

        $data = $users->get()->map(function ($novel) {
            $novel->foto_sampul = url('uploads/' . $novel->foto_sampul); // Kirim URL lengkap
            return $novel;
        });

        // Periksa apakah data kosong
        // if ($data->isEmpty()) {
        //     return response()->json([
        //         'draw' => $draw,
        //         'recordsTotal' => $recordsTotal,
        //         'recordsFiltered' => $recordsFiltered,
        //         'data' => [],
        //         'message' => 'No records found' 
        //     ], 200);
        // }
    
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ], 200);
    }

    public function store(Request $request){
        //cek apakah sudah di isi
        $validator = Validator::make($request->all(),[
            'nama_novel' => 'required|string|max:255',
            // 'foto_sampul'=> 'required|string|max:255',
            // diubah bagian foto sampul agar menerima image
            'foto_sampul' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi'=> 'required',
            'rating_novel'=> 'required|numeric'
        ]); 

        if($validator -> fails()){
         return response()->json([
            'messege' => "All fields are mandetoory",
            'error' => $validator -> messages(),
         ], 422);
        }

        if($request->hasfile('foto_sampul'))
        {
            $imageName = time() . '.' . $request->foto_sampul->extension();
            $request->foto_sampul->move(public_path('uploads'), $imageName);
        }

        $novel = Novels::create([
            'nama_novel' => $request ->nama_novel,
            // 'foto_sampul'=> $request ->foto_sampul,
            'foto_sampul' => $imageName,
            'deskripsi'=> $request ->deskripsi,
            'rating_novel'=> $request ->rating_novel,
        ]);

        return response()->json([
            'messege'=> 'Novel Created Succesfully',
            'data' => new NovelResource($novel)
        ], 200);
    }

    public function show(Novels $novel){
        return new NovelResource($novel);
    }

    public function update(Request $request, Novels $novel){
        $validator = Validator::make($request->all(),[
            'nama_novel' => 'required|string|max:255',
            'foto_sampul'=> 'required|string|max:255',
            'deskripsi'=> 'required',
            'rating_novel'=> 'required|numeric'
        ]); 

        if($validator -> fails()){
         return response()->json([
            'messege' => "All fields are mandetoory",
            'error' => $validator -> messages(),
         ], 422);
        }

        $novel->update([
            'nama_novel' => $request ->nama_novel,
            'foto_sampul'=> $request ->foto_sampul,
            'deskripsi'=> $request ->deskripsi,
            'rating_novel'=> $request ->rating_novel
        ]);

        return response()->json([
            'messege'=> 'Novel Updated Succesfully',
            'data' => new NovelResource($novel)
        ], 200);
    }

    public function destroy(Novels $novel){
        $novel ->delete();

        return response()->json([
            'messege'=> 'Novel Deleted Succesfully',
        ], 200);
    }
}