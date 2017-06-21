<?php
## messages layout
$messages_toolbox =	write_html('form', 'id="msg_nav" class="toolbox"',
	write_html('span', 'style="margin:0px 30px"',
		write_html('label', 'class="label ui-state-default ui-corner-left" style="height:14px"',
			$lang['goto'].': '
		).
		write_html_select('id="view_select" class="combobox" onchange="reloadView()"',$view_array, $cur_view)
	).			
	(count($allowed_recivers) > 0 ?
		write_html('a', 'class="ui-state-default hoverable" onclick="loadCompose()"',
			write_html('span', 'class="ui-icon ui-icon-document"','').
			$lang['new']
		)
	: '').
	write_html('a', 'class="ui-state-default hoverable" onclick="loadCompose(\'\', \'forward\')"',
		write_html('span', 'class="ui-icon ui-icon-arrowreturnthick-1-e"','').
		$lang['forward']
	).
	($cur_view == 'inbox' ?
		write_html('a', 'class="ui-state-default hoverable" onclick="loadCompose(\'reply\')"',
			write_html('span', 'class="ui-icon ui-icon-arrowreturnthick-1-w"','').
			$lang['reply']
		).
		write_html('a', 'class="ui-state-default hoverable"  onclick="markUnread()"',
			write_html('span', 'class="ui-icon ui-icon-mail-closed"','').
			$lang['mark_uread']
		)
	:'').
	write_html('a', 'class="ui-state-default hoverable" action="print_pre" rel="#msg_content" plugin="print"',
		write_html('span', 'class="ui-icon ui-icon-print"','').
		$lang['print']
	).
	(isset($_GET['trash']) ? 
		write_html('a', 'class="ui-state-default ui-corner-right hoverable" onclick="restoreMsg()"',
			write_html('span', 'class="ui-icon ui-icon-refresh"','').
			$lang['restore']
		) :
		write_html('a', 'class="ui-state-default ui-corner-right hoverable" onclick="deleteMsg()"',
			write_html('span', 'class="ui-icon ui-icon-trash"','').
			$lang['delete']
		) 
	)
);

$message_layout = write_html('table', 'width="100%" cellspacing="0" cellpadding="0"', 
	write_html('tr','',
		write_html('td', 'width="30%" valign="top"', 
			write_html('div', 'class="ui-corner-bottom ui-widget-content"',
				write_html('div', 'id="msg_list-messages" style="max-height:400px; overflow:auto;padding:5px"', $messages_list)
			)
		).
		write_html('td', ' valign="top" style="padding:4px"', 
			$messages_toolbox.
			write_html('div', 'id="msg_content" style="padding:5px"',
				getMessageContent($lastmsg['id'])
			)
		)
	)
);

?>