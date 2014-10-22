<?
include_once "jsnfakestr.php";

// 自動
		echo jsnfakestr::create(50);
		echo "<br>";

//中文
		echo jsnfakestr::lang("ch")->create(10, 50, "...");
		echo "<br>";


//英文
		echo jsnfakestr::lang("en")->create(40);
		echo "<br>";



// --------- 附加方法 --------- 


//[截字串]

		echo Jsnfakestr::word_limit("繁體假文輸出", "3", ".......[更多]");

		//--------------------------------
		echo "<br><br>";
		//--------------------------------


//[陣列洗牌]
		$array = array
		(
			"星期一", "星期二","星期三",
			"星期四","星期五","星期六",
			"星期日"
		);
		$new_array = Jsnfakestr::array_random($array, 2);
		var_dump($new_array);
