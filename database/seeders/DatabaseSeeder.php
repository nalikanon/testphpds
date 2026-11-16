<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\SalesLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $user = User::firstOrCreate(
            ['username' => 'user'],
            [
                'password' => Hash::make('user123'),
                'role' => 'user',
            ]
        );

        // 2. Customers
        $customers = [
            ['customer_code' => 'CUST-001', 'customer_name' => 'บริษัท สยาม พรีเมียม เทรดดิ้ง จำกัด'],
            ['customer_code' => 'CUST-002', 'customer_name' => 'บริษัท ไทยพัฒนา โลจิสติกส์ จำกัด'],
            ['customer_code' => 'CUST-003', 'customer_name' => 'หจก. รุ่งเรือง พาณิชย์'],
            ['customer_code' => 'CUST-004', 'customer_name' => 'บริษัท กรุงเทพ ดิจิทัล โซลูชั่นส์ จำกัด'],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(['customer_code' => $c['customer_code']], $c);
        }

        // 3. Items
        $items = [
            [
                'item_code' => 'ITEM-001',
                'item_name' => 'จอมอนิเตอร์ LED 24 นิ้ว Full HD',
                'description' => 'ความละเอียด 1920x1080 75Hz พอร์ต HDMI/VGA',
                'unit' => 'เครื่อง',
                'unit_price' => 3500.00,
            ],
            [
                'item_code' => 'ITEM-002',
                'item_name' => 'คีย์บอร์ดไร้สาย Mechanical',
                'description' => 'สวิตช์ Red ไร้สาย Bluetooth & 2.4GHz',
                'unit' => 'ตัว',
                'unit_price' => 1850.00,
            ],
            [
                'item_code' => 'ITEM-003',
                'item_name' => 'เมาส์ไร้สาย Ergonomic',
                'description' => 'ออกแบบตามหลักสรีรศาสตร์ ถนอมข้อมือ ชาร์จ Type-C',
                'unit' => 'ตัว',
                'unit_price' => 790.00,
            ],
            [
                'item_code' => 'ITEM-004',
                'item_name' => 'โต๊ะทำงานปรับระดับไฟฟ้า',
                'description' => 'ท็อปไม้ขนาด 140x70 ซม. มอเตอร์คู่ บันทึกความสูงได้ 4 ระดับ',
                'unit' => 'ชุด',
                'unit_price' => 9900.00,
            ],
            [
                'item_code' => 'ITEM-005',
                'item_name' => 'เก้าอี้เพื่อสุขภาพ Ergonomic',
                'description' => 'พนักพิงตาข่ายระบายอากาศ รองรับเอว ปรับระดับที่พักแขน 3D',
                'unit' => 'ตัว',
                'unit_price' => 5400.00,
            ],
            [
                'item_code' => 'ITEM-006',
                'item_name' => 'สายเคเบิล Ultra High Speed HDMI 2.1 (2 ม.)',
                'description' => 'รองรับความละเอียด 8K@60Hz / 4K@120Hz',
                'unit' => 'เส้น',
                'unit_price' => 290.00,
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['item_code' => $item['item_code']], $item);
        }

        // 4. Sample Sales Orders
        $cust1 = Customer::where('customer_code', 'CUST-001')->first();
        $item1 = Item::where('item_code', 'ITEM-001')->first();
        $item2 = Item::where('item_code', 'ITEM-002')->first();

        if ($cust1 && $item1 && $item2) {
            $order = SalesOrder::firstOrCreate(
                ['order_no' => 'SO-202611-0001'],
                [
                    'customer_id' => $cust1->customer_id,
                    'order_date' => now()->format('Y-m-d'),
                    'delivery_date' => now()->addDays(7)->format('Y-m-d'),
                    'total_amount' => 8850.00,
                    'discount_percent' => 5.00,
                    'discount_amount' => 442.50,
                    'net_amount' => 8407.50,
                    'vat_percent' => 7.00,
                    'vat_amount' => 588.53,
                    'grand_total' => 8996.03,
                    'status' => 'confirmed',
                    'remark' => 'จัดส่งที่แผนก IT ชั้น 8',
                    'created_by' => $admin->user_id,
                ]
            );

            if ($order->lines()->count() === 0) {
                SalesLine::create([
                    'order_id' => $order->order_id,
                    'item_id' => $item1->item_id,
                    'quantity' => 2.000,
                    'unit_price' => 3500.00,
                    'discount_percent' => 0.00,
                    'discount_amount' => 0.00,
                    'line_total' => 7000.00,
                    'remark' => 'จอสำหรับโปรแกรมเมอร์',
                    'line_no' => 1,
                ]);

                SalesLine::create([
                    'order_id' => $order->order_id,
                    'item_id' => $item2->item_id,
                    'quantity' => 1.000,
                    'unit_price' => 1850.00,
                    'discount_percent' => 0.00,
                    'discount_amount' => 0.00,
                    'line_total' => 1850.00,
                    'remark' => '',
                    'line_no' => 2,
                ]);
            }
        }
    }
}
