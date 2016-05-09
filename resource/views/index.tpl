<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/normalize.css">
	<!-- 新 Bootstrap 核心 CSS 文件 -->
	<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- 可选的Bootstrap主题文件（一般不用引入） -->
	<!-- <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"> -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<title><?php echo $title; ?></title>

<style type="text/css">
.tree{

}
.tree ul{
	list-style-type: none;
}
.tree ul i{
	width: 20px;
	height: 20px;
	line-height: 20px;
	display: inline-block;
	background-image:url("images/tree.png");
	background-repeat: no-repeat;
}
.i{
	width: 20px;
	height: 20px;
	line-height: 20px;
	display: inline-block;
	background-image:url("images/tree.png");
	background-repeat: no-repeat;
}
.tree .tree-line{
	padding-left: 20px;
	background: url(images/tree-line.png) repeat-y 0 0 transparent;
}

.tree ul a{
	height: 20px;
	vertical-align: top;
}
.tree .plus{
	background-position: -20px -20px;
}

.tree .minus{
	background-position: -40px -20px;
}

.tree .directory{
	background-position: -80px -20px;
}

.tree .switch{
    background-position: 0 -20px;
}

.tree .file{
	background-position: -100px -20px;
}
.display_none{
	display: none;
}
</style>
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-10">
			<div  class="tree">
			</div>
		</div>
		<div class="col-xs-7 col-md-7">
		</div>
	</div>
</div>
		<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script type="text/javascript">
		
		// 形式一
$(document).ready( function(){
	tree = $(".tree");
	tree.empty();
	tree.append(create_ul('name',is_dir(), 'id1'));
} );



function create_div (name, dir) {
	var div = $("<div></div>");
	var i_class = new Array();
	if(dir){
		i_class[0] = "plus";
		i_class[1] = "directory";
	} else {
		i_class[0] = "minus";
		i_class[1] = "file";
	}
	div.append($("<i></i>").addClass(i_class[0])).append($("<i></i>").addClass(i_class[1])).append($("<a></a>").attr({"href": '#'}).text(name));
	return div;
}


function create_ul (name, dir, id) {
	var div = $("<ul></ul>").addClass('tree-line').attr('id', id);;
	var i_len = Math.ceil(Math.random()*4);
	for(var i=0; i<i_len; i++) {
		div.append(create_li(name + '_' + i, is_dir(), id));
	}
	return div;
}

function create_li (name, dir, id) {
	var li = $("<li></li>");
	var div = create_div(name, dir);
	li.append(div);
	if(dir) {
		var ul =create_ul(name,is_dir(), id + dir).hide();
		var is_hidden = 1;
		div.children('a').click(function(event) {
			i = $(this).parent().children('i').eq(0);
			if(is_hidden) {
				ul.show();
				is_hidden = 0;
				i.removeClass('plus').addClass('minus');
			} else {
				ul.hide();
				is_hidden = 1;
				i.removeClass('minus').addClass('plus');
			}
		});
		li.append(ul);
	}
	return li;
}


function is_dir()
{
	return Math.ceil((Math.random()*10)) % 2
}
</script>
</body>
</html>