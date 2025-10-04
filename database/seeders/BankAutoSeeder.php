<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BankAuto;

class BankAutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAutos = [
            [
                'name' => 'MBBank Official',
                'code' => 'MBB',
                'account_number' => '1234567890',
                'account_name' => 'NGUYEN VAN A',
                'status' => true,
            ],
            [
                'name' => 'Vietcombank',
                'code' => 'VCB',
                'account_number' => '0987654321',
                'account_name' => 'TRAN THI B',
                'status' => true,
            ],
            [
                'name' => 'Techcombank',
                'code' => 'TCB',
                'account_number' => '1122334455',
                'account_name' => 'LE VAN C',
                'status' => true,
            ],
            [
                'name' => 'BIDV',
                'code' => 'BIDV',
                'account_number' => '5566778899',
                'account_name' => 'PHAM THI D',
                'status' => true,
            ],
            [
                'name' => 'Agribank',
                'code' => 'AGB',
                'account_number' => '9988776655',
                'account_name' => 'HOANG VAN E',
                'status' => true,
            ],
        ];

        foreach ($bankAutos as $bankData) {
            BankAuto::updateOrCreate(
                ['code' => $bankData['code']],
                $bankData
            );
        }

        $this->command->info('Bank autos seeded successfully!');
    }
}