<?php

/**
 * @file
 * Contains \Drupal\wfm_migrate_recipe\Plugin\migrate\source\WfmMigrateRecipe.
 */

namespace Drupal\wfm_migrate_recipe\Plugin\migrate\source;

use Drupal\migrate\Row;
use Wfm\Api\SageClient\Recipe;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

// The config should be stored in settings.php, or outside of docroot
require('config.php');

/**
 * Source plugin for WFM Recipe content.
 *
 * @MigrateSource(
 *   id = "wfm_migrate_recipe"
 * )
 */
class WfmMigrateRecipe extends SourcePluginBase {

  protected function initializeIterator() {
    $apiRecipe = new Recipe(API_KEY, API_SECRET, API_URL);
    $apiRecipe->setLimit(50);
    $rows = $apiRecipe->getRecipesModifiedSince(strtotime('-240 month'));
    //$rows = $apiRecipe->getAllRecipes();
    $it = new \ArrayIterator($rows);
    return $it;
  }

  public function getIds() {
    return array(
      '_id' => array(
        // Should be 'string' if the IDs are strings
        'type' => 'string',
      ),
    );
  }

  public function fields() {
    return array(
      'id' => t('ID number for each recipe'),
      '_id' => t('Mongo ID for each recipe'),
      'title' => t('Title of recipe'),
      'modified_at' => 'Datetime recipe was last modified',
      'modified_at2' => 'Datetime recipe was last modified',
      'description' => 'Description',
      'comment_count' => 'Number of comments',
      'number_of_ratings' => 'Number of ratings',
      'created_at' => 'Created',
      'rating' => 'Rating',
      'weighted_rating' => 'Weighted Rating',
      'prep_time' => 'Preparation time',
      'serves' => 'Serves',
      'ingredients' => 'Ingredients',
      'directions' => 'Directions',
      'photos' => 'Photographs',
      'special_diet' => 'Special Diet Flag',
      'type_dish' => 'Type of dish',
      'cuisine' => 'Cuisine',
      'occasion' => 'Occasion',
      'category' => 'Category',
      'categories' => 'Categories',
      'main_ingredient' => 'Main Ingredient in dish',
      //'cloudinary_images' => 'Cloudinary Images',
      'image_filename' => 'Image Filename',
      'field_hero_image' => 'Main image for the recipe',
      'image_alt_text' => 'Image title text',
      'image_title_text' => 'Image title text',
      'height' => "Image height",
      'width' => "Image Width",
      'basic_nutrition' => t('Nutritional Info'),
    );
  }

  public function __toString() {
    return (string) $this->query;
  }

  public function prepareRow(Row $row) {
    // do my row mods here..
    $title = $row->getSourceProperty("title") ;
    $str = sprintf("Processing: %s", $title);
    drush_print_r($str);

    // Bail if this is a non-published recipe
    $status = $row->getSourceProperty("status");
    if ($status != 'Published') {
      return FALSE;  // Don't migrate non-published recipes.
    }

    $nutrition = $row->getSourceProperty("basic_nutrition");
    $nutrition_string = $this->prepareNutritionInfo($nutrition);
    $row->setSourceProperty("basic_nutrition", $nutrition_string);

    $ingredients = $row->getSourceProperty("ingredients");
    $ingredients = $this->prepareIngredients($ingredients);
    $row->setSourceProperty("ingredients", $ingredients);

    $title = $row->getSourceProperty("title");
    $photos = $row->getSourceProperty("photos");
    $photos = $this->preparePhotos($title, $photos);
    $row->setSourceProperty("photos", $photos);

    return parent::prepareRow($row);
  }

