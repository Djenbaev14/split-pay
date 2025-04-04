<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ContractsExport implements FromCollection,WithHeadings,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    
    */
    
    protected $contracts;
    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    public function collection()
    {
        $data=$this->contracts->map(fn ($contract) => [
            'Kompaniya' => $contract->branch->name,
            'Kompaniya nomi' => $contract->branch->company_name,
            'Shartnoma raqami' => $contract->id,
            'FISH' => $contract->client->first_name.' '.$contract->client->last_name.' '.$contract->client->patronymic,
            'Passport' => $contract->client->passport_series.$contract->client->passport_number,
            'PINFL' => $contract->client->pinfl,
            'Viloyat' => $contract->contractDetail->region->name,
            'Tuman' => $contract->contractDetail->district->name,
            'Yashash Manzili' => $contract->contractDetail->address,
            'MFY Manzili' => $contract->contractDetail->mfy_address,
            'Ish joyi' => $contract->contractDetail->workplace,
            'Telefonlar' => collect($contract->contractDetail->phones)->pluck('phone')->implode(', '),
            'Miqdor' => number_format($contract->amount,2,'.',' '),  // Miqdor ustuni qo'shildi
            "Dastlabki to'lov" =>number_format($contract->down_payment,2,'.',' '),  
            "Davr" => $contract->period_month,  
            "To'lov kuni" => $contract->payment_day,  
            "Muddati o'tgan qarzlar" => 0,  
            "Asosiy qarzdorlik" => number_format($contract->paymentSchedule->sum('principal_amount'),2,'.',' '), 
            "Foiz bo'yicha qarzdorlik" =>number_format($contract->paymentSchedule->sum('interest_amount'),2,'.',' '),
            'Status' => $contract->status->name,
            'Sana' => $contract->created_at->format('Y-m-d h:i:s'),
            'Tariff' => $contract->tariff->name.' '.($contract->tariff->percentage .'%'),
            'Mahsulotlarga izohlar' => $contract->product,
            'Sharh' => $contract->comment,
            'Bekor qilish sababi' =>null,
            'Xodim' =>$contract->customer->name,
            "Masu'l" =>$contract->customer->name,
            "Faollashtirilgan" =>$contract->updated_at,

        ]);
        
        $totalAmount = $this->contracts->sum('amount');  // Miqdorlar yig'indisini olish
        $totalDown = $this->contracts->sum('down_payment');  // Miqdorlar yig'indisini olish
        $totalPrincipal=$this->contracts->sum(function ($contract) {
            return $contract->paymentSchedule->sum('principal_amount');
        });
        $totalInterest=$this->contracts->sum(function ($contract) {
            return $contract->paymentSchedule->sum('interest_amount');
        });
        $data->push([
            'Kompaniya' => 'Umumiy',
            'Kompaniya nomi' => '',
            'Shartnoma raqami' =>'',
            'FISH' => '',
            'Passport' => '',
            'PINFL' => '',
            'Viloyat' => '',
            'Tuman' => '',
            'Yashash Manzili' => '',
            'MFY Manzili' => '',
            'Ish joyi' => '',
            'Telefonlar' => '',
            'Miqdor' => number_format($totalAmount,2,'.',' '),  // Miqdor ustuni qo'shildi
            "Dastlabki to'lov" => number_format($totalDown,2,'.',' ') ,
            "Davr" => '',  
            "To'lov kuni" => '',  
            "Muddati o'tgan qarzlar" => '',  
            "Asosiy qarzdorlik" => number_format($totalPrincipal,2,'.',' ')   ,
            "Foiz bo'yicha qarzdorlik" => number_format($totalInterest,2,'.',' ')   ,
            'Status' => '',
            'Sana' => '',
            'Tariff' => '',
            'Mahsulotlarga izohlar' => '',
            'Sharh' => '',
            'Bekor qilish sababi' =>'',
            'Xodim' =>'',
            "Masu'l" =>'',
            "Faollashtirilgan" =>'',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            
            'Kompaniya' ,
            'Kompaniya nomi' ,
            'Shartnoma raqami',
            'FISH' ,
            'Passport' ,
            'PINFL' ,
            'Viloyat' ,
            'Tuman' ,
            'Yashash Manzili' ,
            'MFY Manzili' ,
            'Ish joyi' ,
            'Telefonlar' ,
            'Miqdor' , 
            "Dastlabki to'lov" ,  
            "Davr" ,  
            "To'lov kuni" ,  
            "Muddati o'tgan qarzlar" ,  
            "Asosiy qarzdorlik",  
            "Foiz bo'yicha qarzdorlik" ,  
            'Status' ,
            'Sana' ,
            'Tariff' ,
            'Mahsulotlarga izohlar' ,
            'Sharh' ,
            'Bekor qilish sababi',
            'Xodim',
            "Masu'l",
            "Faollashtirilgan" 
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:AC1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFFF00'], // Sariq rang
            ],
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Oxirgi qator (jami) uslublari
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:AC{$lastRow}")->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FFFF00'], // Sariq rang
            ],
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Barcha hujayralarga umumiy uslublar
        $sheet->getStyle("A2:AC{$lastRow}")->applyFromArray([
            'font' => [
                'size' => 11,
                'name' => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        
        // Ustun kengliklarini qo‘lda oshirish
        $columnWidths = [
            'A' => 20,  // Kompaniya
            'B' => 25,  // Kompaniya nomi
            'C' => 15,  // Shartnoma raqami
            'D' => 30,  // FISH
            'E' => 15,  // Passport
            'F' => 15,  // PINFL
            'G' => 20,  // Viloyat
            'H' => 20,  // Tuman
            'I' => 30,  // Yashash Manzili
            'J' => 30,  // MFY Manzili
            'K' => 20,  // Ish joyi
            'L' => 25,  // Telefonlar
            'M' => 15,  // Miqdor
            'N' => 15,  // Dastlabki to'lov
            'O' => 10,  // Davr
            'P' => 10,  // To'lov kuni
            'Q' => 15,  // Muddati o'tgan qarzlar
            'R' => 15,  // Asosiy qarzdorlik
            'S' => 15,  // Foiz bo'yicha qarzdorlik
            'T' => 25,  // Status
            'U' => 20,  // Sana
            'V' => 30,  // Tariff
            'W' => 25,  // Mahsulotlarga izohlar
            'X' => 25,  // Sharh
            'Y' => 20,  // Bekor qilish sababi
            'Z' => 20,  // Xodim
            'AA' => 20, // Masu'l
            'AB' => 20, // Faollashtirilgan
        ];

        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Qator balandligini sozlash
        $sheet->getRowDimension(1)->setRowHeight(30);
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(25);
        }

        return [];
    }
}
