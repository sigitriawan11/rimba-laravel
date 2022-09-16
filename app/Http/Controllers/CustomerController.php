<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    protected $rules = [
        "nama" => 'required',
        "contact" => "required|numeric|unique:customers",
        "email" => "required|email|unique:customers",
        "alamat" => "required",
        "diskon" => "required",
        "diskon_tipe" => "required",
        "ktp" => "required"
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
        $data = Customer::latest()->get();

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

        $file = $request->file('ktp')->store('ktp', 'public');

        $validated['ktp'] = $file;

        $data = Customer::create($validated);

        return $this->successMessage(true, $data, 'Berhasil menambah Customer');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($email)
    {
        $data = Customer::where('email', $email)->first();

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
    public function update(Request $request, $email)
    {
        $data = Customer::where('email', $email)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        $rules = $this->rules;
        $rules['contact'] = $request->contact == $data['contact'] ? 'required|numeric'
                            : 'required|numeric|unique:customers';
        $rules['email'] = $request->email == $data['email'] ? 'required|email'
                            : 'required|email|unique:customers';

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
    public function destroy($email)
    {
        $data = Customer::where('email', $email)->first();

        if(!$data){
            return $this->errorMessage(false, 'Data tidak ditemukan');
        }

        Customer::destroy($data['id']);

        return $this->successMessage(true, "", "Berhasil menghapus data");
    }
}
