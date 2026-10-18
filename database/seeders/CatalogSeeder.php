<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Chassis;
use Illuminate\Database\Seeder;

/** Base catalog structure: part sections, car brands, models and chassis codes. */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['موتور و قطعات موتوری', 'fa-gears'],
            ['گیربکس و انتقال قدرت', 'fa-circle-dot'],
            ['جلوبندی و تعلیق', 'fa-arrows-up-down'],
            ['سیستم ترمز', 'fa-circle-stop'],
            ['بدنه و فیس‌لیفت', 'fa-car-side'],
            ['چراغ و روشنایی', 'fa-lightbulb'],
            ['برق، ECU و سنسورها', 'fa-microchip'],
            ['سیستم خنک‌کننده', 'fa-temperature-low'],
            ['فیلتر و قطعات مصرفی', 'fa-filter'],
            ['داخل کابین و تزئینات', 'fa-couch'],
            ['لوازم جانبی و AMG', 'fa-sliders'],
        ];
        foreach ($categories as $i => [$name, $icon]) {
            Category::query()->firstOrCreate(['name' => $name], ['icon' => $icon, 'sort_order' => $i]);
        }

        $tree = [
            ['Mercedes-Benz', 'مرسدس بنز', true, [
                'C-Class' => [['W204', 2007, 2014], ['W205', 2014, 2021], ['W206', 2021, null]],
                'E-Class' => [['W211', 2002, 2009], ['W212', 2009, 2016], ['W213', 2016, 2023], ['W214', 2023, null]],
                'S-Class' => [['W221', 2005, 2013], ['W222', 2013, 2020], ['W223', 2020, null]],
                'CLS' => [['C218', 2011, 2018], ['C257', 2018, 2023]],
                'G-Class' => [['W463', 1990, 2018], ['W463A', 2018, null]],
                'GLE / ML' => [['W164', 2005, 2011], ['W166', 2011, 2019], ['V167', 2019, null]],
                'GLC' => [['X253', 2015, 2022], ['X254', 2022, null]],
                'GLS / GL' => [['X166', 2012, 2019], ['X167', 2019, null]],
            ]],
            ['BMW', 'بی‌ام‌و', true, [
                '3 Series' => [['F30', 2012, 2019], ['G20', 2019, null]],
                '5 Series' => [['F10', 2010, 2017], ['G30', 2017, 2023]],
                '7 Series' => [['F01', 2008, 2015], ['G11', 2015, 2022]],
                'X5' => [['F15', 2013, 2018], ['G05', 2018, null]],
            ]],
            ['Toyota', 'تویوتا', true, [
                'Land Cruiser' => [['J200', 2007, 2021], ['J300', 2021, null]],
                'Camry' => [['XV50', 2011, 2017], ['XV70', 2017, null]],
                'Prado' => [['J150', 2009, 2023]],
            ]],
            ['Lexus', 'لکسوس', true, [
                'LX' => [['LX570 (J200)', 2007, 2021], ['LX600 (J310)', 2021, null]],
                'ES' => [['XV60', 2012, 2018], ['XZ10', 2018, null]],
            ]],
            ['Porsche', 'پورشه', false, [
                'Cayenne' => [['958', 2010, 2017], ['9Y0', 2017, null]],
                'Panamera' => [['970', 2009, 2016], ['971', 2016, null]],
            ]],
            ['Land Rover', 'لندروور', false, [
                'Range Rover' => [['L405', 2012, 2021], ['L460', 2022, null]],
                'Range Rover Sport' => [['L494', 2013, 2022]],
            ]],
            ['Hyundai', 'هیوندای', false, [
                'Sonata' => [['LF', 2014, 2019], ['DN8', 2019, null]],
                'Santa Fe' => [['DM', 2012, 2018], ['TM', 2018, null]],
            ]],
            ['Kia', 'کیا', false, [
                'Optima / K5' => [['JF', 2015, 2020], ['DL3', 2020, null]],
                'Sorento' => [['UM', 2014, 2020], ['MQ4', 2020, null]],
            ]],
            ['Nissan', 'نیسان', false, [
                'Patrol' => [['Y62', 2010, null]],
                'Maxima' => [['A36', 2015, 2023]],
            ]],
        ];

        foreach ($tree as $b => [$name, $nameFa, $featured, $models]) {
            $brand = Brand::query()->firstOrCreate(['name' => $name], [
                'name_fa' => $nameFa, 'is_featured' => $featured, 'sort_order' => $b,
            ]);

            $m = 0;
            foreach ($models as $modelName => $chassisList) {
                $model = CarModel::query()->firstOrCreate(
                    ['brand_id' => $brand->id, 'name' => $modelName],
                    ['sort_order' => $m++]
                );

                foreach ($chassisList as $c => [$code, $from, $to]) {
                    Chassis::query()->firstOrCreate(
                        ['car_model_id' => $model->id, 'code' => $code],
                        ['year_from' => $from, 'year_to' => $to, 'sort_order' => $c]
                    );
                }
            }
        }
    }
}
