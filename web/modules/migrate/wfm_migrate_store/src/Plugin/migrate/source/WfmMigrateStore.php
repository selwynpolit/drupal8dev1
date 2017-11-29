<?php

/**
 * @file
 * Contains \Drupal\wfm_migrate_store\Plugin\migrate\source\WfmMigrateStore.
 */

namespace Drupal\wfm_migrate_store\Plugin\migrate\source;

use Drupal\migrate\Row;
use Wfm\Api\SageClient\Store;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

// The config should be stored in settings.php, or outside of docroot
require('config.php');

/**
 * Source plugin for WFM Store content.
 *
 * @MigrateSource(
 *   id = "wfm_migrate_store"
 * )
 */
class WfmMigrateStore extends SourcePluginBase {
  protected function initializeIterator() {
    $apiStore = new Store(API_KEY, API_SECRET, API_URL);
    $apiStore->setLimit(500);
    //$rows = $apiStore->getAllStores();
    // TODO: Select only the following fields
    //  $apiStore->setFields(array('name', 'tlc', 'address','zip_code', 'twitter'));

    $rows = $apiStore->getStoresModifiedSince(strtotime('-12 month'));
    $it = new \ArrayIterator($rows);

    return $it;
  }

  // We need these functions because we are extending an abstract class
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
      '_id' => t('Mongo ID for each store'),
      'name' => t('Name of store'),
      'display_name' => t('Display name maybe nickname? of store'), //TODO: is this nickname?
      'tlc' => t('Three letter code for store'),
      'geox' => t('Geolocation x-coordinate'),
      'geoy' => t('Geolocation y-coordinate'),
      'store_hours' => t('Store hours'),
      'address' => t('Address line 1'),
      'address2' => t('Address line 2'),
      'city' => t('City'),
      'state' => t('State'),
      'zip' => t('Zip code or Postal code'),
      'country' => t('Country'),
      'region' => t('Region'),
      'has_alcohol' => t('Store sells alcohol'),
      'twitter' => t('Store twitter username'),
    );
  }

  public function __toString() {
    return (string) $this->query;
  }

  public function prepareRow(Row $row) {
    // Perform source row modification here.

    // Bail if this is a non-published store
    $status = $row->getSourceProperty("status");
    if ($status != 'OPEN') {
      return FALSE;
    }




    $geo = $row->getSourceProperty("geo_location") ;
    $x = $geo['coordinates'][0];
    $y = $geo['coordinates'][1];
    $row->setSourceProperty("geox", $x);
    $row->setSourceProperty("geoy", $y);

    $hours = $row->getSourceProperty("hours") ;
    $row->setSourceProperty("hours",ltrim($hours));

    $country = $row->getSourceProperty("country") ;
    $country = $this->getIsoCountryCode($country);
    $row->setSourceProperty("country",$country);

    $state = $row->getSourceProperty("state");
    if ($country == "US") {
      $state = $this->getUsaStateCode($state);
      $row->setSourceProperty("state", $state);
    }

    $facebook = $row->getSourceProperty("facebook");
    if (!Empty($facebook)) {
      $facebook = "https://www.facebook.com/" . ltrim(trim($facebook));
    }
    $row->setSourceProperty("facebook", $facebook);

    $has_alcohol = $row->getSourceProperty("has_alcohol") ;
    if (empty($has_alcohol)) {
      $has_alcohol = 0 ;
      $row->setSourceProperty("has_alcohol", $has_alcohol);
    }


    //TODO: "states" not in USA - ie UK & Canada
    //TODO: lookup region eg. NE in taxonomy and store region actual taxonomy in field_store_region

    $tlc = $row->getSourceProperty("tlc") ;
    $name = $row->getSourceProperty("name") ;
    $country = $row->getSourceProperty("country") ;
    $state = $row->getSourceProperty("state") ;
    $has_alcohol = $row->getSourceProperty("has_alcohol") ;
    $str = sprintf("%s: %s %s %s alc:%s", $tlc, $name, $state, $country, $has_alcohol);

    drush_print_r($str);
    return parent::prepareRow($row);
  }

  public function getUsaStateCode($state) {
    static $states = array (
      'US-AL'=>'Alabama',
      'US-AK'=>'Alaska',
      'US-AZ'=>'Arizona',
      'US-AR'=>'Arkansas',
      'US-CA'=>'California',
      'US-CO'=>'Colorado',
      'US-CT'=>'Connecticut',
      'US-DE'=>'Delaware',
      'US-DC'=>'District Of Columbia',
      'US-FL'=>'Florida',
      'US-GA'=>'Georgia',
      'US-HI'=>'Hawaii',
      'US-ID'=>'Idaho',
      'US-IL'=>'Illinois',
      'US-IN'=>'Indiana',
      'US-IA'=>'Iowa',
      'US-KS'=>'Kansas',
      'US-KY'=>'Kentucky',
      'US-LA'=>'Louisiana',
      'US-ME'=>'Maine',
      'US-MD'=>'Maryland',
      'US-MA'=>'Massachusetts',
      'US-MI'=>'Michigan',
      'US-MN'=>'Minnesota',
      'US-MS'=>'Mississippi',
      'US-MO'=>'Missouri',
      'US-MT'=>'Montana',
      'US-NE'=>'Nebraska',
      'US-NV'=>'Nevada',
      'US-NH'=>'New Hampshire',
      'US-NJ'=>'New Jersey',
      'US-NM'=>'New Mexico',
      'US-NY'=>'New York',
      'US-NC'=>'North Carolina',
      'US-ND'=>'North Dakota',
      'US-OH'=>'Ohio',
      'US-OK'=>'Oklahoma',
      'US-OR'=>'Oregon',
      'US-PA'=>'Pennsylvania',
      'US-RI'=>'Rhode Island',
      'US-SC'=>'South Carolina',
      'US-SD'=>'South Dakota',
      'US-TN'=>'Tennessee',
      'US-TX'=>'Texas',
      'US-UT'=>'Utah',
      'US-VT'=>'Vermont',
      'US-VA'=>'Virginia',
      'US-WA'=>'Washington',
      'US-WV'=>'West Virginia',
      'US-WI'=>'Wisconsin',
      'US-WY'=>'Wyoming',
    );
    $code = array_search($state, $states);
    return $code;
  }

  public function getIsoCountryCode($country) {
    static $countries = array
    (
      'AF' => 'Afghanistan',
      'AX' => 'Aland Islands',
      'AL' => 'Albania',
      'DZ' => 'Algeria',
      'AS' => 'American Samoa',
      'AD' => 'Andorra',
      'AO' => 'Angola',
      'AI' => 'Anguilla',
      'AQ' => 'Antarctica',
      'AG' => 'Antigua And Barbuda',
      'AR' => 'Argentina',
      'AM' => 'Armenia',
      'AW' => 'Aruba',
      'AU' => 'Australia',
      'AT' => 'Austria',
      'AZ' => 'Azerbaijan',
      'BS' => 'Bahamas',
      'BH' => 'Bahrain',
      'BD' => 'Bangladesh',
      'BB' => 'Barbados',
      'BY' => 'Belarus',
      'BE' => 'Belgium',
      'BZ' => 'Belize',
      'BJ' => 'Benin',
      'BM' => 'Bermuda',
      'BT' => 'Bhutan',
      'BO' => 'Bolivia',
      'BA' => 'Bosnia And Herzegovina',
      'BW' => 'Botswana',
      'BV' => 'Bouvet Island',
      'BR' => 'Brazil',
      'IO' => 'British Indian Ocean Territory',
      'BN' => 'Brunei Darussalam',
      'BG' => 'Bulgaria',
      'BF' => 'Burkina Faso',
      'BI' => 'Burundi',
      'KH' => 'Cambodia',
      'CM' => 'Cameroon',
      'CA' => 'Canada',
      'CV' => 'Cape Verde',
      'KY' => 'Cayman Islands',
      'CF' => 'Central African Republic',
      'TD' => 'Chad',
      'CL' => 'Chile',
      'CN' => 'China',
      'CX' => 'Christmas Island',
      'CC' => 'Cocos (Keeling) Islands',
      'CO' => 'Colombia',
      'KM' => 'Comoros',
      'CG' => 'Congo',
      'CD' => 'Congo, Democratic Republic',
      'CK' => 'Cook Islands',
      'CR' => 'Costa Rica',
      'CI' => 'Cote D\'Ivoire',
      'HR' => 'Croatia',
      'CU' => 'Cuba',
      'CY' => 'Cyprus',
      'CZ' => 'Czech Republic',
      'DK' => 'Denmark',
      'DJ' => 'Djibouti',
      'DM' => 'Dominica',
      'DO' => 'Dominican Republic',
      'EC' => 'Ecuador',
      'EG' => 'Egypt',
      'SV' => 'El Salvador',
      'GQ' => 'Equatorial Guinea',
      'ER' => 'Eritrea',
      'EE' => 'Estonia',
      'ET' => 'Ethiopia',
      'FK' => 'Falkland Islands (Malvinas)',
      'FO' => 'Faroe Islands',
      'FJ' => 'Fiji',
      'FI' => 'Finland',
      'FR' => 'France',
      'GF' => 'French Guiana',
      'PF' => 'French Polynesia',
      'TF' => 'French Southern Territories',
      'GA' => 'Gabon',
      'GM' => 'Gambia',
      'GE' => 'Georgia',
      'DE' => 'Germany',
      'GH' => 'Ghana',
      'GI' => 'Gibraltar',
      'GR' => 'Greece',
      'GL' => 'Greenland',
      'GD' => 'Grenada',
      'GP' => 'Guadeloupe',
      'GU' => 'Guam',
      'GT' => 'Guatemala',
      'GG' => 'Guernsey',
      'GN' => 'Guinea',
      'GW' => 'Guinea-Bissau',
      'GY' => 'Guyana',
      'HT' => 'Haiti',
      'HM' => 'Heard Island & Mcdonald Islands',
      'VA' => 'Holy See (Vatican City State)',
      'HN' => 'Honduras',
      'HK' => 'Hong Kong',
      'HU' => 'Hungary',
      'IS' => 'Iceland',
      'IN' => 'India',
      'ID' => 'Indonesia',
      'IR' => 'Iran, Islamic Republic Of',
      'IQ' => 'Iraq',
      'IE' => 'Ireland',
      'IM' => 'Isle Of Man',
      'IL' => 'Israel',
      'IT' => 'Italy',
      'JM' => 'Jamaica',
      'JP' => 'Japan',
      'JE' => 'Jersey',
      'JO' => 'Jordan',
      'KZ' => 'Kazakhstan',
      'KE' => 'Kenya',
      'KI' => 'Kiribati',
      'KR' => 'Korea',
      'KW' => 'Kuwait',
      'KG' => 'Kyrgyzstan',
      'LA' => 'Lao People\'s Democratic Republic',
      'LV' => 'Latvia',
      'LB' => 'Lebanon',
      'LS' => 'Lesotho',
      'LR' => 'Liberia',
      'LY' => 'Libyan Arab Jamahiriya',
      'LI' => 'Liechtenstein',
      'LT' => 'Lithuania',
      'LU' => 'Luxembourg',
      'MO' => 'Macao',
      'MK' => 'Macedonia',
      'MG' => 'Madagascar',
      'MW' => 'Malawi',
      'MY' => 'Malaysia',
      'MV' => 'Maldives',
      'ML' => 'Mali',
      'MT' => 'Malta',
      'MH' => 'Marshall Islands',
      'MQ' => 'Martinique',
      'MR' => 'Mauritania',
      'MU' => 'Mauritius',
      'YT' => 'Mayotte',
      'MX' => 'Mexico',
      'FM' => 'Micronesia, Federated States Of',
      'MD' => 'Moldova',
      'MC' => 'Monaco',
      'MN' => 'Mongolia',
      'ME' => 'Montenegro',
      'MS' => 'Montserrat',
      'MA' => 'Morocco',
      'MZ' => 'Mozambique',
      'MM' => 'Myanmar',
      'NA' => 'Namibia',
      'NR' => 'Nauru',
      'NP' => 'Nepal',
      'NL' => 'Netherlands',
      'AN' => 'Netherlands Antilles',
      'NC' => 'New Caledonia',
      'NZ' => 'New Zealand',
      'NI' => 'Nicaragua',
      'NE' => 'Niger',
      'NG' => 'Nigeria',
      'NU' => 'Niue',
      'NF' => 'Norfolk Island',
      'MP' => 'Northern Mariana Islands',
      'NO' => 'Norway',
      'OM' => 'Oman',
      'PK' => 'Pakistan',
      'PW' => 'Palau',
      'PS' => 'Palestinian Territory, Occupied',
      'PA' => 'Panama',
      'PG' => 'Papua New Guinea',
      'PY' => 'Paraguay',
      'PE' => 'Peru',
      'PH' => 'Philippines',
      'PN' => 'Pitcairn',
      'PL' => 'Poland',
      'PT' => 'Portugal',
      'PR' => 'Puerto Rico',
      'QA' => 'Qatar',
      'RE' => 'Reunion',
      'RO' => 'Romania',
      'RU' => 'Russian Federation',
      'RW' => 'Rwanda',
      'BL' => 'Saint Barthelemy',
      'SH' => 'Saint Helena',
      'KN' => 'Saint Kitts And Nevis',
      'LC' => 'Saint Lucia',
      'MF' => 'Saint Martin',
      'PM' => 'Saint Pierre And Miquelon',
      'VC' => 'Saint Vincent And Grenadines',
      'WS' => 'Samoa',
      'SM' => 'San Marino',
      'ST' => 'Sao Tome And Principe',
      'SA' => 'Saudi Arabia',
      'SN' => 'Senegal',
      'RS' => 'Serbia',
      'SC' => 'Seychelles',
      'SL' => 'Sierra Leone',
      'SG' => 'Singapore',
      'SK' => 'Slovakia',
      'SI' => 'Slovenia',
      'SB' => 'Solomon Islands',
      'SO' => 'Somalia',
      'ZA' => 'South Africa',
      'GS' => 'South Georgia And Sandwich Isl.',
      'ES' => 'Spain',
      'LK' => 'Sri Lanka',
      'SD' => 'Sudan',
      'SR' => 'Suriname',
      'SJ' => 'Svalbard And Jan Mayen',
      'SZ' => 'Swaziland',
      'SE' => 'Sweden',
      'CH' => 'Switzerland',
      'SY' => 'Syrian Arab Republic',
      'TW' => 'Taiwan',
      'TJ' => 'Tajikistan',
      'TZ' => 'Tanzania',
      'TH' => 'Thailand',
      'TL' => 'Timor-Leste',
      'TG' => 'Togo',
      'TK' => 'Tokelau',
      'TO' => 'Tonga',
      'TT' => 'Trinidad And Tobago',
      'TN' => 'Tunisia',
      'TR' => 'Turkey',
      'TM' => 'Turkmenistan',
      'TC' => 'Turks And Caicos Islands',
      'TV' => 'Tuvalu',
      'UG' => 'Uganda',
      'UA' => 'Ukraine',
      'AE' => 'United Arab Emirates',
      'GB' => 'United Kingdom',
      'US' => 'United States',
      'US' => 'USA',
      'UM' => 'United States Outlying Islands',
      'UY' => 'Uruguay',
      'UZ' => 'Uzbekistan',
      'VU' => 'Vanuatu',
      'VE' => 'Venezuela',
      'VN' => 'Viet Nam',
      'VG' => 'Virgin Islands, British',
      'VI' => 'Virgin Islands, U.S.',
      'WF' => 'Wallis And Futuna',
      'EH' => 'Western Sahara',
      'YE' => 'Yemen',
      'ZM' => 'Zambia',
      'ZW' => 'Zimbabwe',
    );


    $x = array_search($country, $countries) ;
    return $x;

  }


}
