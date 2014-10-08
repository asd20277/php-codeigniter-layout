php-codeigniter-layout
======================

使用 php 語言來建立你的 html 版面
<h2>請先閱讀</h2>
<p>
    &emsp;&emsp;這是基於 Codeigniter Framework 的一隻程式碼，可以利用我們習慣編輯view的方式來設計模板。在PHP上面先做測試與開發，或是用php語言判別顯示的html，等到最後完成開發了，再生成html非動態語言。範例中不使用Codeigniter的模板語言，強烈推薦使用php原生語言來做 if else 判斷，或是任何的迴圈設計。這方便所有的php程式設計師維護。<br>
</p>

<h2>安裝方式</h2>
<ul>

    <li>
        目前裡面有 『application/controllers/layout.php』 與 『application/views/layout/』，把他們複製到你的 Codeigniter 對應路徑底下。
    </li>

    <li>
        若你的 Codeigniter 沒有在 .htaccess 添加去除 index.php網址字串的設計，那麼就直接在網址打入『http://localhost/CI/index.php/layout』來查看範例。
    </li>

    <li>
        如果想要刪除網址的index.php字串，可以在根目錄新增 .htaccess 並寫入
        <pre>
            DirectoryIndex index.php
            RewriteEngine on
            RewriteCond $1 !^(index\.php|images|css|js|robots\.txt|favicon\.ico)
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ ./index.php/$1 [L,QSA] 
        </pre>
    </li>

</ul>


<h2>使用方法</h2>

<ul>
    <li>
        進入 setting() 設定 $this->copymap[] <br>
        選用添加要匯出的額外檔案路徑，例如你的圖片檔、js檔。<br>
        當你執行產出時，這將會複製一份到產出的模板路徑。<br>
    </li>  

    <li>
        我們依照 Codeigniter 編撰 view 的習慣來製作版面。<br>
        預設在瀏覽的時候，並不會自動生成 html，除非改變URL參數。<br>
        這樣不會生成一份 html： http://localhost/CI/layout/index <br>
        這樣會生成一份 html：http://localhost/CI/layout/index/1<br>
        可參考 layout.php 中的註解：[快速模板]
    </li>

    <li>
        最後，我們想要產出所有編輯好的文件，也就是 application/views/layout/* <br>
        只要在網址打上 http://localhost/CI/layout/project/save 就可以下載zip的封裝。<br>
        若只想匯出而不下載zip，<br>
        可在網址打上 http://localhost/CI/layout/project/display <br>
        就會在 views/build_projecsts_html 產出所有 html。
    </li>

</ul>
