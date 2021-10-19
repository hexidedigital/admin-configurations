<?php

use App\Models\Variable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulateVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $faker = \Faker\Factory::create();

        $data = [
            'home' => [
                Variable::imageItem('image', 'preview', 0, '/img/home_img.png'),
                Variable::titleItem('title', 'main title', 1, [
                    'uk' => 'Назва',
                    'ru' => 'Название',
                    'en' => 'Named',
                ]),
                Variable::textItem('address', 'address', 1, [
                    'uk' => $faker->paragraph(2),
                    'ru' => $faker->paragraph(2),
                    'en' => $faker->paragraph(2),
                ]),
            ],
            'home_banner' => [
                Variable::titleItem('banner_title', 'text 1', 1, $faker->words(3, true)),
                Variable::imageItem('banner_image', 'image 1', 0, '/img/banner1.png'),

                Variable::titleItem('banner_title', 'text 2', 1, $faker->words(3, true)),
                Variable::imageItem('banner_image', 'image 2', 0, '/img/banner2.png'),

                Variable::titleItem('banner_title', 'text 3', 1, $faker->words(3, true)),
                Variable::imageItem('banner_image', 'image 3', 0, '/img/banner3.png'),
            ],
            'contacts' => [
                Variable::titleItem('email', 'company.mail@email.com', 0, 'company.mail@email.com'),
                Variable::titleItem('phone', '+38 (0382) 878787', 0, '+38 (0382) 878787'),
                Variable::titleItem('phone', '+38 (053) 4578679', 0, '+38 (053) 4578679'),
                Variable::titleItem('address', 'address', 1, [
                    'uk' => "вул.Шевченка 1/1A 01000 м. Київ, Україна",
                    'ru' => "ул.Шевченка 1/1A 01000  Киев, Украина",
                    'en' => "Shewchnka Street 1/1A 01000  Kiev, Ukraine",
                ]),
                Variable::titleItem('map_link', 'Google Map URL', 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d325515.6816222933!2d30.25250901759096!3d50.40213675479182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40d4cf4ee15a4505%3A0x764931d2170146fe!2sKyiv%2C%2002000!5e0!3m2!1sen!2sua!4v1632052962910!5m2!1sen!2sua'),

                Variable::titleItem('instagram', 'Insta', 0, 'https://instagram.com'),
                Variable::titleItem('facebook', 'FB', 0, 'https://facebook.com'),
            ],
            'configurations' => [
                Variable::titleItem('show_admin_header', 'Відображення навігації адміністратора на сайті',
                    0, 'Дане поле відповідає за відображення навігації для адміністратора'),
                Variable::titleItem('show_debug_footer', 'Відображення інструментів для відлагодження коду',
                    0, 'Дане поле відповідає за відображення спеціального елементу в нижній частині екрану'),
            ],
        ];

        foreach ($data as $group => $items) {
            $position = 1;

            foreach ($items as $item) {
                $item['status'] = true;
                $item['group'] = $group;
                $item['in_group_position'] = $position++;

                if (!isset($item['translatable'])) {
                    $item['translatable'] = false;
                }

                Variable::create($item);
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('variable_translations')->delete();
        DB::table('variables')->delete();
    }
}
