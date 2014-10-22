<?
/**
 * 假文產生器
 */
class Jsnfakestr
{

    // 紀錄當前使用的語言如 ch
    public static $language;

	// 假文存放陣列
    // array("語言名稱" => array("中文", "文字"));
    // 如 array("ch" => array());
	public static $data;


	/**
	 * 擷取字串
	 * @param   $String     	放入字串
	 * @param   $Num        	要截取的數量
	 * @param   $OverString 	超過的字符
	 * @param   $HTML_LIMIT 	允許被保留的HTML標籤
	 * @param   $Code       	字串編碼
	 */
	public function word_limit ($String , $Num = 100 , $OverString = "...", $HTML_LIMIT = true , $Code = "UTF-8") 
	{
		if (is_string($HTML_LIMIT)) {
    		$String = str_replace(array("  ","&nbsp;","\t","\r"),array(" ",""),strip_tags($String,$HTML_LIMIT)) ;
		} else if ($HTML_LIMIT == false) {
			//不動
		} else if ($HTML_LIMIT == true) {
			//替換特殊字或多空格者
			$String = str_replace(array("  ","&nbsp;","\t","\r"),array(" ",""),strip_tags($String,"")) ;
		}
		
		$ASCII= array(1		=> 0.0, 2		=> 0.0, 3		=> 0.0, 4		=> 0.0, 5		=> 0.0, 6		=> 0.0, 7		=> 0.0, 8		=> 0.0,	 9	=> 0.0, 10	=> 0.0, 
								11	=> 0.0, 12	=> 0.0, 13	=> 0.0, 14	=> 0.0, 15	=> 0.0, 16	=> 0.0, 17	=> 0.0, 18	=> 0.0, 19	=> 0.0, 20	=> 0.0, 
								21	=> 0.0, 22	=> 0.0, 23	=> 0.0, 24	=> 0.0, 25	=> 0.0, 26	=> 0.0, 27	=> 0.0, 28	=> 0.0, 29	=> 0.0, 30	=> 0.0, 
								31	=> 0.0, 32	=> 0.0, 33	=> 0.3, 34	=> 0.3, 35	=> 0.6, 36	=> 0.5, 37	=> 0.9, 38	=> 0.7, 39	=> 0.2, 40	=> 0.3, 
								41	=> 0.3, 42	=> 0.3, 43	=> 0.3, 44	=> 0.3, 45	=> 0.3, 46	=> 0.3, 47	=> 0.3, 48	=> 0.55, 49	=> 0.55, 50	=> 0.55, 
								51	=> 0.55, 52	=> 0.55, 53	=> 0.55, 54	=> 0.55, 55	=> 0.55, 56	=> 0.55, 57	=> 0.55, 58	=> 0.6, 59	=> 0.6, 60	=> 0.6, 
								61	=> 0.6, 62	=> 0.6, 63	=> 0.6, 64	=> 0.6, 65	=> 0.65, 66	=> 0.65, 67	=> 0.65, 68	=> 0.65, 69	=> 0.65, 70	=> 0.65, 
								71	=> 0.65, 72	=> 0.65, 73	=> 0.3, 74	=> 0.3, 75	=> 0.65, 76	=> 0.65, 77	=> 0.65, 78	=> 0.65, 79	=> 0.65, 80	=> 0.65, 
								81	=> 0.65, 82	=> 0.65, 83	=> 0.65, 84	=> 0.65, 85	=> 0.65, 86	=> 0.65, 87	=> 0.65, 88	=> 0.65, 89	=> 0.65, 90	=> 0.65, 
								91	=> 0.6, 92	=> 0.6, 93	=> 0.6, 94	=> 0.6, 95	=> 0.6, 96	=> 0.6, 97	=> 0.6, 98	=> 0.6, 99	=> 0.6, 100	=> 0.6, 
								101	=> 0.6, 102	=> 0.6, 103	=> 0.6, 104	=> 0.6, 105	=> 0.6, 106	=> 0.6, 107	=> 0.6, 108	=> 0.6, 109	=> 0.6, 110	=> 0.6, 
								111	=> 0.6, 112	=> 0.6, 113	=> 0.6, 114	=> 0.6, 115	=> 0.6, 116	=> 0.6, 117	=> 0.6, 118	=> 0.6, 119	=> 0.6, 120	=> 0.6, 
								121	=> 0.6, 122	=> 0.6, 123	=> 0.6, 124	=> 0.6, 125	=> 0.6, 126	=> 0.6, 127	=> 0.6) ;
		
		$WordCounter = 0 ;
		$ReturnStr = "" ;
		$STR_LENGTH = mb_strlen($String,$Code) ;
		for ($CurrentPosition = 0 ; $CurrentPosition < $STR_LENGTH ; $CurrentPosition++) {
			$Word = mb_substr($String,$CurrentPosition,1,$Code) ;
			
			$WordOrd = ord($Word) ;
			
			if ($WordOrd < 32) continue ;
			$ReturnStr .= $Word ;
			
			if ($WordOrd < 128) {
				$WordCounter += $ASCII[$WordOrd] ;
			} else {
				$WordCounter++ ;
			}
			
			if ($WordCounter >= $Num) break ;
		}
		
		return $CurrentPosition < $STR_LENGTH ? $ReturnStr . $OverString : $String ;
	}

	/**
	 * 陣列洗牌
	 * 來源：http://php.net/manual/en/function.array-rand.php
	 * 
	 * @param   $arr 要洗牌的陣列
	 * @param   $num 數量	
	 * @return 		 返回陣列
	 */
	public function array_random($arr, $num = 1) 
	{
	    shuffle($arr);
	    
	    $r = array();

	    for ($i = 0; $i < $num; $i++) 
	    {
	        $r[] = $arr[$i];
	    }
	    
	    return $num == 1 ? $r[0] : $r;
	}

