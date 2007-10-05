<?php

if (!is_array($pages) || count($pages) == 0) {
	return false;
}

$request = implode('/', $request);
$request = eregi_replace('\/page[0-9]+\/?', '', $request);
$request = $request.'/page%d/';

?>

<div class="pages">
	<ul>
		<li>

<?php

if ($currentPage != 1) {

?>
			
			<a href="<?=sprintf($request, ($currentPage - 1))?>">&laquo;</a>

<?php

} else {
	echo '<a class="off">&laquo;</a>';
}
?>
		</li>
<?php
foreach ($pages as $page) {
	if (!is_array($page)) {
		echo '<li class="sep">...</li>';
		continue;
	}
	if (!isset($page['current'])) {
		echo '<li>';
		echo '<a href="'.sprintf($request, $page['pageno']).'">';
		echo $page['pageno'];
		echo '</a>';
		echo '</li>';
	} else {
		echo '<li class="current"><a class="off">'.$page['pageno'].'</a></li>';
	}
}
?>
		<li>
<?php
if ($currentPage != $maxPage) {
?>
			<a href="<?=sprintf($request, ($currentPage + 1))?>">&raquo;</a>
<?php
} else {
	echo '<a class="off">&raquo;</a>';
}
?>
		</li>
	</ul>
</div> <!-- pages -->