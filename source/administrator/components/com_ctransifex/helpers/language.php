<?php
/**
 * This is a fork of the Text_LanguageDetect class from the PEAR project Text_LanguageDetect
 *
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       : 22.10.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Part of Text_LanguageDetect
 *
 * PHP version 5
 *
 * @category  Text
 * @package   Text_LanguageDetect
 * @author    Christian Weiske <cweiske@php.net>
 * @copyright 2011 Christian Weiske <cweiske@php.net>
 * @license   http://www.debian.org/misc/bsd.license BSD
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/Text_LanguageDetect/
 */

/**
 * Provides a mapping between the languages from lang.dat and the
 * ISO 639-1 and ISO-639-2 codes.
 *
 * Note that this class contains only languages that exist in lang.dat.
 *
 * @category  Text
 * @package   Text_LanguageDetect
 * @author    Christian Weiske <cweiske@php.net>
 * @copyright 2011 Christian Weiske <cweiske@php.net>
 * @license   http://www.debian.org/misc/bsd.license BSD
 * @link      http://www.loc.gov/standards/iso639-2/php/code_list.php
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class CtransifexHelperLanguage
 *
 * @since  1
 */
class CtransifexHelperLanguage
{
	/**
	 * Maps all language names from the language database to the
	 * ISO 639-1 2-letter language code.
	 *
	 * NULL indicates that there is no 2-letter code.
	 *
	 * @var array
	 */
	public static $nameToCode2 = array(
		'africaans' => 'af',
		'albanian' => 'sq',
		'amharic' => 'am',
		'arabic' => 'ar',
		'azeri' => 'az',
		'belarusian' => 'bel',
		'bengali' => 'bn',
		'bulgarian' => 'bg',
		'cebuano' => null,
		'chinese' => 'zh',
		'croatian' => 'hr',
		'czech' => 'cs',
		'danish' => 'da',
		'dutch' => 'nl',
		'english' => 'en',
		'esperanto' => 'eo',
		'estonian' => 'et',
		'farsi' => 'fa',
		'finnish' => 'fi',
		'french' => 'fr',
		'german' => 'de',
		'hausa' => 'ha',
		'hawaiian' => null,
		'hindi' => 'hi',
		'hungarian' => 'hu',
		'icelandic' => 'is',
		'indonesian' => 'id',
		'italian' => 'it',
		'kazakh' => 'kk',
		'kurdish' => 'ku',
		'kyrgyz' => 'ky',
		'latin' => 'la',
		'latvian' => 'lv',
		'lithuanian' => 'lt',
		'macedonian' => 'mk',
		'mongolian' => 'mn',
		'nepali' => 'ne',
		'norwegian' => 'no',
		'pashto' => 'ps',
		'pidgin' => null,
		'polish' => 'pl',
		'portuguese' => 'pt',
		'romanian' => 'ro',
		'russian' => 'ru',
		'serbian' => 'sr',
		'slovak' => 'sk',
		'slovene' => 'sl',
		'somali' => 'so',
		'spanish' => 'es',
		'swahili' => 'sw',
		'swedish' => 'sv',
		'tagalog' => 'tl',
		'turkish' => 'tr',
		'ukrainian' => 'uk',
		'urdu' => 'ur',
		'uzbek' => 'uz',
		'vietnamese' => 'vi',
		'welsh' => 'cy',

	);

	/**
	 * Maps all language names from the language database to the
	 * ISO 639-2 3-letter language code.
	 *
	 * @var array
	 */
	public static $nameToCode3 = array(
		'albanian' => 'sqi',
		'amharic' => 'amh',
		'arabic' => 'ara',
		'azeri' => 'aze',
		'belarusian' => 'bel',
		'bengali' => 'ben',
		'bulgarian' => 'bul',
		'cebuano' => 'ceb',
		'croatian' => 'hrv',
		'czech' => 'ces',
		'danish' => 'dan',
		'dutch' => 'nld',
		'english' => 'eng',
		'estonian' => 'est',
		'farsi' => 'fas',
		'finnish' => 'fin',
		'french' => 'fra',
		'german' => 'deu',
		'hausa' => 'hau',
		'hawaiian' => 'haw',
		'hindi' => 'hin',
		'hungarian' => 'hun',
		'icelandic' => 'isl',
		'indonesian' => 'ind',
		'italian' => 'ita',
		'kazakh' => 'kaz',
		'kyrgyz' => 'kir',
		'latin' => 'lat',
		'latvian' => 'lav',
		'lithuanian' => 'lit',
		'macedonian' => 'mkd',
		'mongolian' => 'mon',
		'nepali' => 'nep',
		'norwegian' => 'nor',
		'pashto' => 'pus',
		'pidgin' => 'crp',
		'polish' => 'pol',
		'portuguese' => 'por',
		'romanian' => 'ron',
		'russian' => 'rus',
		'serbian' => 'srp',
		'slovak' => 'slk',
		'slovene' => 'slv',
		'somali' => 'som',
		'spanish' => 'spa',
		'swahili' => 'swa',
		'swedish' => 'swe',
		'tagalog' => 'tgl',
		'turkish' => 'tur',
		'ukrainian' => 'ukr',
		'urdu' => 'urd',
		'uzbek' => 'uzb',
		'vietnamese' => 'vie',
		'welsh' => 'cym',
	);

