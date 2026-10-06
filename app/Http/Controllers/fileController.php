<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Transaction;
use RuntimeException;
use Throwable;

const TRANSACTION_NUMBER = 1;
const TRANSACTION_DATE = 2;
const TRANSACTION_TYPE = 4;
const TRANSACTION_SECTOR = 5;
const TRANSACTION_ENTRY = 6;
const TRANSACTION_EXIT = 8;
const TRANSACTION_AMOUNT = 11;

const SPREADSHEET_NAME = "B5";
const SPREADSHEET_DATE = "D5";

const SPREADSHEET_NUMBER_COLUMN = "A";
const SPREADSHEET_DATE_COLUMN = "B";
const SPREADSHEET_DESCRIPTION_COLUMN = "C";
const SPREADSHEET_AMOUNT_COLUMN = "D";
const SPREADSHEET_FIRST_ROW = 8;

class fileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Welcome', [
            'transactions' => Transaction::query()
                ->orderBy('transaction_id')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function downloadClaim()
    {
        $filePath = base_path('resources/js/lib/PFE CLAIM FORM TEMPLATE.xlsx');
        $spreadsheet = IOFactory::load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $transactions = Transaction::query()
            ->orderBy('transaction_id')
            ->get();

        foreach ($transactions as $index => $transaction) {
            $row = SPREADSHEET_FIRST_ROW + $index;
            if($transaction->sector == "PARKING"){
                $worksheet->setCellValue(SPREADSHEET_DESCRIPTION_COLUMN.$row, $transaction->sector);
            }else{
                break;
            }
            $transactionDate=$transaction->date_time;
            $formatDate=strstr($transactionDate," ", true);
            $worksheet->setCellValue(SPREADSHEET_DATE_COLUMN.$row, $formatDate);
            $worksheet->setCellValue(SPREADSHEET_AMOUNT_COLUMN.$row, $transaction->amount);
        }

        $downloadPath = tempnam(sys_get_temp_dir(), 'claim_');

        if ($downloadPath === false) {
            throw new RuntimeException('Unable to create a temporary spreadsheet file.');
        }

        try {
            (new Xlsx($spreadsheet))->save($downloadPath);
        } catch (Throwable $exception) {
            unlink($downloadPath);
            throw $exception;
        }

        return response()
            ->download($downloadPath, 'PFE CLAIM FORM TEMPLATE.xlsx')
            ->deleteFileAfterSend(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx'],
        ]);

        $rows = IOFactory::load($request->file('file')->getRealPath())
            ->getActiveSheet()
            ->toArray();

        for($i=1; $i < count($rows); $i++){
            Transaction::firstOrCreate([
                'transaction_id' => $rows[$i][TRANSACTION_NUMBER],
                'date_time' => $rows[$i][TRANSACTION_DATE],
                'type' => $rows[$i][TRANSACTION_TYPE],
                'sector' => $rows[$i][TRANSACTION_SECTOR],
                'entry_location' => $rows[$i][TRANSACTION_ENTRY],
                'exit_location' => $rows[$i][TRANSACTION_EXIT],
                'amount' => $rows[$i][TRANSACTION_AMOUNT],
            ]);
        }

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