	/**
	 * 設定假文資料
	 * 
	 * @param  $array 若指定假文陣列，將使用使用者自訂。預設使用內置假文
	 */
	public static function set_data($language = NULL, $array = NULL)
	{
        $data['ch'] = array
        (
            "我要給阿Ｑ做正傳，",
            "已經不止一兩年了。",
            "但一面要做，",
            "一面又往回想，",
            "這足見我不是一個「立言」的人，",
            "因為從來不朽之筆，",
            "須傳不朽之人，",
            "於是人以文傳－－",
            "文以人傳——究竟誰靠誰傳，",
            "漸漸的不甚瞭然起來，",
            "而終於歸接到傳阿Ｑ，",
            "彷彿思想裡有鬼似的。",
            "然而要做這一篇速朽的文章，",
            "才下筆，",
            "便感到萬分的困難了。",
            "第一是文章的名目。",
            "孔子曰，",
            "「名不正則言不順」。",
            "這原是應該極註意的。",
            "傳的名目很繁多：",
            "列傳，",
            "自傳，",
            "內傳，",
            "外傳，",
            "別傳，",
            "家傳，",
            "小傳……",
            "而可惜都不合。",
            "阿Ｑ並沒有抗辯他確鑿姓趙，",
            "只用手摸著左頰，",
            "和地保退出去了；",
            "外面又被地保訓斥了一番，",
            "謝了地保二百文酒錢。",
            "知道的人都說阿Ｑ太荒唐，",
            "自己去招打；",
            "他大約未必姓趙，",
            "即使真姓趙，",
            "有趙太爺在這裡，",
            "也不該如此胡說的。",
            "此後便再沒有人提起他的氏族來，",
            "以我終於不知道阿Ｑ究竟什麼姓。",
            "我又不知道阿Ｑ的名字是怎麼寫的。",
            "他活著的時候，",
            "人都叫他阿Ｑｕｅｉ，",
            "死了以後，",
            "便沒有一個人再叫阿Ｑｕｅｉ了，",
            "那裡還會有「著之竹帛」的事。",
            "若論「著之竹帛」，",
            "這篇文章要算第一次，",
            "所以先遇著了這第一個難關。",
            "我曾仔細想：",
            "阿Ｑｕｅｉ，",
            "阿桂還是阿貴呢？",
            "倘使他號月亭，",
            "或者在八月間做過生日，",
            "那一定是阿桂了；",
        );

    
        $data['en'] = array
        (
            "In November, ", 
            "1805, Prince Vasili had to go on a tour of inspection in four different provinces. ", 
            "He had arranged this for himself so as to visit his neglected estates at the same time and pick up his son Anatole where his regiment was stationed,",
            "and take him to visit Prince Nicholas Bolkonski in order to arrange a match for him with the daughter of that rich old man.", 
            "But before leaving home and undertaking these new affairs, ", 
            "Prince Vasili had to settle matters with Pierre, who, it is true, ",
            "had latterly spent whole days at home, ",
            "that is, in Prince Vasili's house where he was staying, ",
            "and had been absurd, excited, and foolish in Helene's presence (as a lover should be), ",
            "but had not yet proposed to her. ",
            "This is all very fine, but things must be settled, ",
            "said Prince Vasili to himself, with a sorrowful sigh, one morning, ",
            "feeling that Pierre who was under such obligations to him ('But never mind that') was not behaving very well in this matter. ",
            "'Youth, frivolity... well, God be with him, '",
            "thought he, relishing his own goodness of heart,",
        );
        
        if ($language == "en" or $language == "ch") 
        {
            $ary = $data[$language];
        }
        else
        {
            $ary = array();
            
            // 把第二層陣列全部放到第一層陣列
            foreach ($data as $child_ary) foreach ($child_ary as $val)
            {
                array_push($ary, $val);
            }   
        }

		self::$data[$language] = $ary;

	}

    // 設定假文資料
    public static function lang($language)
    {
        //設定語言資料並記錄起來
        self::set_data($language);

        self::$language = $language;

        return new Jsnfakestr;
    }


	/**
	 * 產生假文
	 * 
	 * @param  $num            總數量。但若指定第二個參數 $num_rand_limit 代表最少的字串。
	 * @param  $num_rand_limit 最多的字串。預設0代表不使用，所以第一個參數 $num 將代表總數量。
	 * @param  $over_string    超過的字符
	 */
    public function create($num = 20, $num_rand_limit = 0, $over_string = "")
    {
    	//字串編碼
        $strcode = "utf-8";
        
        $string  = NULL;

        // 如果沒指定語言
        if (empty(self::$data[self::$language])) 
        {
            self::set_data();
            // array_merge(arr1, arr2)
        }
        
        $ary     = self::$data[self::$language];

		while(true)
		{
			//打散陣列			
            $ary_rand = self::array_random($ary, count($ary));
            
            $string   .= implode("", $ary_rand);
            
            //假文總字數
            $num_fake = mb_strlen($string, $strcode);

			//若字數不足
			if ($num_rand_limit > 0)
			{
				if ($num_fake < $num_rand_limit) continue;
			}
			else
			{
				if ($num_fake < $num) continue;
			}
			
			//使用區間數量
			if ($num_rand_limit > 0)
			{
				$num  =  rand($num, $num_rand_limit);
			}
			
            //如果是英文就 / 2
            if (self::$language == "en") $num = $num / 2;

			$string   = self::word_limit($string, $num, $over_string, true, $strcode);

			break;
		}

		return $string;
    }


}
