<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>首頁</title>
</head>
<body>
	<table border="1">
		<tr>
			<td>連結到其他模板的網址</td>
			<td><?=hlink("layout/contact")?></td>
		</tr>
		<tr>
			<td>空連結</td>
			<td><?=hlink()?></td>
		</tr>
		
		<tr>
			<td>圖檔</td>
			<td><?=hlink("images/about.png")?></td>
		</tr>
	</table>
	

	<img src="<?=hlink("images/about.png")?>" alt="">

	<ul>
		<li><a href="<?=hlink()?>">首頁</a></li>
		<li><a href="<?=hlink("layout/news")?>">最新消息</a></li>
		<li><a href="<?=hlink("layout/contact")?>">聯絡我們</a></li>
	</ul>
	

	<section>
		<? $Jsnfakestr = new Jsnfakestr; ?>
		<? $a=0; while($a++<5) { ?>
			<article>
				<h2><?=$Jsnfakestr->create(10, 20)?></h2>
				<p><?=$Jsnfakestr->create(400, 800)?></p>
			</article>
		<? } ?>

	</section>
</body>
</html>