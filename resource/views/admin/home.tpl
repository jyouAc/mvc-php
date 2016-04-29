<!DOCTYPE html>
<html>
<head>
	<title>{$title}</title>
	{!js/index.js}
</head>
<body>
	{$body}
	<table>
		<ul>
			<li>title:</li>
			<li>body</li>
		</ul>

			@foreach ($pages as  $page)
				<ul>
					<li> {$page['title']}</li>
					<li>{$page['body']}</li>
				</ul>
			@endforeach
	</table>

</body>
</html>