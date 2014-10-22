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

		//引入假文系統
		include_once(APPPATH . "libraries/jsnclass/jsnfakestr/jsnfakestr.php");

		//報錯
		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

		$_SESSION['_layout_sess'] 		= 	"ci";
		$_SESSION['_layout_classname'] 	= 	__CLASS__;

		//添加想要複製的檔案或路徑，與 index.php 相同目錄算起
		$this->copymap[] = "images";
		$this->copymap[] = "css/FrontEnd";
		$this->copymap[] = "plugin/CSS";
		$this->copymap[] = "plugin/FONT";
		$this->copymap[] = "plugin/JS";

		//使用自訂義的方法
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

	    
	    

	}
}

/* End of file layout.php */
/* Location: ./application/controllers/layout.php */
