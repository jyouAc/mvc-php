<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title; ?></title>
	<script src = 'js/index.js' type="text/javascript"> </script>
</head>
<body>
	<?php echo $body; ?>
	<table>
		<ul>
			<li>title:</li>
			<li>body</li>
		</ul>

			<?php foreach ($pages as $page ) {?>
				<ul>
					<li> <?php echo $page['title']; ?></li>
					<li><?php echo $page['body']; ?></li>
				</ul>
			<?php }?>
	</table>

</body>
</html>