php-codeigniter-layout
======================


<html>
<head>
</head>
<body>
<p>&emsp;&emsp;這是基於 Codeigniter Framework 的一隻程式碼，可以利用我們習慣在 Controller 與 View 的地方來設計模板。</p>
<h2>特色</h2>
<ul>
<li>用 php 的判斷與 loop 來寫你的 html。例如 &lt;span&gt;&lt;?=$string?&gt;&lt;/span&gt;。</li>
<li>支援繁體中文的假文字串，讓你規劃版面更真實。</li>
<li>可產出純 html 檔給不懂 php 的夥伴。</li>
</ul>
<h2>工作流程</h2>
<ol>
<li>在PHP上面先做測試與開發。也就是你會利用控制器 『http://localhost/CI/layout/你的頁面』&nbsp;在做編輯。</li>
<li>完成開發後，可以批次生成html非動態語言。或是壓縮打包為Zip。</li>
<li>範例中不推薦Codeigniter的模板語言，當然你習慣了也是可以。只是這麼推廣是為了方便所有的php程式設計師維護。盡量使用如<br />
<pre>&lt;? $i = 0; while($i++&lt; 5) { ?&gt;<br />&nbsp; &nbsp; &lt;span&gt;次數：&lt;?=$i?&gt;&lt;/span&gt;<br />&lt;? } ?&gt;
</pre>
</li>
</ol>
<h2>未來要做的事情...</h2>
<ul>
<li>錄影來教學囉</li>
</ul>
<h2>安裝方式</h2>
<ul>
<li>目前裡面有 『application/controllers/layout.php』 與 『application/views/layout/』，把他們複製到你的 Codeigniter 對應路徑底下。</li>
<li>再把擴充程式庫『application/libraries/jsnclass/jsnfakestr』複製到你的 Codeigniter 對應路徑底下。</li>
<li>若你的 Codeigniter 沒有在 .htaccess 添加去除 index.php 網址字串的設計，那麼就直接在網址打入『http://localhost/CI/index.php/layout』來查看範例。</li>
<li>
</li>
</ul>
<h2>開發方法</h2>
<ul>
<li>如果想要刪除網址的index.php字串，可以在根目錄新增 .htaccess 並寫入
<pre>            DirectoryIndex index.php
            RewriteEngine on
            RewriteCond $1 !^(index\.php|images|css|js|robots\.txt|favicon\.ico)
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ ./index.php/$1 [L,QSA] </pre>
</li>
<li>進入 setting() 設定 $this-&gt;copymap[] 選用添加要匯出的額外檔案路徑，例如你的圖片檔、js檔。當你執行產出時，這將會複製一份到產出的模板路徑。</li>
<li>我們依照 Codeigniter 編撰 view 的習慣來製作版面。<br />『http://localhost/CI/layout/index』代表首頁，但不會生成 html。<br />『http://localhost/CI/layout/index/1』這樣會生成 html ，並自動匯出在你的 『application/views』。<br />詳細可參考 layout.php 中的註解：[快速模板]。</li>
<li>假如我們想要一次產出所有編輯好的文件，只要在網址打上 『http://localhost/CI/layout/project/save 』就可以下載zip的封裝；若只想匯出而不下載zip， 可在網址打上 『http://localhost/CI/layout/project/display』 就會在 『application/views/build_projecsts_html』 產出所有 html。<br />vgs</li>
</ul>
</body>
</html>