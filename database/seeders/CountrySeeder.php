<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::insert([
            [
                "id" => 2,
                "name_ar" => "الكويت",
                "name_en" => "Kuwait",
                "flag" => "https://flagcdn.com/w320/kw.png",

            ],
            [
                "id" => 27,
                "name_ar" => "تركيا",
                "name_en" => "Turkey",
                "flag" => "https://flagcdn.com/w320/tr.png",

            ],
            [
                "id" => 31,
                "name_ar" => "الصومال",
                "name_en" => "Somalia",
                "flag" => "https://flagcdn.com/w320/so.png",

            ],
            [
                "id" => 39,
                "name_ar" => "تونس",
                "name_en" => "Tunisia",
                "flag" => "https://flagcdn.com/w320/tn.png",

            ],
            [
                "id" => 46,
                "name_ar" => "جيبوتي",
                "name_en" => "Djibouti",
                "flag" => "https://flagcdn.com/w320/dj.png",

            ],
            [
                "id" => 68,
                "name_ar" => "السودان",
                "name_en" => "Sudan",
                "flag" => "https://flagcdn.com/w320/sd.png",

            ],
            [
                "id" => 82,
                "name_ar" => "البحرين",
                "name_en" => "Bahrain",
                "flag" => "https://flagcdn.com/w320/bh.png",

            ],
            [
                "id" => 96,
                "name_ar" => "عمان",
                "name_en" => "Oman",
                "flag" => "https://flagcdn.com/w320/om.png",

            ],
            [
                "id" => 97,
                "name_ar" => "دولة الإمارات العربية المتحدة",
                "name_en" => "United Arab Emirates",
                "flag" => "https://flagcdn.com/w320/ae.png",

            ],
            [
                "id" => 103,
                "name_ar" => "سوريا",
                "name_en" => "Syria",
                "flag" => "https://flagcdn.com/w320/sy.png",

            ],
            [
                "id" => 104,
                "name_ar" => "ليبيا",
                "name_en" => "Libya",
                "flag" => "https://flagcdn.com/w320/ly.png",

            ],
            [
                "id" => 110,
                "name_ar" => "إريتريا",
                "name_en" => "Eritrea",
                "flag" => "https://flagcdn.com/w320/er.png",

            ],
            [
                "id" => 115,
                "name_ar" => "مصر",
                "name_en" => "Egypt",
                "flag" => "https://flagcdn.com/w320/eg.png",

            ],
            [
                "id" => 130,
                "name_ar" => "اليمن",
                "name_en" => "Yemen",
                "flag" => "https://flagcdn.com/w320/ye.png",

            ],
            [
                "id" => 161,
                "name_ar" => "العراق",
                "name_en" => "Iraq",
                "flag" => "https://flagcdn.com/w320/iq.png",

            ],
            [
                "id" => 168,
                "name_ar" => "السعودية",
                "name_en" => "Saudi Arabia",
                "flag" => "https://flagcdn.com/w320/sa.png",

            ],
            [
                "id" => 169,
                "name_ar" => "الأردن",
                "name_en" => "Jordan",
                "flag" => "https://flagcdn.com/w320/jo.png",

            ],
            [
                "id" => 184,
                "name_ar" => "لبنان",
                "name_en" => "Lebanon",
                "flag" => "https://flagcdn.com/w320/lb.png",

            ],
            [
                "id" => 189,
                "name_ar" => "جزر القمر",
                "name_en" => "Comoros",
                "flag" => "https://flagcdn.com/w320/km.png",

            ],
            [
                "id" => 213,
                "name_ar" => "المغرب",
                "name_en" => "Morocco",
                "flag" => "https://flagcdn.com/w320/ma.png",

            ],
            [
                "id" => 215,
                "name_ar" => "قطر",
                "name_en" => "Qatar",
                "flag" => "https://flagcdn.com/w320/qa.png",

            ],
            [
                "id" => 225,
                "name_ar" => "الجزائر",
                "name_en" => "Algeria",
                "flag" => "https://flagcdn.com/w320/dz.png",

            ],
            [
                "id" => 243,
                "name_ar" => "فلسطين",
                "name_en" => "Palestine",
                "flag" => "https://flagcdn.com/w320/ps.png",

            ],
            [
                "id" => 245,
                "name_ar" => "جنوب السودان",
                "name_en" => "South Sudan",
                "flag" => "https://flagcdn.com/w320/ss.png",

            ],
        ]);
    }
}
