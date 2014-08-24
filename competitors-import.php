<h2><img src="<?php echo plugins_url("assets/images/Juiced-Metrics-Green.png", __FILE__);?>" /></h2>

         <hr />

                <h2>Import Competitor</h2>

            <form action="" method="post" enctype="multipart/form-data">

            <table class="form-table">

            <tbody>

            <tr>

            <th scope="row"><label for="blogname">file (Excel) :</label></th>

            <td>
            <input type="file" name="xls_import" size="31" id="xls_import"  class="full-width">
            <a href="<?php echo plugins_url("uploadedXLfile/sample.xls", __FILE__);?>">Sample Excel File</a>
</td>

            </tr>


            </tbody></table>

              

            <p class="submit"><input type="submit" value="Import" class="button button-primary" id="submit" name="submit">

            

            <input name="action" value="import_competitor" type="hidden" />

            </p></form>

                
