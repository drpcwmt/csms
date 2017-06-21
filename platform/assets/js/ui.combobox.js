//UI combobox

(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function() {
			var input,
				that = this,
				select = this.element.hide(),
				selected = select.children( ":selected" ),
				value = selected.val() ? selected.text() : "",
				onchng = select.attr('onChange');
				wrapper = this.wrapper = $( "<span>" )
					.addClass( "ui-combobox" )
					.insertAfter( select );
				
			function removeIfInvalid(element) {
				var value = $( element ).val(),
					matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( value ) + "$", "i" ),
					valid = false;
				select.children( "option" ).each(function() {
					if ( $( this ).text().match( matcher ) ) {
						this.selected = valid = true;
						return false;
					}
				});
				if ( !valid ) {
					// remove invalid value, as it didn't match anything
					$( element )
						.val( "" )
						.attr( "title", value + ' '+getLang('error_not_item_found') )
						.tooltip( "open" );
					select.val( "" );
					setTimeout(function() {
						input.tooltip( "close" ).attr( "title", "" );
					}, 2500 );
					input.data( "autocomplete" ).term = "";
					return false;
				}
			}

			input = $( '<input type="text">' )
				.appendTo( wrapper )
				.val( value )
				.attr( {
					"title": "",
				})
				.addClass( "ui-combobox-input" )
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: function( request, response ) {
						var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
						response( select.children( "option" ).map(function() {
							var text = $( this ).text();
							if ( this.value && ( !request.term || matcher.test(text) ) )
								return {
									label: text,/*text.replace(
										new RegExp(
											"(?![^&;]+;)(?!<[^<>]*)(" +
											$.ui.autocomplete.escapeRegex(request.term) +
											")(?![^<>]*>)(?![^&;]+;)", "gi"
										), "<strong class=\"red_item\">$1</strong>" ),*/
									value: text,
									option: this
								};
						}) );
					},
					select: function( event, ui ) {
						ui.item.option.selected = true;
						that._trigger( "selected", event, {
							item: ui.item.option
						});
						if(select.attr('onchange') && select.attr('onchange') != ''){
							eval(select.attr('onchange'));
						}
						if(select.attr('update') && select.attr('update') != ''){
							if($(this).attr('module') && $(this).attr('module')!='' ){
								loadModuleJS(select.attr('module'));
							} else if(select.attr('plugin') && select.attr('plugin')!=''){
								loadPluginJS(select.attr('plugin'));
							};	
							var action = select.attr('update');
							if (typeof window[action] === "function") {
								window[action](select);
							} else {
								alert(action+' is undefined');
							}
						}
					},
					change: function( event, ui ) {
						if ( !ui.item ){
							return removeIfInvalid( this );
						} 
						var $form = $(this).parents('form');
						$form.find('input.this_form_modified').val(1);
					}
				})
				.addClass( "ui-widget" );

			input.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.data( "item.autocomplete", item )
					.append( '<a >' + item.label + "</a>" )
					.appendTo( ul );
			};

			$( "<a>" )
				.attr( "title", getLang('show_all') )
				.tooltip()
				.appendTo( wrapper )
				.html('<span class="ui-icon ui-icon-triangle-1-s"></span>')
				.removeClass( "ui-corner-all" )
				.addClass("ui-corner-right icon_button ui-state-default ui-combobox-toggle" )
				.click(function() {
					// close if already visible
					if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
						input.autocomplete( "close" );
						removeIfInvalid( input );
						return;
					}

					// work around a bug (likely same cause as #5265)
					$( this ).blur();

					// pass empty string as value to search for, displaying all results
					input.autocomplete( "search", "" );
					input.focus();
				});

				input
					.tooltip({
						position: {
							of: this.button
						},
						tooltipClass: "ui-state-highlight"
					});
		},

		destroy: function() {
			this.wrapper.remove();
			this.element.show();
			$.Widget.prototype.destroy.call( this );
		}
	});
})( jQuery );