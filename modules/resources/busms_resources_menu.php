<?php

## BusMs

## Resources menu



$resource_menu = write_html('ul', 'class="nav"',

	write_html('li', '', 

		write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="drivers"', $lang['drivers'])

	).

	write_html('li', '', 

		write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="matrons"', $lang['matrons'])

	).

	write_html('li', '', 

		write_html('a', 'class="ui-state-default hoverable" action="openResource" rel="bus"', $lang['bus'])

	)

);