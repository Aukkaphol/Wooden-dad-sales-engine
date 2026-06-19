<?php

namespace Database\Seeders;

use App\Models\FurnitureSetCategory;
use App\Models\WebsiteSection;
use Illuminate\Database\Seeder;

class WebsiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'title' => 'Wooden Dad Design',
                'subtitle' => 'เฟอร์นิเจอร์ไม้สนสั่งทำสำหรับบ้านอบอุ่น',
                'description' => 'เลือกเซ็ตเฟอร์นิเจอร์ที่เข้ากับพื้นที่จริงของบ้านคุณ ทีมงานช่วยประเมินขนาด งบประมาณ และออกใบเสนอราคาก่อนเริ่มผลิต',
                'button_text' => 'ขอประเมินราคา',
                'button_url' => '/lead',
                'sort_order' => 1,
                'active' => true,
            ],
            'workflow' => [
                'title' => 'ขั้นตอนสั่งทำที่ชัดเจน',
                'subtitle' => 'จากไอเดียแรกจนถึงวันติดตั้ง',
                'description' => 'เลือกเซ็ต ส่งขนาดพื้นที่ ประเมินราคา ออกใบเสนอราคา ผลิต และส่งมอบ/ติดตั้งโดยทีม Wooden Dad Design',
                'sort_order' => 2,
                'active' => true,
            ],
            'trust' => [
                'title' => 'งานไม้สนที่วัดพื้นที่จริงก่อนผลิต',
                'subtitle' => 'เรียบง่าย แข็งแรง และดูแลได้ในระยะยาว',
                'description' => 'เราออกแบบให้เข้ากับการใช้งานจริง เลือกโทนไม้ธรรมชาติ และสื่อสารรายละเอียดงานก่อนผลิตทุกครั้ง',
                'sort_order' => 3,
                'active' => true,
            ],
            'final_cta' => [
                'title' => 'เริ่มวางแผนเฟอร์นิเจอร์ไม้สนของคุณ',
                'subtitle' => 'ส่งขนาดพื้นที่และงบประมาณให้ทีมช่วยประเมิน',
                'description' => 'รับคำแนะนำเบื้องต้นฟรี พร้อมแนวทางเซ็ตเฟอร์นิเจอร์ที่เหมาะกับบ้านของคุณ',
                'button_text' => 'ขอประเมินราคา',
                'button_url' => '/lead',
                'sort_order' => 4,
                'active' => true,
            ],
        ];

        foreach ($sections as $key => $section) {
            WebsiteSection::query()->updateOrCreate(
                ['section_key' => $key],
                $section + ['section_key' => $key]
            );
        }

        $categories = [
            [
                'name' => 'ชุดห้องนอน',
                'slug' => 'bedroom-set',
                'short_description' => 'เตียง ตู้ โต๊ะหัวเตียง และพื้นที่เก็บของในโทนไม้สนอบอุ่น',
                'full_description' => 'เซ็ตห้องนอนสั่งทำตามขนาดพื้นที่จริง เหมาะกับบ้านและคอนโดที่ต้องการความเรียบง่ายใช้งานได้ทุกวัน',
                'start_price' => 32000,
                'sort_order' => 1,
                'active' => true,
            ],
            [
                'name' => 'ชุดห้องนั่งเล่น',
                'slug' => 'living-room-set',
                'short_description' => 'ชั้นวางทีวี โต๊ะกลาง และชั้นเก็บของไม้สนสำหรับพื้นที่พักผ่อน',
                'full_description' => 'ออกแบบห้องนั่งเล่นให้เป็นมุมพักผ่อนที่อบอุ่นและเป็นระเบียบด้วยเฟอร์นิเจอร์ไม้สนโทนธรรมชาติ',
                'start_price' => 28000,
                'sort_order' => 2,
                'active' => true,
            ],
            [
                'name' => 'ชุดห้องอาหาร',
                'slug' => 'dining-set',
                'short_description' => 'โต๊ะอาหาร ม้านั่ง และตู้เก็บของที่เข้ากับครอบครัว',
                'full_description' => 'เซ็ตห้องอาหารสำหรับบ้านที่ต้องการพื้นที่ใช้งานจริง แข็งแรง และดูอบอุ่นทุกมื้ออาหาร',
                'start_price' => 24000,
                'sort_order' => 3,
                'active' => true,
            ],
            [
                'name' => 'ชุดห้องทำงาน',
                'slug' => 'working-room-set',
                'short_description' => 'โต๊ะทำงาน ชั้นเอกสาร และชั้นวางของสำหรับมุมทำงานที่สงบ',
                'full_description' => 'เฟอร์นิเจอร์ไม้สนสำหรับโฮมออฟฟิศหรือมุมทำงานในบ้าน ออกแบบตามพื้นที่และอุปกรณ์ที่ใช้จริง',
                'start_price' => 22000,
                'sort_order' => 4,
                'active' => true,
            ],
        ];

        foreach ($categories as $category) {
            FurnitureSetCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
