<?php

namespace Database\Seeders;

use HexideDigital\AdminConfigurations\Models\AdminConfiguration;
use Illuminate\Database\Seeder;

class AdminConfigurationExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $data = [
            'home' => [
                AdminConfiguration::itemImage('image', 'preview', 0, '/img/home_img.png'),
                AdminConfiguration::itemTitle('title', 'main title', 1, [
                    'uk' => 'Назва',
                    'ru' => 'Название',
                    'en' => 'Named',
                ]),
                AdminConfiguration::itemText('address', 'address', 1, [
                    'uk' => $faker->paragraph(2),
                    'ru' => $faker->paragraph(2),
                    'en' => $faker->paragraph(2),
                ]),
            ],
            'home_banner' => [
                AdminConfiguration::itemTitle('banner_title', 'text 1', 1, $faker->words(3, true)),
                AdminConfiguration::itemImage('banner_image', 'image 1', 0, '/img/banner1.png'),

                AdminConfiguration::itemTitle('banner_title', 'text 2', 1, $faker->words(3, true)),
                AdminConfiguration::itemImage('banner_image', 'image 2', 0, '/img/banner2.png'),

                AdminConfiguration::itemTitle('banner_title', 'text 3', 1, $faker->words(3, true)),
                AdminConfiguration::itemImage('banner_image', 'image 3', 0, '/img/banner3.png'),
            ],
            'contacts' => [
                AdminConfiguration::itemTitle('email', 'company.mail@email.com', 0, 'company.mail@email.com'),
                AdminConfiguration::itemTitle('phone', '+38 (0382) 878787', 0, '+38 (0382) 878787'),
                AdminConfiguration::itemTitle('phone', '+38 (053) 4578679', 0, '+38 (053) 4578679'),
                AdminConfiguration::itemTitle('address', 'address', 1, [
                    'uk' => "вул.Шевченка 1/1A 01000 м. Київ, Україна",
                    'ru' => "ул.Шевченка 1/1A 01000  Киев, Украина",
                    'en' => "Shewchnka Street 1/1A 01000  Kiev, Ukraine",
                ]),
                AdminConfiguration::itemTitle('map_link', 'Google Map URL', 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d325515.6816222933!2d30.25250901759096!3d50.40213675479182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40d4cf4ee15a4505%3A0x764931d2170146fe!2sKyiv%2C%2002000!5e0!3m2!1sen!2sua!4v1632052962910!5m2!1sen!2sua'),

                AdminConfiguration::itemTitle('instagram', 'Insta', 0, 'https://instagram.com'),
                AdminConfiguration::itemTitle('facebook', 'FB', 0, 'https://facebook.com'),
            ],
            'configurations' => [
                AdminConfiguration::itemTitle('show_admin_header', 'Відображення навігації адміністратора на сайті',
                    0, 'Дане поле відповідає за відображення навігації для адміністратора'),
                AdminConfiguration::itemTitle('show_debug_footer', 'Відображення інструментів для відлагодження коду',
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

                AdminConfiguration::create($item);
            }
        }
    }
}
