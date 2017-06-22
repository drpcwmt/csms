<form action="#" method="POST" enctype="multipart/form-data" name="prod_details" id="prod_details" class="contact_form">
   <div class="form_subtitle">[#item]</div>
        <table width="100%" cellspacing="20">
            <tr>
                <td valign="top" width="65%">
                    <div class="form_row">
                      <input type="text" class="contact_input" name="id" disabled="disabled" value="[@id]" />
                      <label class="contact"><strong>[#code]</strong></label>
                    </div>
                     <div class="form_row">
                        <span id="req_txt" class="hidden">[#error-empty_files]</span><br />
                       <select name="cat_id" id="cat_id"  class="contact_input required">
                         <option value=""></option>
                         [@cats_options]
                       </select>
                       <label class="contact"><strong>[#category] :</strong><span class="astrix">*</span></label>
                    </div>
                    
                    <div class="form_row">
                      <select name="sub_id" id="sub_id" class="contact_input required">
                      	[@subcats_options]
                      </select>
                      <label class="contact"><strong>[#subcategory] :</strong><span class="astrix">*</span></label>
                  	</div>
                    
                    <div class="form_row">
                      <input type="text" class="contact_input required" name="name_ar" value="[@name]" />
                      <label class="contact"><strong>[#name]:</strong><span class="astrix">*</span></label>
                    </div>

                    <div class="form_row">
                      <input type="text" class="contact_input" name="writer"  value="[@writer]"/>
                      <label class="contact"><strong>[#writer_name]:</strong></label>
                    </div>
        
                     <div class="form_row">
                      <input type="text" class="contact_input" name="invest"  value="[@invest]"/>
                      <label class="contact"><strong>[#investigator]:</strong></label>
                    </div>
                                        
                    <div class="form_row">
                      <select name="lang" id="lang"  class="contact_input">
                        [@language_options]
                      </select>
                      <label class="contact"><strong>[#language] :</strong></label>
                    </div>
        
               		<div class="form_row">
                      <input type="text" class="contact_input" name="year" value="[@year]" />
                      <label class="contact"><strong>[#year] :</strong></label>
                  	</div>
                    
                    <div class="form_row">
                      <input type="text" class="contact_input" name="size"  value="[@size]"/>
                      <label class="contact"><strong>[#size] :</strong></label>
                      
                    </div>
                    <div class="form_row">
                      <input type="text" class="contact_input" name="cover_type"  value="[@cover_type]"/>
                      <label class="contact"><strong>[#cover_type] :</strong></label>
                    </div>
                     <div class="form_row">
                      <input type="text" class="contact_input" name="paper_type"  value="[@paper_type]"/>
                      <label class="contact"><strong>[#paper_type] :</strong></label>
                    </div>
                    <div class="form_row">
                      <input type="text" class="contact_input" name="pages" value="[@pages]" />
                      <label class="contact"><strong>[#pages] :</strong></label>
                    </div>
                    <div class="form_row">
                      <input type="text" class="contact_input" name="vol" value="[@vol]" />
                      <label class="contact"><strong>[#vol] :</strong></label>
                    </div>
                    <div class="form_row">
                      <input type="text" class="contact_input" name="contener" value="[@contener]" />
                      <label class="contact"><strong>[#contener] :</strong></label>
                    </div>
                    <div class="form_row">
                       <input type="text" class="contact_input required" name="price" value="[@price]" />
                      <label class="contact"><strong>[#price] :</strong><span class="astrix">*</span></label>
                    </div>
                </td>
                <td valign="top">      
                    <table class="upload_files"></table>
                    <table class="download_files" width="100%" height="150px"><tr><td align="center"><img src="scripts/img_resize.php?path=../photos/<?php echo $prod['id'];?>.jpg&w=150" width="150" border="0" /></td></tr></table>
                    <label class="contact"  style="display:block; width:100%"><strong>التعليق </strong></label>      
                    <div class="form_row" align="center">
                        <textarea name="comment_ar" class="contact_textarea tinymce" ><?php echo $prod['comment_ar']; ?></textarea>
                    </div>
                    <label class="contact" style="display:block; width:100%"><strong>Comments</strong></label>
                    <div class="form_row" align="center"> 
                        <textarea name="comment_en" class="contact_textarea tinymce" dir="ltr" lang="en" ><?php echo $prod['comment_en']; ?></textarea>
                    </div>
                </td>
            </tr>
        </table>
                 
            </form>