  public function preparePhotos($title, $photos) {
    //$return = array();
    //foreach ($photos as $photo) {
    //  $filename = basename($photo['url']);
    //  $fid = $this->lookupImageFid($filename);
    //  $return[] = $fid;
    //  $str = sprintf("fid: %s photo:%s", $fid, $filename);
    //  drush_print_r($str);
    //}
    //return $return;

    // Return only the first image
    $return = array();
    $photo = array();
    $photo = array_shift($photos);
    $filename = basename($photo['url']);
    //Modify the image filename to prepend recipe_hero_images for funky photo migrate.
    //$filename = "recipe_hero_images" . $filename;

    $fid = $this->lookupImageFid($filename);
    $return[] = $fid;

    //if ($fid == NULL) {
    //  $id = "not found";
    //}
    //else {
    //  $id = $fid;
    //}
    $str = sprintf("image: %s fid: %s", $filename, $fid);
    drush_print_r($str);

    return $return;
  }
  public function lookupImageFid($image) {
    if (!strlen($image)) {
      return NULL;
    }
    $fid = db_query('SELECT f.fid
      FROM {file_managed} f
      WHERE f.filename = :filename', array(':filename' => $image))
      ->fetchField();
    if ($fid === false) {
      $fid = NULL;
    }
    return $fid;
  }
  /**
   * Grab the ingredients.
   *
   * @param array $ingredients
   *   Array of ingredients for this recipe.
   *
   * @return array
   *  Array of nicely formatted ingredients with uoms.
   */
  public function prepareIngredients($ingredients) {
    $return = array();
    foreach ($ingredients as $ingredient) {
      $ingredient_string = $this->prepareIngredient($ingredient);
      if (strlen($ingredient_string)) {
        $return[] = $ingredient_string;
      }
    }
    return $return;
  }
  /**
   * Process one ingredient at a time.
   *
   * @param array $row
   *   One of the ingredients for this recipe.
   *
   * @return string
   *   A nice neat string for display like: "1 large yellow chopped onion"
   */
  private function prepareIngredient($row) {
    $item = new \stdClass();
    $content = array();

    // Convert array to an object.
    foreach ($row as $key => $value) {
      $item->$key = $value;
    }

    if (!empty($item->amount)) {
      if (is_float($item->amount)) {
        $item->amount = $this->decToFraction($item->amount);
      }
      $content[] = $item->amount . ' ';
    }

    if (!empty($item->package_size_amount)) {
      $content[] = '(' . $item->package_size_amount . '-' .  $item->package_size_uom . ') ';
    }

    if (!empty($item->uom)) {
      if (is_numeric($item->amount)) {
        if ($item->amount == 1) {
          // E.g. Tablespoon.
          $content[] = $item->uom . ' ';
        }
        else {
          // E.g. Tablespoons.
          $content[] = $item->uom_plural . ' ';
        }
      }
      else {
        // $item->Amount is a string like "1/2"
        $content[] = $item->uom . ' ';
      }
    }
    $content[] = $item->description;

    if (!empty($item->extra_info)) {
      $content[] = ', ' . $item->extra_info;
    }

    // Put them all together into a nice string.
    $string_ver = implode('', $content);
    return $string_ver;
  }

  /**
   * Convert Decimal to Fraction.
   *
   * @param float $float
   *   a float like 0.5 or 0.75
   *
   * @return float|string
   *   a string like 1/2  or 3/4
   */
  public function decToFraction($float) {
    // 1/2, 1/4, 1/8, 1/16, 1/3 ,2/3, 3/4, 3/8, 5/8, 7/8, 3/16, 5/16, 7/16,
    // 9/16, 11/16, 13/16, 15/16
    $whole = floor($float);
    $decimal = $float - $whole;
    // 16 * 3;
    $least_common_denom = 48;
    $denominators = array(2, 3, 4, 8, 16, 24, 48);
    $rounded_decimal = round($decimal * $least_common_denom) / $least_common_denom;
    if ($rounded_decimal == 0) {
      return $whole;
    }
    if ($rounded_decimal == 1) {
      return $whole + 1;
    }

    foreach ($denominators as $d) {
      if ($rounded_decimal * $d == floor($rounded_decimal * $d)) {
        $denom = $d;
        break;
      }
    }
    return ($whole == 0 ? '' : $whole) . " " . ($rounded_decimal * $denom) . "/" . $denom;
  }

  /**
   * Grab nutritional information and construct schema.org appropriate html.
   *
   * @param array $nut_array
   *   Array of nutritional data.
   *
   * @return string
   *  A neatly constructed string ready for display.
   */
  private function prepareNutritionInfo($nut_array) {
    $content = array();
    $uom_g = 'g';
    $uom_mg = 'mg';
    $string_ver = '';

    // Don't go through all this if there is no nutritional info.
    if (empty($nut_array)) {
      return "";
    }

    // Convert array to an object.
    $nutrition = new \stdClass();
    foreach ($nut_array as $key => $value) {
      $nutrition->$key = $value;
    }

    $content[] = '<strong>' . t('Per Serving:') . '</strong>';

    // Serving size: about 1/4 cup,
    if (isset($nutrition->serving_size)) {
      $content[] = 'Serving size: <span itemprop="servingSize">' . $nutrition->serving_size . '</span>,';
    }

    // 150 calories (100 from fat),
    if (isset($nutrition->calories)) {
      $partial_content = '<span itemprop="calories">' . $nutrition->calories .
        ' calories</span> ';
      if (isset($nutrition->calories_from_fat)) {
        $partial_content .= '(' . $nutrition->calories_from_fat . ' from fat),';
      }
      $content[] = $partial_content;
    }

    // 11g total fat,
    if (isset($nutrition->total_fat)) {
      $content[] = '<span itemprop="fatContent">' . $nutrition->total_fat . $uom_g . ' </span>total fat,';
    }

    // 1g saturated fat,
    if (isset($nutrition->saturated_fat)) {
      $content[] = '<span itemprop="saturatedFatContent">' . $nutrition->saturated_fat . $uom_g . '</span> saturated fat,';
    }

    // 0mg cholesterol,
    if (isset($nutrition->cholesterol)) {
      $content[] = '<span itemprop="cholesterolContent">' . $nutrition->cholesterol . $uom_mg . '</span> cholesterol,';
    }

    // 190mg sodium,
    if (isset($nutrition->sodium)) {
      $content[] = '<span itemprop="sodiumContent">' . $nutrition->sodium . $uom_mg . '</span> sodium,';
    }

    // 10g carbohydrate.
    if (isset($nutrition->total_carbohydrates)) {
      $content[] = '<span itemprop="carbohydrateContent">' . $nutrition->total_carbohydrates . $uom_g .
        '</span> carbohydrates,';
    }

    // (3g dietary fiber, 2g sugar),
    if (isset($nutrition->carbohydrates_from_fiber)) {
      $partial_content = '(' . $nutrition->carbohydrates_from_fiber . '</span><span itemprop="fiberContent">' . $uom_g . '</span> dietary fiber';
      if (isset($nutrition->carbohydrates_from_sugar)) {
        $partial_content .= ', <span itemprop="sugarContent">' .$nutrition->carbohydrates_from_sugar .
          $uom_g . '</span> sugar';
      }
      $partial_content .= '),';
      $content[] = $partial_content;
    }
    // 4g protein.
    if (isset($nutrition->protein)) {
      $content[] = '<span itemprop="proteinContent">' . $nutrition->protein . $uom_g . '</span> protein';
    }

    // Put them all together into a nice string.
    $string_ver = implode(' ', $content);

    // Remove any trialing whitespace & comma.
    $string_ver = rtrim($string_ver, ', ');
    $string_ver .= '.';
    return $string_ver;

  }

}
