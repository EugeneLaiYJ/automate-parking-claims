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
    public function downloadClaim(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
        ]);
        $month = (int) $validated['month'];

        $filePath = base_path('resources/js/lib/PFE CLAIM FORM TEMPLATE.xlsx');
        $spreadsheet = IOFactory::load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();
        $transactions = Transaction::query()
            ->orderBy('transaction_id')
            ->get();

        $row = SPREADSHEET_FIRST_ROW;
        $currentDate = now()->format('Y-m-d');

        $worksheet->setCellValue('B5',"NAME: Lai Yong Jun");
        $worksheet->setCellValue('D5',"DATE: " . $currentDate);

        foreach ($transactions as $transaction) {
            $transactionDate = \Carbon\Carbon::parse($transaction->date_time);
            if ((int) $transactionDate->format('n') !== $month) {
                continue;
            }
            if ($transaction->sector !== 'PARKING' || !str_contains($transaction->entry_location, 'EMHUB')) {
                continue;
            }
            if ($row>14+SPREADSHEET_FIRST_ROW){
                $worksheet->insertNewRowBefore($row);
                $worksheet->setCellValue(SPREADSHEET_NUMBER_COLUMN . $row, "=" . SPREADSHEET_NUMBER_COLUMN . $row-1 . "+1");
                $worksheet->setCellValue(SPREADSHEET_NUMBER_COLUMN . $row+1, "=" . SPREADSHEET_NUMBER_COLUMN . $row . "+1");
                $worksheet->setCellValue(SPREADSHEET_NUMBER_COLUMN . $row+2, "=" . SPREADSHEET_NUMBER_COLUMN . $row+1 . "+1");
                $worksheet->setCellValue(SPREADSHEET_AMOUNT_COLUMN . $row+2, "=SUM(D8:".SPREADSHEET_AMOUNT_COLUMN. $row+1 .")");
            }
            $worksheet->setCellValue(SPREADSHEET_DESCRIPTION_COLUMN . $row, $transaction->sector);
            $worksheet->setCellValue(SPREADSHEET_DATE_COLUMN . $row, $transactionDate->format('Y-m-d'));
            $worksheet->setCellValue(SPREADSHEET_AMOUNT_COLUMN . $row, $transaction->amount);
            $row++;
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
            ->download($downloadPath, 'PFE CLAIM FORM ' . $currentDate . '.xlsx')
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