	/**
	 * Maps ISO 639-1 2-letter language codes to the language names
	 * in the language database
	 *
	 * Not all languages have a 2 letter code, so some are missing
	 *
	 * @var array
	 */
	public static $code2ToName = array(
		'af' => 'africaans',
		'am' => 'amharic',
		'ar' => 'arabic',
		'az' => 'azeri',
		'be' => 'belarusian',
		'bg' => 'bulgarian',
		'bn' => 'bengali',
		'bs' => 'bosnian',
		'ca' => 'catalan',
		'cs' => 'czech',
		'cy' => 'welsh',
		'da' => 'danish',
		'de' => 'german',
		'el' => 'greek',
		'en' => 'english',
		'eo' => 'esperanto',
		'es' => 'spanish',
		'et' => 'estonian',
		'gl' => 'galician',
		'gu' => 'gujarati',
		'ja' => 'japanese',
		'fa' => 'farsi',
		'fi' => 'finnish',
		'fr' => 'french',
		'ha' => 'hausa',
		'he' => 'hebrew',
		'hi' => 'hindi',
		'hr' => 'croatian',
		'hu' => 'hungarian',
		'hy' => 'armenian',
		'id' => 'indonesian',
		'is' => 'icelandic',
		'it' => 'italian',
		'ka' => 'Georgian',
		'kk' => 'kazakh',
		'km' => 'Khmer',
		'ko' => 'korean',
		'ku' => 'kurdish',
		'ky' => 'kyrgyz',
		'la' => 'latin',
		'lt' => 'lithuanian',
		'lv' => 'latvian',
		'mk' => 'macedonian',
		'mn' => 'mongolian',
		'mr' => 'marathi',
		'ms' => 'malay',
		'my' => 'burmese',
		'nb' => 'norwegian',
		'ne' => 'nepali',
		'nl' => 'dutch',
		'no' => 'norwegian',
		'pl' => 'polish',
		'ps' => 'pashto',
		'pt' => 'portuguese',
		'ro' => 'romanian',
		'ru' => 'russian',
		'sk' => 'slovak',
		'sl' => 'slovene',
		'so' => 'somali',
		'sq' => 'albanian',
		'sr' => 'serbian',
		'sv' => 'swedish',
		'sw' => 'swahili',
		'ta' => 'tamil',
		'te' => 'telugo',
		'th' => 'thai',
		'tl' => 'tagalog',
		'tr' => 'turkish',
		'ug' => 'uighur',
		'uk' => 'ukrainian',
		'ur' => 'urdu',
		'uz' => 'uzbek',
		'vi' => 'vietnamese',
		'zh' => 'chinese',
	);

