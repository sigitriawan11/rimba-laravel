<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Customer;
use App\Helpers\MyHelpers;
use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    protected $rules = [
        "customer_id" => "required",
        "item_id" => "required",
        "qty" => "required|numeric",
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
        $data = Sales::latest()->get();

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

        $item = Item::where('id', $validated['item_id'])->first();

        $harga = $item['harga_satuan'] * $validated['qty'];

        $customer = Customer::where('id', $validated['customer_id'])->first();
        if($customer['diskon_tipe'] == "persen"){
            $diskon = $harga * ( $customer['diskon'] / 100 );
            $diskon = $harga - $diskon;
            $validated['total_diskon'] = $harga - $diskon;
        } else {
            $diskon = $harga - $customer['diskon'];
            $validated['total_diskon'] = $harga - $diskon;
        }

        $validated['total_harga'] = $harga;
        $validated['total_bayar'] = $harga - $validated['total_diskon'];

        $validated['code_transaksi'] = 'TRX000' . Str::random(5);
        $validated['tanggal_transaksi'] = now();


        $data = Sales::create($validated);

        return $this->successMessage(true, $data, 'Berhasil menambah Item');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code_transaksi)
    {
        $data = Sales::where('code_transaksi', $code_transaksi)->first();

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
    public function update(Request $request, $code_transaksi)
    {
        $data = Sales::where('code_transaksi', $code_transaksi)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        $rules = $this->rules;
        $rules['code_transaksi'] = 'required';

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
    public function destroy($code_transaksi)
    {
        $data = Sales::where('code_transaksi', $code_transaksi)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        Sales::destroy($data['id']);

        return $this->successMessage(true, "", "Berhasil menghapus data");
    }
}
