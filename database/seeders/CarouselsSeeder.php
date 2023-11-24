<?php

namespace Database\Seeders;

use App\Models\Carousel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarouselsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carousels = array(
            [
                'name' => "Promo",
                'type' => "promo",
                'dots' => 'false',
                'arrows' => 'false',
                'accessibility' => 'true',
                'rows' => 1,
                'slides_to_show' => 1,
                'slides_per_row' => 1,
                'autoplay_speed' => 5000,
                'pause_on_hover' => 'true',
                'center_padding' => '0'
            ],
            [
                'name' => "Main",
                'type' => "main",
                'dots' => 'false',
                'arrows' => 'false',
                'accessibility' => 'true',
                'rows' => 1,
                'slides_to_show' => 1,
                'slides_per_row' => 1,
                'autoplay_speed' => 5000,
                'pause_on_hover' => 'true',
                'center_padding' => '0'
            ],
            [
                'name' => "Products",
                'type' => "products",
                'dots' => 'false',
                'arrows' => 'false',
                'accessibility' => 'true',
                'rows' => 1,
                'slides_to_show' => 7,
                'slides_per_row' => 1,
                'autoplay_speed' => 3000,
                'pause_on_hover' => 'false',
                'center_padding' => '0'
            ],
        );

        Carousel::insert($carousels);
    }
}