	/**
	 * Maps ISO 639-2 3-letter language codes to the language names
	 * in the language database.
	 *
	 * @var array
	 */
	public static $code3ToName = array(
		'ara' => 'arabic',
		'aze' => 'azeri',
		'bel' => 'belarusian',
		'ben' => 'bengali',
		'bul' => 'bulgarian',
		'ceb' => 'cebuano',
		'ces' => 'czech',
		'crp' => 'pidgin',
		'cym' => 'welsh',
		'dan' => 'danish',
		'deu' => 'german',
		'eng' => 'english',
		'est' => 'estonian',
		'fas' => 'farsi',
		'fin' => 'finnish',
		'fra' => 'french',
		'hau' => 'hausa',
		'haw' => 'hawaiian',
		'hin' => 'hindi',
		'hrv' => 'croatian',
		'hun' => 'hungarian',
		'ind' => 'indonesian',
		'isl' => 'icelandic',
		'ita' => 'italian',
		'kaz' => 'kazakh',
		'kir' => 'kyrgyz',
		'lat' => 'latin',
		'lav' => 'latvian',
		'lit' => 'lithuanian',
		'mkd' => 'macedonian',
		'mon' => 'mongolian',
		'nep' => 'nepali',
		'nld' => 'dutch',
		'nor' => 'norwegian',
		'pol' => 'polish',
		'por' => 'portuguese',
		'pus' => 'pashto',
		'rom' => 'romanian',
		'rus' => 'russian',
		'slk' => 'slovak',
		'slv' => 'slovene',
		'som' => 'somali',
		'spa' => 'spanish',
		'sqi' => 'albanian',
		'srp' => 'serbian',
		'swa' => 'swahili',
		'swe' => 'swedish',
		'tgl' => 'tagalog',
		'tur' => 'turkish',
		'ukr' => 'ukrainian',
		'urd' => 'urdu',
		'uzb' => 'uzbek',
		'vie' => 'vietnamese',
	);

