		<div class="span2">
			<ul class="well nav nav-pills nav-stacked">
				<li {if $cur_left == 'managestation'} class="active"{/if}><a href="/chadmin/ManageStation.php">城市站点管理</a></li>
				<li {if $cur_left == 'trainsearch'} class="active"{/if}><a href="/chadmin/trainsearch.php">列车数据采集</a></li>
				<li {if $cur_left == 'trainsearch_manage'} class="active"{/if}><a href="/chadmin/trainsearch.php?do=manage">列车采集数据管理</a></li>
			</ul>
		</div>