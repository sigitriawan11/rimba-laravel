<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Helpers\MyHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected $rules = [
        "kode_item" => 'required|unique:items',
        "nama_item" => 'required',
        "unit" => "required",
        "stok" => "required|numeric",
        "harga_satuan" => "required",
        "barang" => "required|image"
    ];

    protected function successMessage($status, $data = "", $message = ""){
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ]);
    }

    protected function errorMessage($status, $message = ""){
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Item::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);

        if($validator->fails()){
            return $this->errorMessage(false, $validator->errors());
        }

        $validated = $validator->validated();

        $file = $request->file('barang')->store('barang', 'public');

        $validated['barang'] = $file;

        $data = Item::create($validated);

        return $this->successMessage(true, $data, 'Berhasil menambah Item');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($kode_item)
    {
        $data = Item::where('kode_item', $kode_item)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        return $this->successMessage(true, $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $kode_item)
    {
        $data = Item::where('kode_item', $kode_item)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        $rules = $this->rules;
        $rules['kode_item'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->errorMessage(false, $validator->errors());
        }

        $validated = $validator->validated();

        $update = $data->update($validated);

        return $this->successMessage(true, $update, 'Berhasil merubah data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($kode_item)
    {
        $data = Item::where('kode_item', $kode_item)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        Item::destroy($data['id']);

        return $this->successMessage(true, "", "Berhasil menghapus data");
    }
}
