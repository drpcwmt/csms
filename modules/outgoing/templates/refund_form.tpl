<form class="ui-state-highlight">
  <input type="hidden" name="currency" value="[@cur]" />
  <input type="hidden" name="note" value="[@note]" />
  <table cellspacing="0">
    <tr>
      <td><label class="label reverse_align" style="width:120px; float:left">[#amount]: </label></td>
      <td><div class="fault_input">[@val]</div>
        [@cur]</td>
    </tr>
    <tr>
      <td><label class="label reverse_align" style="width:120px; float:left">[#office_fees_refund]: </label></td>
      <td><input type="text" name="refund" class="required" value="0" /></td>
    </tr>
    <tr>
      <td><label class="label reverse_align" style="width:120px; float:left">[#discount_val]: </label></td>
      <td><input type="text" name="discount" class="required" value="0" /></td>
    </tr>
    <tr>
      <td><label class="label reverse_align" style="width:120px; float:left">[#date]: </label></td>
      <td><input type="text" name="date" class="required datepicker mask-date" value="0" /></td>
    </tr>
  </table>
</form>
