<html>
	<head>
		<title><?php echo $data['title'] ?></title>
	</head>
	<body>
		<header><?php echo $data['title'] ?></header>
		<p><?php echo $data['body'] ?></p>
		<ul>
			<?php foreach ($data['list'] as $item): ?>
				<li><?php echo $item ?></li>
			<?php endforeach ?>
		</ul>
		<footer><?php echo $data['footer'] ?></footer>
	</body>
</html>