<form name="accademic_form">
    <table border="0" cellpadding="1" cellspacing="0" width="100%">
      <tbody><tr>
        <td width="120"><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#diplom]</label></td>
        <td><input name="diplome" id="diplome" value="[@diplom]" type="text"></td>
        </tr>
        <tr>
          <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#university]</label></td>
          <td><input name="university" type="text" id="university" value="[@university]" /></td>
        </tr>
        <tr>
        <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#graduation_year]</label></td>
        <td><input name="diplome_year" id="diplome_year" value="[@graduation_year]" type="text"></td>
        </tr>
      <tr>
        <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#degree]</label></td>
        <td><input name="diplome_degree" id="diplome_degree" value="[@degree]" type="text"></td>
        </tr>
      <tr>
        <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#school]</label></td>
        <td>
            <label><input value="0" name="school_type" checked="checked" type="radio">
            [#national_school]</label>
            <br>
            <label><input value="1" name="school_type" type="radio">
            [#language_school]</label>
        </td>
        </tr>
      <tr>
        <td><label class="label ui-widget-header ui-corner-left reverse_align" dir="rtl">[#school_name]</label></td>
        <td><input name="school_name" id="school_name" value="[@school_name]" type="text"></td>
        </tr>
    </tbody></table>
    <fieldset>
      <legend>[#others]</legend>
      <textarea name="diplome_others">[@diplome_others]</textarea>
  </fieldset>
</form>
