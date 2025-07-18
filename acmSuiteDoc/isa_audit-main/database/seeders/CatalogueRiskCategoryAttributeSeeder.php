<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\V2\Catalogs\RiskCategory;
use App\Models\V2\Catalogs\RiskAttribute;

class CatalogueRiskCategoryAttributeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \DB::table('risk_category_risk_attribute')->truncate();
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
    $categories = RiskCategory::all();
    $attributes = RiskAttribute::all()->pluck('id_risk_attribute');

    foreach ($categories as $category) {
      $category->attributes()->attach($attributes);
    }
  }
}