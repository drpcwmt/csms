<?php
/** Suppliers
*
*
*/

require_once('modules/products/products.class.php');

class Transactions{
	public function __construct($id){
		if($id != ''){	
			$supplier = do_query_obj("SELECT * FROM transactions WHERE id=$id", MySql_Database);	
			if(isset( $supplier->id )){
			
				foreach($supplier as $key =>$value){
					$this->$key = $value;
				}
				return $this;
			} else {
				return false;
			}	
		} else {return false;}
			
	}
	
	static function _toList($transactions){
		global $lang;
		$trs = array();
		if($transactions != false && count($transactions) > 0){
			foreach($transactions as $trans){
				$trans_type = get_class($trans);
				switch($trans_type){
					case 'Buys':
						$attr_action = 'module="buys" action="openBuysBut" buyid="'.$trans->id.'"';
					break;
					case 'BuysRefunfs':
						$attr_action = 'module="buys" action="openBuysRefundsBut" refundid="'.$trans->id.'"';
					break;
					case 'Transfers':
						$attr_action = 'module="transfers" action="openTransfersBut" transfid="'.$trans->id.'"';
					break;
					case 'Sells':
						$attr_action = 'module="sells" action="openSellsBut" sellid="'.$trans->id.'"';
					break;
					case 'SellsRefunfs':
						$attr_action = 'module="sells" action="openSellsRefundsBut" refundid="'.$trans->id.'"';
					break;
					default:
						echo $trans_type;
					break;
					
				}
				$trs[] = write_html('tr', '',
					write_html('td', '', 
						write_html('button', 'class="ui-state-default ui-corner-all hoverable circle_button" '. $attr_action, write_icon('extlink'))
					).
					write_html('td', '', $trans->id).
					write_html('td', '', unixToDate($trans->issue_date)).
					write_html('td', '', unixToDate($trans->delivery_date)).
					(in_array($trans_type, array('Buys', 'Transfers', 'SellsRefunds')) ?
						write_html('td', '', '&nbsp;').
						write_html('td', '', $trans->total)
					:
						write_html('td', '', $trans->total).
						write_html('td', '', '&nbsp;')
					).
					write_html('td', '', $trans->getPaid()).
					write_html('td', '', $trans->getStatus())
				);
			}
		}
		$out = write_html('table', 'class="tablesorter"',
        	write_html('thead', '',
            	write_html('tr', '',
                	write_html('th', 'width="24" style="background-image:none" class="unprintable"', '&nbsp;').
                    write_html('th', 'width="80"', $lang['id']).
					write_html('th', '', $lang['date']).
					write_html('th', '', $lang['delivery_date']).
					write_html('th', '', $lang['outgoing']).
					write_html('th', '', $lang['incoming']).
					write_html('th', '', $lang['paid']).
					write_html('th', '', $lang['status'])
				)
			).
			write_html('tbody', '',
				implode('', $trs)
			)
		);
		return $out;
	}
	
	static function newForm($value){
		return filltemplate('modules/transactions/templates/transactions.tpl', $value);
	}
	
	static function itemsToList($items){
		$out = '';
		foreach($items as $itm){
			$prod = new Products($itm->prod_id);
			$out.= write_html('tr', '',
				write_html('td', 'style="padding:1px 2px" class="unprintable"',
					write_html('button', 'type="button" action="removeTransactionItem" class="ui-state-default ui-corner-all hoverable circle_button"', write_icon('close'))
				).
				write_html('td', 'style="padding:0"',
                    '<input type="text" name="item_id[]" class="input_half no-corner" update="getProductsData" value="'.$prod->id.'" />'
				).
                write_html('td', 'style="padding:0"',
					'<input type="text" name="name[]" class="item_name input_double no-corner" value="'.$prod->getName().'" />
					<input class="autocomplete_value" type="hidden" value="'.$prod->id.'" />'.
					write_html('button', 'type="button" module="products" action="openProduct" prodid="'.$prod->id.'" class="ui-state-default ui-corner-all hoverable circle_button unprintable"', write_icon('extlink'))
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="quantity[]" class="input_half no-corner" value="'.$itm->quantity.'" />'
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="price[]" class="input_half no-corner" value="'.$itm->price.'" />'
				).
                write_html('td', 'style="padding:0"',
                    '<input type="text" name="total[]" class="input_half no-corner" disabled  value="'.($itm->quantity * $itm->price).'" />'
				)
			);
		}
		return $out;
	}
	
}
?>
