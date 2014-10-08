<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Layout extends CI_Controller {

	/***********************************************************************

		// [快速模板]
		// $isbuild           0: 不生成html   1: 生成
		// $isoutput          0: 不顯示畫面   1: 顯示畫面
		public function html_name($isbuild = 0, $isoutput = 1)
		{
			$param->view     = __FUNCTION__;
			$param->data     = NULL;
			$param->isbuild  = $isbuild;
			$param->isoutput = $isoutput;

			return $this->get_view($param);
		}

	************************************************************************/

	// 專案生成的基本位置
	protected $project_base;
	
	// 存放生成的專案目錄
	protected $project_dir;
	
	// 供顯示生成文件列表
	protected $bulid_table;

	// 複製額外的檔案路徑
	protected $copymap;

	function __construct()
	{
		parent::__construct();
		$this->setting();
	}


	//首頁
	public function index($isbuild = 0, $isoutput = 1)
	{
		$param->view     = "index";
		$param->data     = NULL;
		$param->isbuild  = $isbuild;
		$param->isoutput = $isoutput;

		return $this->get_view($param);
	}

	//聯絡我們
	public function contact($isbuild = 0, $isoutput = 1)
	{
		$param->view     = "contact";
		$param->data     = NULL;
		$param->isbuild  = $isbuild;
		$param->isoutput = $isoutput;

		return $this->get_view($param);
	}

	//最新消息
	public function news($isbuild = 0, $isoutput = 1)
	{
		$param->view     = "news";
		$param->data     = NULL;
		$param->isbuild  = $isbuild;
		$param->isoutput = $isoutput;

		return $this->get_view($param);
	}

	
	

	/**
	 * 運行生成專案
	 * 
	 * @param   $status   (必) display 顯示 | save 強制下載
	 */
	public function project($status)
	{
		try
		{
			if ($status != "display" and $status != "save") 
			{
				throw new Exception("請指定參數 display | save");
			} 

			$class_name  				= 	strtolower(__CLASS__);

			//反射本類別
			$PublicList  				= 	$this->public_method($class_name);
			
			//過濾其他條件
			$RunList 					= 	$this->file_public($class_name, __FUNCTION__, $PublicList);

			if (count($RunList[$class_name]) == 0) 
			{
				throw new Exception("沒有要製作的檔案");
			}

			//遍歷有的 class, 運行生成html
			$this->each_call_method($RunList);
			
			//複製其他常用檔
			$this->copy_elsefile();

			//若顯示
			if ($status == "display") 		$this->display_success();

			//若製作zip壓縮檔，並強制下載
			else 							$this->dlzip();
		}
		catch(Exception $e)
		{
			$this->output->set_output("捕獲到例外訊息：" . $e->getMessage());
		}
	}

	// HHHHHHHHH     HHHHHHHHHEEEEEEEEEEEEEEEEEEEEEELLLLLLLLLLL             PPPPPPPPPPPPPPPPP   
	// H:::::::H     H:::::::HE::::::::::::::::::::EL:::::::::L             P::::::::::::::::P  
	// H:::::::H     H:::::::HE::::::::::::::::::::EL:::::::::L             P::::::PPPPPP:::::P 
	// HH::::::H     H::::::HHEE::::::EEEEEEEEE::::ELL:::::::LL             PP:::::P     P:::::P
	//   H:::::H     H:::::H    E:::::E       EEEEEE  L:::::L                 P::::P     P:::::P
	//   H:::::H     H:::::H    E:::::E               L:::::L                 P::::P     P:::::P
	//   H::::::HHHHH::::::H    E::::::EEEEEEEEEE     L:::::L                 P::::PPPPPP:::::P 
	//   H:::::::::::::::::H    E:::::::::::::::E     L:::::L                 P:::::::::::::PP  
	//   H:::::::::::::::::H    E:::::::::::::::E     L:::::L                 P::::PPPPPPPPP    
	//   H::::::HHHHH::::::H    E::::::EEEEEEEEEE     L:::::L                 P::::P            
	//   H:::::H     H:::::H    E:::::E               L:::::L                 P::::P            
	//   H:::::H     H:::::H    E:::::E       EEEEEE  L:::::L         LLLLLL  P::::P            
	// HH::::::H     H::::::HHEE::::::EEEEEEEE:::::ELL:::::::LLLLLLLLL:::::LPP::::::PP          
	// H:::::::H     H:::::::HE::::::::::::::::::::EL::::::::::::::::::::::LP::::::::P          
	// H:::::::H     H:::::::HE::::::::::::::::::::EL::::::::::::::::::::::LP::::::::P          
	// HHHHHHHHH     HHHHHHHHHEEEEEEEEEEEEEEEEEEEEEELLLLLLLLLLLLLLLLLLLLLLLLPPPPPPPPPP          

	//設定
	private function setting()
	{
		session_start();


		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->helper('string');
		$this->load->helper('file');
		$this->load->library('zip');
		
		$_SESSION['_layout_sess'] 		= 	"ci";
		$_SESSION['_layout_classname'] 	= 	__CLASS__;

		//添加想要複製的檔案或路徑，與 index.php 相同目錄算起
		$this->copymap[] = "images";
		$this->copymap[] = "css/FrontEnd";
		$this->copymap[] = "plugin/CSS";
		// $this->copymap[] = "plugin/FONT";
		// $this->copymap[] = "plugin/JS";
		
		$this->global_function();
	}

	//複製其他的檔案
	private function copy_elsefile()
	{

		// 先建立路徑，再複製檔案
		if (count($this->copymap) != 0) foreach ($this->copymap as $dir_file)
		{
			$dir_file = str_replace("\\", "/", $dir_file);
			
			rmkdir($this->project_dir . $dir_file);

			smartCopy(FCPATH . $dir_file, $this->project_dir . $dir_file);
		}

	}

	

	//指定class的公開方法
	private function public_method($class_name)
	{
		//反射本類別
		$relflection = new ReflectionClass($class_name);
		$PublicList  = $relflection->getMethods(ReflectionMethod::IS_PUBLIC);
		return $PublicList;
	}

	//還需要再過濾一些自訂的條件
	private function file_public($class_name, $self_method, $PublicList)
	{
		foreach ($PublicList as $PublicInfo)
		{
			$name   = $PublicInfo->name; //方法名稱

			$owner  = strtolower($PublicInfo->class); //所屬的類別轉小寫

			//跳過不屬於本類別
			if ($owner != $class_name) continue;
			
			//跳過建構子
			if ($name == "__construct") continue;

			//跳過自己方法
			if ($name == strtolower($self_method)) continue;

			//需要製作成html的陣列
			$RunList[$class_name][] = $name;
		}

		return $RunList;
	}

	//遍歷呼叫方法來執行
	private function each_call_method($RunList)
	{
		foreach ($RunList as $runclass => $MethodList)
		{
			// 遍歷該 class 的 function 
			foreach ($MethodList as $method_name)
			{
				$callback = array($runclass, $method_name);
				
				//需要夾帶參數
				call_user_func($callback, 1, 0);
			}
		}
	}

	//顯示成功訊息
	private function display_success()
	{
		$this

			->output

			->set_content_type("text/html; charset=utf-8")
			
			->append_output("生成 " . count($this->build_table) . " 筆 html。 <br>")
			
			->append_output("存放的路徑在：" . $this->project_dir . "<br>")

			->append_output("您可以直接在裡面做打開html做測試。 <br><hr>");

		foreach ($this->build_table as $li) $this->output->append_output($li);
		
	}

	//下載prject html壓縮檔
	private function dlzip()
	{
		//製作zip壓縮檔，並強制下載
		if ( !file_exists($this->project_dir))
		{
			throw new Exception("系統發生錯誤，無法找到生成的目錄");
		}

		//取一個下載的名稱
		$file = uniqid("bp");

		//讀入目錄
		$this->zip->read_dir($this->project_dir, false); 

		//刪除檔案
		delete_files($this->project_dir, true);
		rmdir($this->project_dir);
		rmdir($this->project_base);

		//下載
		$this->zip->download("{$file}.zip");
	}


	/**
	 * 放置到view並建立html檔
	 * 
	 * @param  $view       指定的view 
	 * @param  $data       傳遞給view的參數 
	 * @param  $isbuild    產出html嗎? 
	 * @param  $isoutput   輸出嗎? 
	 * 
	 * @return             返回 view 的字串
	 *
	 */
	private function get_view($param)
	{

		//若要直接建立html
		if ($param->isbuild == "1") 		
		{
			//變換CI路徑 為 html路徑
			$_SESSION['_layout_sess']   = 	"html";
			$get_html 					= 	$this->load->view("layout/{$param->view}", $param->data, true);

			//建立html
			$this->build($param->view, $get_html);

		}
		else
		{
			$get_html 					= 	$this->load->view("layout/{$param->view}", $param->data, true);
		}

		if ($param->isoutput == 1) 			$this->output->set_output($get_html);
		
		return $get_html;
	}

	//建立 html
	private function build($view, $data)
	{
		$timestamp          		=	date("Ymd");
		$base               		=   APPPATH . "views/build_projecsts_html/";
		$path               		=	$base . "{$timestamp}/";
		$file               		= 	$path . "{$view}.html";
		
		$this->project_base 		=   $base;
		$this->project_dir  		=   $path;

		if ( ! file_exists($base))
		{
			mkdir($base, 0755) or die("路徑無法建立：{$base}");
		}
		
		if ( ! file_exists($path))
		{
			mkdir($path, 0755) or die("路徑無法建立：{$path}");
		}


		$this->load->helper('file');

		write_file($file, $data) or die("檔案匯出失敗：{$file}");

		$this->build_table[] 		= 	"<div>{$view}.html</div>";
	}


	private function global_function()
	{
		/**
		 * 自動轉換連結
		 * @param   $link html模板連結或其他資源的連結
		 * @return        轉換後的連結
		 *
		 * 在view中的使用方式如同我們習慣CI的方法
		 * http://domain.com/layout/contact 寫做 <?=hlink("layout/contact")?>
		 * http://domain.com/images/img.jpg 寫做 <?=hlink("images/img.jpg")?>
		 *  
		 */
		function hlink($link = NULL)
		{
			$c = strtolower($_SESSION['_layout_classname']);

			//若填如 layout/article
			if (substr_count($link, "{$c}/") > 0)
			{
				if ($_SESSION['_layout_sess'] == "ci")
				{
					return site_url($link);
				}
				else
				{
					$link = str_replace("{$c}/", NULL, $link);

					$link = "{$link}.html";

					return $link;
				}
			}

			//都不填
			elseif (empty($link))
			{
				if ($_SESSION['_layout_sess'] == "ci")

				{
					return site_url($c);
				}
				else
				{
					return "index.html";
				}
			}

			//如多媒體或圖檔
			else
			{
				if ($_SESSION['_layout_sess'] == "ci")
				{
					$link         = site_url($link);

					//去除沒有用 .htaccess 所產生在網址的 index.php
					$remove_index = str_replace("index.php/", NULL, $link);
					
					return $remove_index;
				}
				else
				{
					return $link;
				}
			}
		}


		//擷取字串
		if ( !function_exists('WordLimit'))
		{
			function WordLimit ($String , $Num = 100 , $OverString = "...", $HTML_LIMIT = true , $Code = "UTF-8") {
					
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
		}


		/**
		 * 陣列洗牌
		 * 來源：http://php.net/manual/en/function.array-rand.php
		 * 
		 * @param   $arr 要洗牌的陣列
		 * @param   $num 數量	
		 * @return 		 返回陣列
		 */
		function array_random($arr, $num = 1) {
		    shuffle($arr);
		    
		    $r = array();
		    for ($i = 0; $i < $num; $i++) {
		        $r[] = $arr[$i];
		    }
		    return $num == 1 ? $r[0] : $r;
		}



		/** 
		 * 聰明的複製檔案或整個路徑
		 * 來源：http://tw2.php.net/manual/zh/function.copy.php
		 * 
	     * Copy file or folder from source to destination, it can do 
	     * recursive copy as well and is very smart 
	     * It recursively creates the dest file or directory path if there weren't exists 
	     * Situtaions : 
	     * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination 
	     * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it 
	     * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest      
	     * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest 
	     * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name 
	     * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name 
	     * @todo 
	     *     - Should have rollback technique so it can undo the copy when it wasn't successful 
	     *  - Auto destination technique should be possible to turn off 
	     *  - Supporting callback function 
	     *  - May prevent some issues on shared enviroments : http://us3.php.net/umask 
	     * @param $source //file or folder 
	     * @param $dest ///file or folder 
	     * @param $options //folderPermission,filePermission 
	     * @return boolean 
	     */ 
	    function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755)) 
	    { 
	        $result=false; 
	        
	        if (is_file($source)) { 
	            if ($dest[strlen($dest)-1]=='/') { 
	                if (!file_exists($dest)) { 
	                    cmfcDirectory::makeAll($dest,$options['folderPermission'],true); 
	                } 
	                $__dest=$dest."/".basename($source); 
	            } else { 
	                $__dest=$dest; 
	            } 
	            $result=copy($source, $__dest); 
	            chmod($__dest,$options['filePermission']); 
	            
	        } elseif(is_dir($source)) { 
	            if ($dest[strlen($dest)-1]=='/') { 
	                if ($source[strlen($source)-1]=='/') { 
	                    //Copy only contents 
	                } else { 
	                    //Change parent itself and its contents 
	                    $dest=$dest.basename($source); 
	                    @mkdir($dest); 
	                    chmod($dest,$options['filePermission']); 
	                } 
	            } else { 
	                if ($source[strlen($source)-1]=='/') { 
	                    //Copy parent directory with new name and all its content 
	                    @mkdir($dest,$options['folderPermission']); 
	                    chmod($dest,$options['filePermission']); 
	                } else { 
	                    //Copy parent directory with new name and all its content 
	                    @mkdir($dest,$options['folderPermission']); 
	                    chmod($dest,$options['filePermission']); 
	                } 
	            } 

	            $dirHandle=opendir($source); 
	            while($file=readdir($dirHandle)) 
	            { 
	                if($file!="." && $file!="..") 
	                { 
	                     if(!is_dir($source."/".$file)) { 
	                        $__dest=$dest."/".$file; 
	                    } else { 
	                        $__dest=$dest."/".$file; 
	                    } 
	                    //echo "$source/$file ||| $__dest<br />"; 
	                    $result=smartCopy($source."/".$file, $__dest, $options); 
	                } 
	            } 
	            closedir($dirHandle); 
	            
	        } else { 
	            $result=false; 
	        } 
	        return $result; 
	    } 



	    /**
	    * 遞迴建立路徑
	    * 來源：http://php.net/manual/zh/function.mkdir.php
	    * 
	    * Makes directory and returns BOOL(TRUE) if exists OR made.
	    *
	    * @param  $path Path name
	    * @return bool
	    */
	    function rmkdir($path, $mode = 0755) {
	        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
	        $e = explode("/", ltrim($path, "/"));
	        if(substr($path, 0, 1) == "/") {
	            $e[0] = "/".$e[0];
	        }
	        $c = count($e);
	        $cp = $e[0];
	        for($i = 1; $i < $c; $i++) {
	            if(!is_dir($cp) && !@mkdir($cp, $mode)) {
	                return false;
	            }
	            $cp .= "/".$e[$i];
	        }
	        return @mkdir($path, $mode);
	    }

	    //中文假文
	    function fake($num = 20, $num_rand_limit = 0, $over_string = "")
	    {
	    	$ary = array
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
			
			$string = NULL;
			while(true)
			{
				//打散陣列			
				$ary_rand = array_random($ary, count($ary));
				$string   .= implode("", $ary_rand);
				
				//假文總字數
				$num_fake = mb_strlen($string);
				
				//若字數不足
				if ($num_fake < $num) continue;
				
				//使用區間數量
				if ($num_rand_limit > 0)
				{
					$num =  rand($num, $num_rand_limit);
				}
				
				$string   = WordLimit($string, $num, $over_string);

				break;
			}

			return $string;
	    }
	    

	}
}

/* End of file layout.php */
/* Location: ./application/controllers/layout.php */