	public static $code2ToCountry = array(
		'aa' => 'Unitag',
		'af' => 'Afghanistan',
		'ax' => 'Åland islands',
		'al' => 'Albania',
		'dz' => 'Algeria',
		'as' => 'American samoa',
		'ad' => 'Andorra',
		'ao' => 'Angola',
		'ai' => 'Anguilla',
		'aq' => 'Antarctica',
		'ag' => 'Antigua and barbuda',
		'ar' => 'Argentina',
		'am' => 'Armenia',
		'aw' => 'Aruba',
		'au' => 'Australia',
		'at' => 'Austria',
		'az' => 'Azerbaijan',
		'bs' => 'Bahamas',
		'bh' => 'Bahrain',
		'bd' => 'Bangladesh',
		'bb' => 'Barbados',
		'by' => 'Belarus',
		'be' => 'Belgium',
		'bz' => 'Belize',
		'bj' => 'Benin',
		'bm' => 'Bermuda',
		'bt' => 'Bhutan',
		'bo' => 'Bolivia',
		'bq' => 'Bonaire',
		'ba' => 'Bosnia and Herzegovina',
		'bw' => 'Botswana',
		'bv' => 'Bouvet island',
		'br' => 'Brazil',
		'io' => 'British indian ocean territory',
		'bn' => 'Brunei darussalam',
		'bg' => 'Bulgaria',
		'bf' => 'Burkina faso',
		'bi' => 'Burundi',
		'kh' => 'Cambodia',
		'cm' => 'Cameroon',
		'ca' => 'Canada',
		'cv' => 'Cape verde',
		'ky' => 'Cayman islands',
		'cf' => 'Central african republic',
		'td' => 'Chad',
		'cl' => 'Chile',
		'cn' => 'China',
		'cx' => 'Christmas island',
		'cc' => 'Cocos (keeling) islands',
		'co' => 'Colombia',
		'km' => 'Comoros',
		'cg' => 'Congo',
		'cd' => 'Congo',
		'ck' => 'Cook islands',
		'cr' => 'Costa rica',
		'ci' => 'Côte d\'ivoire',
		'hr' => 'Croatia',
		'cu' => 'Cuba',
		'cw' => 'Curaçao',
		'cy' => 'Cyprus',
		'cz' => 'Czech republic',
		'dk' => 'Denmark',
		'dj' => 'Djibouti',
		'dm' => 'Dominica',
		'do' => 'Dominican republic',
		'ec' => 'Ecuador',
		'eg' => 'Egypt',
		'sv' => 'El salvador',
		'gq' => 'Equatorial guinea',
		'er' => 'Eritrea',
		'ee' => 'Estonia',
		'et' => 'Ethiopia',
		'fk' => 'Falkland islands (malvinas)',
		'fo' => 'Faroe islands',
		'fj' => 'Fiji',
		'fi' => 'Finland',
		'fr' => 'France',
		'gf' => 'French guiana',
		'pf' => 'French polynesia',
		'tf' => 'French southern territories',
		'ga' => 'Gabon',
		'gm' => 'Gambia',
		'ge' => 'Georgia',
		'de' => 'Germany',
		'gh' => 'Ghana',
		'gi' => 'Gibraltar',
		'gr' => 'Greece',
		'gl' => 'Greenland',
		'gd' => 'Grenada',
		'gp' => 'Guadeloupe',
		'gu' => 'Guam',
		'gt' => 'Guatemala',
		'gg' => 'Guernsey',
		'gn' => 'Guinea',
		'gw' => 'Guinea-bissau',
		'gy' => 'Guyana',
		'ht' => 'Haiti',
		'hm' => 'Heard island and mcdonald islands',
		'va' => 'Holy see (vatican city state)',
		'hn' => 'Honduras',
		'hk' => 'Hong kong',
		'hu' => 'Hungary',
		'is' => 'Iceland',
		'in' => 'India',
		'id' => 'Indonesia',
		'ir' => 'Iran, islamic republic of',
		'iq' => 'Iraq',
		'ie' => 'Ireland',
		'im' => 'Isle of man',
		'il' => 'Israel',
		'it' => 'Italy',
		'jm' => 'Jamaica',
		'jp' => 'Japan',
		'je' => 'Jersey',
		'jo' => 'Jordan',
		'kz' => 'Kazakhstan',
		'ke' => 'Kenya',
		'ki' => 'Kiribati',
		'kp' => 'Korea, democratic people\'s republic of',
		'kr' => 'Korea, republic of',
		'kw' => 'Kuwait',
		'kg' => 'Kyrgyzstan',
		'la' => 'Lao people\'s democratic republic',
		'lv' => 'Latvia',
		'lb' => 'Lebanon',
		'ls' => 'Lesotho',
		'lr' => 'Liberia',
		'ly' => 'Libya',
		'li' => 'Liechtenstein',
		'lt' => 'Lithuania',
		'lu' => 'Luxembourg',
		'mo' => 'Macao',
		'mk' => 'Macedonia',
		'mg' => 'Madagascar',
		'mw' => 'Malawi',
		'my' => 'Malaysia',
		'mv' => 'Maldives',
		'ml' => 'Mali',
		'mt' => 'Malta',
		'mh' => 'Marshall islands',
		'mq' => 'Martinique',
		'mr' => 'Mauritania',
		'mu' => 'Mauritius',
		'yt' => 'Mayotte',
		'mx' => 'Mexico',
		'fm' => 'Micronesia',
		'md' => 'Moldova, republic of',
		'mc' => 'Monaco',
		'mn' => 'Mongolia',
		'me' => 'Montenegro',
		'ms' => 'Montserrat',
		'ma' => 'Morocco',
		'mz' => 'Mozambique',
		'mm' => 'Myanmar',
		'na' => 'Namibia',
		'nr' => 'Nauru',
		'np' => 'Nepal',
		'nl' => 'Netherlands',
		'nc' => 'New caledonia',
		'nz' => 'New zealand',
		'ni' => 'Nicaragua',
		'ne' => 'Niger',
		'ng' => 'Nigeria',
		'nu' => 'Niue',
		'nf' => 'Norfolk island',
		'mp' => 'Northern mariana islands',
		'no' => 'Norway',
		'om' => 'Oman',
		'pk' => 'Pakistan',
		'pw' => 'Palau',
		'ps' => 'Palestinian territory, occupied',
		'pa' => 'Panama',
		'pg' => 'Papua new guinea',
		'py' => 'Paraguay',
		'pe' => 'Peru',
		'ph' => 'Philippines',
		'pn' => 'Pitcairn',
		'pl' => 'Poland',
		'pt' => 'Portugal',
		'pr' => 'Puerto rico',
		'qa' => 'Qatar',
		're' => 'Réunion',
		'ro' => 'Romania',
		'ru' => 'Russian federation',
		'rw' => 'Rwanda',
		'bl' => 'Saint barthélemy',
		'sh' => 'Saint helena, ascension and tristan da cunha',
		'kn' => 'Saint kitts and nevis',
		'lc' => 'Saint lucia',
		'mf' => 'Saint martin (french part)',
		'pm' => 'Saint pierre and miquelon',
		'vc' => 'Saint vincent and the grenadines',
		'ws' => 'Samoa',
		'sm' => 'San marino',
		'st' => 'Sao tome and principe',
		'sa' => 'Saudi arabia',
		'sn' => 'Senegal',
		'rs' => 'Serbia',
		'sc' => 'Seychelles',
		'sl' => 'Sierra leone',
		'sg' => 'Singapore',
		'sx' => 'Sint maarten (dutch part)',
		'sk' => 'Slovakia',
		'si' => 'Slovenia',
		'sb' => 'Solomon islands',
		'so' => 'Somalia',
		'za' => 'South africa',
		'gs' => 'South georgia and the south sandwich islands',
		'ss' => 'South sudan',
		'es' => 'Spain',
		'lk' => 'Sri lanka',
		'sd' => 'Sudan',
		'sr' => 'Suriname',
		'sj' => 'Svalbard and jan mayen',
		'sz' => 'Swaziland',
		'se' => 'Sweden',
		'ch' => 'Switzerland',
		'sy' => 'Syrian arab republic',
		'tw' => 'Taiwan, province of China',
		'tj' => 'Tajikistan',
		'tz' => 'Tanzania, united republic of',
		'th' => 'Thailand',
		'tl' => 'Timor-leste',
		'tg' => 'Togo',
		'tk' => 'Tokelau',
		'to' => 'Tonga',
		'tt' => 'Trinidad and Tobago',
		'tn' => 'Tunisia',
		'tr' => 'Turkey',
		'tm' => 'Turkmenistan',
		'tc' => 'Turks and Caicos Islands',
		'tv' => 'Tuvalu',
		'ug' => 'Uganda',
		'ua' => 'Ukraine',
		'ae' => 'United arab emirates',
		'gb' => 'United kingdom',
		'us' => 'United states',
		'um' => 'United states minor outlying islands',
		'uy' => 'Uruguay',
		'uz' => 'Uzbekistan',
		'vu' => 'Vanuatu',
		've' => 'Venezuela, bolivarian republic of',
		'vn' => 'Viet nam',
		'vg' => 'Virgin islands, british',
		'vi' => 'Virgin islands, u.s.',
		'wf' => 'Wallis and futuna',
		'eh' => 'Western sahara',
		'xx' => '',
		'ye' => 'Yemen',
		// There is no latin country, but transifex has named the lang so...
		'yu' => 'Latin',
		'za' => 'South Africa',
		'zm' => 'Zambia',
		'zw' => 'Zimbabwe',
	);

