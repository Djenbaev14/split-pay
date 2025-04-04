<?php

namespace App\Http\Controllers;

use App\Exports\ContractsExport;
use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContractExportController extends Controller
{
    public function export($client_id)
    {
        $client = Client::findOrFail($client_id);
        $file=$client->first_name.'-'.$client->last_name.'-'.$client->patronymic.'.xlsx';
        $contracts = $client->contracts;
        return Excel::download(new ContractsExport($contracts), $file);
    }
}
