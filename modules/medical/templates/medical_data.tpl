<form name="medical_data_form">
	<input type="hidden" name="id" value="[@id]" />
  <table width="100%" border="0" cellspacing="0" cellpadding="0" >
  <tbody>
    <tr>
      <td width="250">[#acute]</td>
      <td width="120">
      	<span class="buttonSet">
              <input type="radio" name="acute_chk" value="0" id="acute_chk_0" [@acute_chk_0_checked]>
              <label for="acute_chk_0" >[#no]</label>
              <input type="radio" name="acute_chk" value="1" id="acute_chk_1" [@acute_chk_1_checked]>
              <label for="acute_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="acute" id="acute" rows="3">[@acute]</textarea></td>
    </tr>
    <tr>
      <td>[#allergie]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="allergie_chk" value="0" id="allergie_chk_0" [@allergie_chk_0_checked]>
              <label for="allergie_chk_0">[#no]</label>
              <input type="radio" name="allergie_chk" value="1" id="allergie_chk_1" [@allergie_chk_1_checked]>
              <label for="allergie_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="allergie" id="allergie" rows="3">[@allergie]</textarea></td>
    </tr>
    <tr>
      <td>[#emotional]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="emotional_chk" value="0" id="emotional_chk_0" [@emotional_chk_0_checked]>
              <label for="emotional_chk_0">[#no]</label>
              <input type="radio" name="emotional_chk" value="1" id="emotional_chk_1" [@emotional_chk_1_checked]>
              <label for="emotional_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="emotional" id="emotional" rows="3">[@emotional]</textarea></td>
    </tr>
    <tr>
      <td>[#gastro]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="gastro_chk" value="0" id="gastro_chk_0" [@gastro_chk_0_checked]>
              <label for="gastro_chk_0">[#no]</label>
              <input type="radio" name="gastro_chk" value="1" id="gastro_chk_1" [@gastro_chk_1_checked]>
              <label for="gastro_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="gastro" id="gastro" rows="3">[@gastro]</textarea></td>
    </tr>
    <tr>
      <td>[#heart]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="heart_chk" value="0" id="heart_chk_0" [@heart_chk_0_checked]>
              <label for="heart_chk_0">[#no]</label>
              <input type="radio" name="heart_chk" value="1" id="heart_chk_1" [@heart_chk_1_checked]>
              <label for="heart_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="heart" id="heart" rows="3">[@heart]</textarea></td>
    </tr>
    <tr>
      <td>[#injuries]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="injuries_chk" value="0" id="injuries_chk_0" [@injuries_chk_0_checked]>
              <label for="injuries_chk_0">[#no]</label>
              <input type="radio" name="injuries_chk" value="1" id="injuries_chk_1" [@injuries_chk_1_checked]>
              <label for="injuries_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="injuries" id="injuries" rows="3">[@injuries]</textarea></td>
    </tr>
    <tr>
      <td>[#kidney]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="kidney_chk" value="0" id="kidney_chk_0" [@kidney_chk_0_checked]>
              <label for="kidney_chk_0">[#no]</label>
              <input type="radio" name="kidney_chk" value="1" id="kidney_chk_1" [@kidney_chk_1_checked]>
              <label for="kidney_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="kidney" id="kidney" rows="3">[@kidney]</textarea></td>
    </tr>
    <tr>
      <td>[#muscular]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="muscular_chk" value="0" id="muscular_chk_0" [@muscular_chk_0_checked]>
              <label for="muscular_chk_0">[#no]</label>
              <input type="radio" name="muscular_chk" value="1" id="muscular_chk_1" [@muscular_chk_1_checked]>
              <label for="muscular_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="muscular" id="muscular" rows="3">[@muscular]</textarea></td>
    </tr>
    <tr>
      <td>[#skin]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="skin_chk" value="0" id="skin_chk_0" [@skin_chk_0_checked]>
              <label for="skin_chk_0">[#no]</label>
              <input type="radio" name="skin_chk" value="1" id="skin_chk_1" [@skin_chk_1_checked]>
              <label for="skin_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="skin" id="skin" rows="3">[@skin]</textarea></td>
    </tr>
    <tr>
      <td>[#surgical]</td>
      <td>
      	<span class="buttonSet">
              <input type="radio" name="surgical_chk" value="0" id="surgical_chk_0" [@surgical_chk_0_checked]>
              <label for="surgical_chk_0">[#no]</label>
              <input type="radio" name="surgical_chk" value="1" id="surgical_chk_1" [@surgical_chk_1_checked]>
              <label for="surgical_chk_1">[#yes]</label>
         </span>
      </td>
      <td><textarea name="surgical" id="surgical" rows="3">[@surgical]</textarea></td>
    </tr>
    <tr>
      <td>[#other]</td>
      <td>
      
      </td>
      <td><textarea name="other" id="other" rows="3">[@other]</textarea></td>
    </tr>
  </tbody></table>
</form>
