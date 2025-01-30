<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailNovelResource;
use App\Models\Detailnovel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DetailNovelController extends Controller
{
    public function index(){
        $detailnovel = Detailnovel::get();
        if($detailnovel -> count()>0){
            return DetailNovelResource::collection($detailnovel);
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
    
        $users = Detailnovel::query();
    
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

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'id_novel' => 'required|integer|exists:novels,id',
            'chapter_novel' => [
            'required',
            'integer',
            Rule::unique('detailnovel')->where(function ($query) use ($request) {
                return $query->where('id_novel', $request->id_novel);
            }),
        ],
            'text_novel'=> 'required|string|max:255',
        ]); 

        if($validator -> fails()){
         return response()->json([
            'messege' => "All fields are mandetoory",
            'error' => $validator -> messages(),
         ], 422);
        }

        $novel = Detailnovel::create([
            'id_novel' => $request ->id_novel,
            'chapter_novel'=> $request ->chapter_novel,
            'text_novel'=> $request ->text_novel
        ]);

        return response()->json([
            'messege'=> 'Detail Novel Created Succesfully',
            'data' => new DetailNovelResource($novel)
        ], 200);
    }

    public function show(Detailnovel $detailnovel){
        return new DetailNovelResource($detailnovel);
    }

    public function update(Request $request, Detailnovel $detailnovel){
        // dd($request->all());
        
        $validator = Validator::make($request->all(),[
            'id_novel' => 'required|integer|exists:novels,id',
            'chapter_novel' => [
            'required',
            'integer',
            ],
            'text_novel'=> 'required|string|max:255',
        ]); 

        if($validator -> fails()){
         return response()->json([
            'messege' => "All fields are mandetoory",
            'error' => $validator -> messages(),
         ], 422);
        }

        $detailnovel->update([
            'id_novel' => $request ->id_novel,
            'chapter_novel'=> $request ->chapter_novel,
            'text_novel'=> $request ->text_novel
        ]);

        return response()->json([
            'messege'=> 'Detail Novel Updated Succesfully',
            'data' => new DetailNovelResource($detailnovel)
        ], 200);
    }
    
    public function destroy(Detailnovel $detailnovel){
        $detailnovel->delete();

        return response()->json([
            'messege'=> 'Detail Novel Deleted Succesfully',
        ], 200);
    }


}