	/**
	 * Returns the 2-letter ISO 639-1 code for the given language name.
	 *
	 * @param   string  $lang  English language name like "swedish"
	 *
	 * @return string Two-letter language code (e.g. "sv") or NULL if not found
	 */
	public static function nameToCode2($lang)
	{
		$lang = strtolower($lang);

		if (!isset(self::$nameToCode2[$lang]))
		{
			return null;
		}

		return self::$nameToCode2[$lang];
	}

	/**
	 * Returns the 3-letter ISO 639-2 code for the given language name.
	 *
	 * @param   string  $lang  English language name like "swedish"
	 *
	 * @return string Three-letter language code (e.g. "swe") or NULL if not found
	 */
	public static function nameToCode3($lang)
	{
		$lang = strtolower($lang);

		if (!isset(self::$nameToCode3[$lang]))
		{
			return null;
		}

		return self::$nameToCode3[$lang];
	}

	/**
	 * Returns the language name for the given 2-letter ISO 639-1 code.
	 *
	 * @param   string  $code  Two-letter language code (e.g. "sv")
	 *
	 * @return string English language name like "swedish"
	 */
	public static function code2ToName($code)
	{
		$lang = strtolower($code);

		if (!isset(self::$code2ToName[$lang]))
		{
			return null;
		}

		return self::$code2ToName[$lang];
	}

	/**
	 * Returns the language name for the given 3-letter ISO 639-2 code.
	 *
	 * @param   string  $code  Three-letter language code (e.g. "swe")
	 *
	 * @return string English language name like "swedish"
	 */
	public static function code3ToName($code)
	{
		$lang = strtolower($code);

		if (!isset(self::$code3ToName[$lang]))
		{
			return null;
		}

		return self::$code3ToName[$lang];
	}


	/**
	 * Returns the country name for the given 2-letter ISO 3166-1-alpha-2 code.
	 *
	 * @param   string  $code  Two-letter language code (e.g. "bg")
	 *
	 * @return string English language name like "swedish"
	 */
	public static function code2ToCountry($code)
	{
		$lang = strtolower($code);

		if (!isset(self::$code2ToCountry[$lang]))
		{
			return null;
		}

		return self::$code2ToCountry[$lang];
	}
}
