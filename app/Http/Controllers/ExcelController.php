<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Rap2hpoutre\FastExcel\FastExcel;
  use Illuminate\Support\Facades\DB;
class ExcelController extends Controller
{
    public function index()
{
   $clients = Client::paginate(5); // 10 per page
    return view('excel', ['clients' => $clients]);
}


   public function import(Request $request){
    $request->validate(['test' => 'required|file|mimes:xlsx,csv']);

    (new FastExcel)->import($request->file('test')->getRealPath(), function($line) {
        Client::firstOrCreate([
            'name' => $line['Name'] ?? $line['name'] ?? null,
            'lastname' => $line['Lastname'] ?? $line['lastname'] ?? null,
            'age' => $line['Age'] ?? $line['age'] ?? null
        ]);
    });

    return redirect('excel')->with('success clients imported!');
}

    public function export(){
        return (new FastExcel(Client::all()))->download('test.xlsx', function ($client){
            return [
                'id' => $client->id,
                'name' => $client->name,
                'lastname' => $client->lastname,
                'age' => $client->age
            ];
        });
    }
}