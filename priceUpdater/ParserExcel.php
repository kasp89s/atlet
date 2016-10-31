<?php
require "PHPExcel/Classes/PHPExcel.php";
ini_set("memory_limit","256M");
class ParserExcel
{
    protected $file;

	public static $_correctISG = array(
	'102' => '5822',
	'128' => '1930',
	'129' => '2015',
	'151' => '5818',
	'159' => '11129',
	'414' => '8909',
	'429' => '1176',
	'429' => '1987',
	'440' => '3870',
	'441' => '3866',
	'442' => '3868',
	'443' => '3865',
	'444' => '3867',
	'445' => '3863',
	'446' => '3869',
	'447' => '3864',
	'450' => '1988',
	'607' => '1368',
	'731' => '8127',
	'732' => '8125',
	'735' => '8129',
	'736' => '8131',
	'737' => '9199',
	'1593' => '11251',
	'1593' => '11337',
	'1967' => '1925',
	'2811' => '4532',
	'2869' => '3704',
	'2870' => '3703',
	'2897' => '12497',
	'2898' => '12495',
	'2899' => '11783',
	'2900' => '11779',
	'2902' => '5838',
	'2916' => '12491',
	'2924' => '12493',
	'2929' => '12499',
	'2952' => '1995',
	'2953' => '992',
	'2954' => '967',
	'2955' => '1746',
	'3602' => '5837',
	'4555' => '1996',
	'4559' => '5199',
	'4563' => '8107',
	'4564' => '7487',
	'4565' => '8907',
	'4566' => '7489',
	'4669' => '1855',
	'5191' => '6799',
	'5217' => '4776',
	'5221' => '4777',
	'5248' => '6801',
	'5788' => '6805',
	'5791' => '6803',
	'56090' => '8035',
	'100' => '1585',
	'10099,795' => '743',
	'10100.794' => '748',
	'10105,795' => '745',
	'10106,793' => '749',
	'10200,793' => '1254',
	'10201,793' => '1082',
	'10207,793' => '1083',
	'10223.795' => '8133',
	'10228,793' => '1093',
	'10297.795' => '1086',
	'102-FDM' => '5819',
	'10429.795' => '1087',
	'10433,793' => '1088',
	'105' => '950',
	'10553,793' => '3810',
	'110' => '733',
	'119' => '2020',
	'122' => '2019',
	'138' => '2010',
	'14' => '1714',
	'141' => '2011',
	'142' => '1933',
	'144' => '1931',
	'145' => '1078',
	'146' => '1076',
	'147' => '1077',
	'148' => '2012',
	'20183,793' => '1605',
	'20185,793' => '1606',
	'20210,793' => '1085',
	'20270.795' => '1598',
	'2486' => '2628',
	'2537' => '1792',
	'2671' => '1748',
	'310' => '734',
	'56' => '5866',
	'602' => '1237',
	'680' => '987',
	'N07081' => '8041',
	'N07082' => '8037',
	'N07083' => '8039',
	'N08021' => '8043',
	'N08022' => '8047',
	'N08023' => '8045',
	'N08051' => '8053',
	'N08052' => '8049',
	'N08053' => '8051',
	'N43251' => '8063',
	'N43252' => '8061',
	'N50500' => '8033',
	'N60180' => '8031',
	'N61180' => '8059',
	'N90060' => '8065',
	'N93251' => '8057',
	'N93252' => '8055',
	);
    public function __construct($file)
    {
        $this->file = $file;
    }
    public function getArray()
    {
        $PHPExcel_file = PHPExcel_IOFactory::load($this->file);
        $ar = $PHPExcel_file->setActiveSheetIndex(0)->toArray(); // выгружаем данные из объекта в массив
        $result = array();
        $resultAll = array();
        foreach ($ar as $row) {
            if(is_numeric($row[0]) && $row[1]) {
                $result['article'] = $row[0];
                $result['section'] = $row[1];
                $result['name'] = $row[2];
                $result['roznica'] = $row[3];
                $result['opt'] = $row[4];
                $result['skidka'] = $row[5];
                $result['nacenka'] = $row[6];
                $result['chtyki'] = $row[7];
                $result['naliche'] = $row[8];
                $resultAll[] = $result;
            }
        }
        return $resultAll;
    }

    public function getArray2()
    {
        $PHPExcel_file = PHPExcel_IOFactory::load($this->file);
        $ar = $PHPExcel_file->setActiveSheetIndex(0)->toArray(); // выгружаем данные из объекта в массив
        $result = array();
        $resultAll = array();
        foreach ($ar as $row) {
            if(is_numeric($row[0]) && $row[1]) {
                $result['article'] = str_replace('475', '', $row[0]);
                $result['name'] = $row[1];
                $result['count'] = $row[2];
                $result['price'] = (int) str_replace(',', '', $row[3]);
                $resultAll[] = $result;
            }
        }
        return $resultAll;
    }

    public function getArray3()
    {
        $PHPExcel_file = PHPExcel_IOFactory::load($this->file);
        $ar = $PHPExcel_file->setActiveSheetIndex(0)->toArray(); // выгружаем данные из объекта в массив
        $result = array();
        $resultAll = array();
        foreach ($ar as $row) {
            if(is_numeric($row[2])) {
                $result['article'] = str_replace('475', '', $row[2]);
                $result['name'] = $row[3];
                $result['unit'] = $row[4];
                $result['count'] = (int) str_replace(',', '', $row[8]);
                $result['cost'] = $row[9];
                $result['sell'] = $row[11];
                $resultAll[] = $result;
            }
        }
        return $resultAll;
    }
	
	public function getIsg()
	{
		$PHPExcel_file = PHPExcel_IOFactory::load($this->file);
        $ar = $PHPExcel_file->setActiveSheetIndex(0)->toArray(); // выгружаем данные из объекта в массив
        $result = array();
        $resultAll = array();
		
		if(!is_numeric($ar[0][0])) {
			echo "Не указан курс доллара!";
			exit;
		}
		$course = (float) $ar[0][0];

        foreach ($ar as $row) {
            if(isset(self::$_correctISG[$row[2]])) {
                $result['article'] = self::$_correctISG[$row[2]];
                $result['name'] = $row[1];
                $result['cost'] = ceil((float) $row[3] * $course);
                $result['sell'] = ceil((float) $row[4] * $course);
                $resultAll[] = $result;
            }
        }
		return $resultAll;
	}
}

function calculateMinPrice($cost, $sell) {
    if ($cost < 500) {
        return $sell + 100;

    }
    if ($cost > 500 && $cost < 1000) {
        return $cost + 150;
    }

    if ($cost > 1000 && $cost < 2000) {
        return $cost + 200;
    }

    if ($cost > 2000 && $cost < 3000) {
        return $cost + 300;
    }

    if ($cost > 3000) {
        return $cost + 400;
    }
}
