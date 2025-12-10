<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssessmentFee;

class AssessmentFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fees = [
            ['charge_description' => 'Audio Visual', 'course' => null, 'amount' => 430.50, 'order' => 1, 'is_active' => true],
            ['charge_description' => 'Energy', 'course' => null, 'amount' => 2362.50, 'order' => 2, 'is_active' => true],
            ['charge_description' => 'External Relations/Internationalization', 'course' => null, 'amount' => 341.25, 'order' => 3, 'is_active' => true],
            ['charge_description' => 'Facilities Upgrading/Modernization', 'course' => null, 'amount' => 1811.25, 'order' => 4, 'is_active' => true],
            ['charge_description' => 'Guidance', 'course' => null, 'amount' => 367.50, 'order' => 5, 'is_active' => true],
            ['charge_description' => 'Internet', 'course' => null, 'amount' => 420.00, 'order' => 6, 'is_active' => true],
            ['charge_description' => 'Library', 'course' => null, 'amount' => 1312.50, 'order' => 7, 'is_active' => true],
            ['charge_description' => 'Medical and Dental', 'course' => null, 'amount' => 378.00, 'order' => 8, 'is_active' => true],
            ['charge_description' => 'Psychological testing', 'course' => null, 'amount' => 236.25, 'order' => 9, 'is_active' => true],
            ['charge_description' => 'Red Cross/Bloodletting', 'course' => null, 'amount' => 40.00, 'order' => 10, 'is_active' => true],
            ['charge_description' => 'Registration', 'course' => null, 'amount' => 605.00, 'order' => 11, 'is_active' => true],
            ['charge_description' => 'Student -Learning Management System', 'course' => null, 'amount' => 600.00, 'order' => 12, 'is_active' => true],
            ['charge_description' => 'Student Development', 'course' => null, 'amount' => 315.00, 'order' => 13, 'is_active' => true],
            ['charge_description' => 'Student Government', 'course' => null, 'amount' => 105.00, 'order' => 14, 'is_active' => true],
            ['charge_description' => 'Student Insurance', 'course' => null, 'amount' => 220.00, 'order' => 15, 'is_active' => true],
            ['charge_description' => 'Student Publication', 'course' => null, 'amount' => 105.00, 'order' => 16, 'is_active' => true],
            ['charge_description' => 'Student Research and Community Extension', 'course' => null, 'amount' => 262.50, 'order' => 17, 'is_active' => true],
            ['charge_description' => 'Testing Material', 'course' => null, 'amount' => 460.00, 'order' => 18, 'is_active' => true],
            ['charge_description' => 'e-Book - Ref', 'course' => null, 'amount' => 500.00, 'order' => 19, 'is_active' => true],
            ['charge_description' => 'E-learning It/cs/is', 'course' => null, 'amount' => 1050.00, 'order' => 20, 'is_active' => true],
            ['charge_description' => 'Examination Booklet', 'course' => null, 'amount' => 180.00, 'order' => 21, 'is_active' => true],
            ['charge_description' => 'Quality Assurance', 'course' => null, 'amount' => 200.00, 'order' => 22, 'is_active' => true],
        ];

        foreach ($fees as $fee) {
            AssessmentFee::create($fee);
        }
    }
}
