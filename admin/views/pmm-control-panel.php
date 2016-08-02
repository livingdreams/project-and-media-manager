<?php
$pmm_options = get_option('pmm_settings');
$franchise_number = $pmm_options['franchise_number'];
?>
<div class="wrap">

    <h1>General Options</h1>
    <hr>

    <div class="pm_block">
        <div class="errorMessage"></div>
        <div class="successMessage"></div>
    </div>
    <h2>Franchise Numbers</h2>
    <form id="user-reg" method="general">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label>Water</label></th>
                    <td><input class="regular-text code" name="franchise_number[water]" type="text" id="water" value="<?= $franchise_number['water'] ?>" <?= $_GET['action'] == 'edit' ? 'readonly=""' : '' ?> /></td>
                </tr>
                <tr>
                    <th><label>Fire</label></th>
                    <td><input class="regular-text" name="franchise_number[fire]" type="text" id="fire" value="<?= $franchise_number['fire'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Mold</label></th>
                    <td><input class="regular-text" name="franchise_number[mold]" type="text" id="mold" value="<?= $franchise_number['mold'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Storm</label></th>
                    <td><input class="regular-text" name="franchise_number[storm]" type="text" id="storm" value="<?= $franchise_number['storm'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Smoke</label></th>
                    <td><input class="regular-text" name="franchise_number[smoke]" type="text" id="smoke" value="<?= $franchise_number['smoke'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Other</label></th>
                    <td><input class="regular-text" name="franchise_number[other]" type="text" id="other" value="<?= $franchise_number['other'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Red Cross</label></th>
                    <td><input class="regular-text" name="franchise_number[red_cross]" type="text" id="red_cross" value="<?= $franchise_number['red_cross'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Fema</label></th>
                    <td><input class="regular-text" name="franchise_number[fema]" type="text" id="fema" value="<?= $franchise_number['fema'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>Shelter</label></th>
                    <td><input class="regular-text" name="franchise_number[shelter]" type="text" id="shelter" value="<?= $franchise_number['shelter'] ?>" /></td>
                </tr>                
            </tbody>
        </table>
        <h2>Video Thumbnail</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label>Video Thumbnail URL</label></th>
                    <td><input class="regular-text upload-img-url" name="video_thumb_url" type="text" id="shelter" value="<?= $pmm_options['video_thumb_url'] ?>" /></td>
                </tr>
                <tr>
                    <th><label>&nbsp;</label></th>
                    <td>
                        <button type="submit" name="submit" class="button-primary ">Update</button>
                        <img id="loading" src="<?= admin_url('images/loading.gif') ?>" title="loading" style="display:none;"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>