<!-- BEGIN: main --> <div id="searchcontent"> <div class="box-border m-bottom"> 	<div class="cat-box-header"> 		<div class="cat-nav"> 			<strong>VIDEO</strong>		</div>	</div>	<!-- BEGIN: noaccess -->		<p style="width:100%;float:left;" align="center"><strong>{LANG.you_must} <a href="{USER_LOGIN}">{LANG.loginsubmit}</a> / <a href="{USER_REGISTER}">{LANG.register}</a> {LANG.to_access}</strong></p>	<!-- END: noaccess -->	<!-- BEGIN: access -->	<!-- BEGIN: edit -->		<!-- BEGIN: ok -->			<div class="sucess">				<p>{LANG.playlist_edit_sucess}<a href="{url_play}"> <strong>{LANG.playlist_edit_sucess1} </strong></a>{LANG.song_edit_listen} <a href="{url_back}"><strong> {LANG.playlist_edit_sucess3}</strong></a> {LANG.song_edit_back}</p>			</div>		<!-- END: ok -->		<form method="post" style="text-align:right;padding-right:10px;"">			<br /><label>{LANG.song_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>			<input style="width:350px;" maxlength="50" type="text" name="name" value="{SONG.tenthat}" /><br /><br />			<label>{LANG.singer}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>			<select name="singer" style="width:200px;">				{SINGER}				</select><br /><br />			<label> {LANG.upload_quicksinger}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>			<input style="width:350px;" maxlength="50" type="text" name="newsinger" /><br /><br />			<label>{LANG.category_2}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>			<select name="theloai" style="width:200px;">				{CATEGORY}			</select><br /><br />			<input type="hidden" name="ok" value="1" />			<input type="submit" value="OK" /><br />		</form>	<!-- END: edit -->	<div class="topsong_container">	<div class="topsong_content">	<!-- BEGIN: song -->	<!-- BEGIN: noacept -->		<div id="song{id}" class="songitem">			<a class="songname" title="{name}">{name}</a>				<div class="tools">								<a class="del" onclick="delsong('{id}');" title="{LANG.song_del}"></a>					<a style="float: right;padding-top:4px;">{LANG.upload_wait_accept} ... </a>				</div>			<p>				{LANG.show}: <a class="singer" href="{url_search_singer}">{singer}</a><br />				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{LANG.category_2}:  				<a class="singer" href="{url_search_category}">{category}</a> | {LANG.view}: {view}	| {bitrate}kb/s | {duration} | {size} MB			</p>		</div>	<!-- END: noacept -->	<!-- BEGIN: acept -->		<div id="song{id}" class="songitem">			<a class="songname" title="{name}" href="{url_view}">{name}</a>				<div class="tools">					<a class="del" onclick="delsong('{id}');" title="{LANG.song_del}"></a>					<a href="{url_edit}" class="edit" title="{LANG.song_edit}"></a>					<a href="{url_view}" class="play" title="{LANG.song_listen}"></a>				</div>			<p>				{LANG.show}: <a class="singer" href="{url_search_singer}">{singer}</a><br />				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{LANG.category_2}:  				<a class="singer" href="{url_search_category}">{category}</a> | {LANG.view}: {view}	| {bitrate}kb/s | {duration} | {size} MB			</p>		</div>	<!-- END: acept -->	<!-- END: song -->	</div>	</div>  <div class="clear"></div></div></div><!-- END: access --><!-- END: main -->