<?
include_once "jsnfakestr.php";

$array = array
(
	"星期一", "星期二","星期三",
	"星期四","星期五","星期六",
	"星期日"
);

//[手動產生假文]

		//一般的方法
		// $jsnfakestr = new jsnfakestr;
		// echo $jsnfakestr->set_data($array)->create(10, 20, "......");

		//較新的寫法
		echo (new Jsnfakestr)->set_data($array)->create(10, 20, "......");

		//--------------------------------
		echo "<br><br>";
		//--------------------------------


//[使用預設假文]

		//一般的方法
		// $jsnfakestr = new jsnfakestr;
		// echo $jsnfakestr->create(10, 20, "......");

		//較新的方法
		echo (new Jsnfakestr)->create(10, 20, "......");

		//--------------------------------
		echo "<br><br>";
		//--------------------------------


// --------- 附加方法 --------- 


//[截字串]

		echo Jsnfakestr::word_limit("繁體假文輸出", "3", ".......[更多]");

		//--------------------------------
		echo "<br><br>";
		//--------------------------------


//[陣列洗牌]

		$new_array = Jsnfakestr::array_random($array, 2);
		var_dump($new_array);
