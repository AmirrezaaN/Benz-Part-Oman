<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Chassis;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Sample products & slides so the storefront is not empty during development.
 * Delete them from the admin panel before going live.
 */
class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $cat = fn (string $name) => Category::query()->where('name', $name)->value('id');
        $brand = fn (string $name) => Brand::query()->where('name', $name)->value('id');
        $chassis = fn (array $codes) => Chassis::query()->whereIn('code', $codes)->pluck('id')->all();

        $items = [
            ['کمک فنر بادی جلو (Airmatic) مرسدس بنز S-Class', 'A2223204713', 'Mercedes-Benz', 'جلوبندی و تعلیق', ['W222'], 2014, 2020, 'new', 4, 395, 'Germany', 'Mercedes-Benz Genuine', true,
                'کمک بادی فابریک بنز کاملاً اورجینال و آکبند، همراه با سوکت و سنسور ارتفاع اصلی. مناسب S500 / S400 / S63.'],
            ['چراغ جلو جفت Multibeam LED بنز E-Class', 'A2139063904', 'Mercedes-Benz', 'چراغ و روشنایی', ['W213'], 2016, 2020, 'used', 2, 760, 'Germany', 'Hella', true,
                'چراغ‌های مالتی‌بیم اصلی مجهز به ترانس فابریک، بدون شکستگی یا ترمیم. استوک دبی در حد نو.'],
            ['کیت کامل فیس‌لیفت S63 AMG برای مرسدس بنز S-Class', 'A2228800200', 'Mercedes-Benz', 'بدنه و فیس‌لیفت', ['W222'], 2014, 2017, 'new', 1, 1320, 'Germany', 'Mercedes-AMG', true,
                'شامل سپرهای جلو و عقب AMG، جلوپنجره پنج‌خط، دیفیوزر و سرپوش اگزوز استیل.'],
            ['موتور کامل ۲.۰ لیتری توربو M274 بنز C200 / E200', 'A2740100402', 'Mercedes-Benz', 'موتور و قطعات موتوری', ['W205', 'W212', 'W213'], 2014, 2020, 'used', 1, 1630, 'Germany', 'Mercedes-Benz Genuine', false,
                'موتور M274 کارکرد زیر ۴۰ هزار کیلومتر وارداتی از آلمان، همراه با برگه تست کمپرس.'],
            ['گیربکس ۹ سرعته 9G-Tronic مرسدس بنز', 'A7252700100', 'Mercedes-Benz', 'گیربکس و انتقال قدرت', ['W213', 'W205', 'W222', 'V167'], 2016, null, 'used', 2, 1120, 'Germany', 'Mercedes-Benz Genuine', true,
                'گیربکس ۹ دنده اصلی بنز، تست شده روی دستگاه هیدرولیک بدون تقه یا ارور.'],
            ['کامپیوتر موتور ECU ME17.7 بنز موتور M276', 'A2769002900', 'Mercedes-Benz', 'برق، ECU و سنسورها', ['W212', 'W222', 'W166'], 2011, 2018, 'used', 3, 230, 'Germany', 'Bosch', false,
                'ایسیو اصلی بوش آلمان بدون آب‌خوردگی، آنلاک و آماده تعریف سوئیچ.'],
            ['کیت دیسک و لنت ترمز سرامیکی AMG مرسدس G63', 'A0004212012', 'Mercedes-Benz', 'سیستم ترمز', ['W463', 'W463A'], 2012, null, 'new', 5, 325, 'Germany', 'Mercedes-AMG', true,
                'کیت دیسک خنک‌شونده شیاردار و لنت سرامیکی فابریک AMG.'],
            ['توربوشارژر کامل M271 EVO بنز C200 / E200', 'A2710903680', 'Mercedes-Benz', 'موتور و قطعات موتوری', ['W204', 'W212'], 2009, 2015, 'new', 6, 265, 'Germany', 'BorgWarner', false,
                'توربوشارژر بورگوارنر سفارش مرسدس بنز همراه با سنسور بوست.'],
            ['فیلتر روغن اصلی مرسدس بنز موتورهای M274 / M264', 'A2701800109', 'Mercedes-Benz', 'فیلتر و قطعات مصرفی', ['W205', 'W213', 'X253', 'W206'], 2014, null, 'new', 120, 6.5, 'Germany', 'Mann Filter', false,
                'فیلتر روغن اورجینال با واشر، مناسب سرویس دوره‌ای.'],
            ['رادیاتور آب اصلی مرسدس بنز E-Class', 'A2135000003', 'Mercedes-Benz', 'سیستم خنک‌کننده', ['W213'], 2016, 2023, 'oem', 3, 145, 'Germany', 'Behr', false,
                'رادیاتور OEM ساخت Behr با کیفیت فابریک.'],
            ['نمایشگر مرکزی (Head Unit) 12.3 اینچ بنز', 'A2229005017', 'Mercedes-Benz', 'داخل کابین و تزئینات', ['W222'], 2017, 2020, 'used', null, null, 'Germany', 'Mercedes-Benz Genuine', false,
                'نمایشگر اصلی مالتی‌مدیا، قیمت و موجودی با استعلام.'],
            ['کمک فنر جلو لندکروزر J200', '4851069905', 'Toyota', 'جلوبندی و تعلیق', ['J200'], 2008, 2021, 'new', 4, 88, 'Japan', 'Toyota Genuine', true,
                'کمک فنر اصلی تویوتا ژاپن، بسته‌بندی فابریک.'],
            ['لنت ترمز جلو BMW سری ۵', '34116860016', 'BMW', 'سیستم ترمز', ['G30'], 2017, 2023, 'oem', 10, 42, 'Germany', 'Textar', false,
                'لنت ترمز OEM تکستار، سفارش بی‌ام‌و.'],
            ['پمپ آب برقی لکسوس LX570', '161A039015', 'Lexus', 'سیستم خنک‌کننده', ['LX570 (J200)'], 2008, 2021, 'aftermarket', 0, 70, 'Japan', 'Aisin', false,
                'پمپ آب آیسین — فعلاً ناموجود؛ برای سفارش استعلام بگیرید.'],
        ];

        foreach ($items as [$name, $pn, $b, $c, $codes, $from, $to, $cond, $qty, $omr, $country, $maker, $featured, $desc]) {
            $product = Product::query()->create([
                'name' => $name, 'part_number' => $pn, 'brand_id' => $brand($b), 'category_id' => $cat($c),
                'year_from' => $from, 'year_to' => $to, 'condition' => $cond, 'quantity' => $qty, 'price_omr' => $omr,
                'country_of_origin' => $country, 'manufacturer' => $maker, 'is_featured' => $featured,
                'warehouse' => 'انبار معبیله مسقط', 'description' => $desc, 'is_active' => true,
            ]);
            $product->chassis()->sync($chassis($codes));
            $product->forceFill(['views' => random_int(5, 300)])->saveQuietly();
        }

        if (! Banner::query()->exists()) {
            Banner::query()->create([
                'badge' => 'واردات مستقیم از مسقط', 'title' => 'قطعات اصلی مرسدس بنز با ضمانت اصالت',
                'subtitle' => 'موجودی لحظه‌ای انبار عمان، قیمت شفاف به تومان و ریال عمان و ارسال سریع به سراسر ایران',
                'button_text' => 'مشاهده قطعات', 'button_url' => '/products', 'sort_order' => 1,
            ]);
            Banner::query()->create([
                'badge' => 'استعلام با شماره شاسی', 'title' => 'قطعه‌ات را پیدا نکردی؟ فقط VIN را بفرست',
                'subtitle' => 'کارشناسان ما با کاتالوگ رسمی EPC قطعه دقیق خودروی شما را پیدا و قیمت می‌دهند',
                'button_text' => 'ثبت استعلام', 'button_url' => '/inquiry', 'sort_order' => 2,
            ]);
            Banner::query()->create([
                'badge' => 'کیت‌های AMG', 'title' => 'فیس‌لیفت و کیت‌های اورجینال AMG',
                'subtitle' => 'سپر، جلوپنجره، دیفیوزر و رینگ‌های اصلی برای کلاس‌های S، E، C و G',
                'button_text' => 'مشاهده بدنه و فیس‌لیفت', 'button_url' => '/products?q=AMG', 'sort_order' => 3,
            ]);
        }
    }
}
