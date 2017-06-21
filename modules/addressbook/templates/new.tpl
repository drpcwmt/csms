<form>
    <input type="hidden" name="id" value="[@id]" />
    <input type="hidden" name="con" value="[@con]" />
    <input type="hidden" name="con_id" value="[@con_id]" />
    <input type="hidden" name="user_id" value="[@user_id]" />
    <input type="hidden" name="lng" value="[@lng]" />
    <input type="hidden" name="lat" value="[@lat]" />
    <table width="100%">
        <tr>
            <td>
                <fieldset  class="ui-state-highlight">
                	<legend>عربي</legend>
                   <table width="100%" cellspacing="1" cellpadding="0" border="0">
                      <tbody>
                        <tr>
                          <td valign="top" dir="rtl">
                            <input type="text" name="building" value="[@building]" style="width: 20" title="[#building]">
                            <input type="text" dir="rtl" class="input_double ui-corner-left rev_float" value="[@address_ar]" id="address_ar" name="address_ar" data-geo="name" >
                          <button class="ui-state-default hoverable circle_button" action="searchMap"><span class="ui-icon ui-icon-search"></span></button></td>
                          <td valign="middel"><label class="label ui-widget-header ui-corner-right">[#rtl_address]</label></td>
                        </tr>
                        <tr>
                          <td valign="top"><input type="text" dir="rtl" class="ena_auto rev_float ui-corner-left" value="[@region_ar]" id="region_ar" name="region_ar" data-geo="sublocality"></td>
                          <td valign="middel" style="align:left"><label class="label ui-widget-header  ui-corner-right">[#region]</label></td>
                        </tr>
                        <tr>
                          <td valign="top"><input type="text" class="ena_auto rev_float ui-corner-left" dir="rtl" value="[@city_ar]" id="city_ar" name="city_ar"  data-geo="locality"></td>
                          <td valign="middel"><label class="label ui-widget-header ui-corner-right ">[#rtl_city]</label></td>
                        </tr>
                        <tr>
                          <td valign="top"><input type="text" dir="rtl" class="ena_auto rev_float ui-corner-left" value="[@country_ar]" id="country_ar" name="country_ar" onchange="copyRelated(this, '#father_country_ar, #mother_country_ar')"  data-geo="country"></td>
                          <td valign="middel" style="align:left"><label class="label ui-widget-header  ui-corner-right">[#country_ar]</label></td>
                        </tr>
                        
                        <tr>
                          <td valign="top"><input type="text" dir="rtl" class="ena_auto rev_float ui-corner-left" value="[@zip]" id="zip" name="zip"  data-geo="postal_code"></td>
                          <td valign="middel" style="align:left"><label class="label ui-widget-header  ui-corner-right">[#zip]</label></td>
                        </tr>
                        <tr>
                          <td valign="top">
                            <textarea name="landmark">[@landmark]</textarea>
                          </td>
                          <td valign="middel" style="align:left"><label class="label ui-widget-header  ui-corner-right">[#landmark]</label></td>
                        </tr>
                      </tbody>
                    </table>
               </fieldset>
              <fieldset class="ui-state-highlight">
              	<legend>English</legend>
                  <table width="100%" cellspacing="1" cellpadding="0" border="0">
                    <tbody>
                      <tr>
                        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_address]</label></td>
                        <td valign="top"><input type="text" class="input_double " value="[@address]" id="address" name="address" /></td>
                      </tr>
                      <tr>
                        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#region]</label></td>
                        <td valign="top"><input type="text" class="ena_auto " value="[@region]" id="region" name="region"  ></td>
                      </tr>
                      <tr>
                        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_city]</label></td>
                        <td valign="top"><input type="text" class="ena_auto " value="[@city]" id="city" name="city"></td>
                      </tr>
                      <tr>
                        <td valign="middel"><label class="label ui-widget-header ui-corner-left reverse_align">[#ltr_country]</label></td>
                        <td valign="top"><input type="text" class="ena_auto " value="[@country]" id="country" name="country" ></td>
                      </tr>
                      
                    </tbody>
                  </table>
               </fieldset>
            </td>
            <td><div class="map-canvas" style=" width:400px;height:320px;"></div></td>
        </tr>
    </table>   
 </form> 