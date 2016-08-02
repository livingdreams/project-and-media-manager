<div class="wrap">

    <h1>Area Manager</h1>
    <hr>

    <div class="pm_block">
        <div class="errorMessage"></div>
        <div class="successMessage"></div>
    </div>
    <div id="col-container">
        <div id="col-right">
            <div class="col-wrap">
                <?php $area = new WP_Area(); ?>
                <form method = "post">
                    <?php
                    $area->prepare_items();
                    $area->display();
                    ?>
                </form>
            </div>
        </div>
        <div id="col-left">
            <div class="col-wrap">
                <h2>Add New Area</h2>
                <form id="new-area" class="form-wrap" method="new_area">
                    <div class="form-field form-required area-wrap">
                        <label for="area">Area</label>
                        <input class="regular-text code" name="area" type="text" id="txtUsername" required="required" />
                        <p>The name of the area.</p>
                    </div>
                    <p class="submit">
                        <button type="submit" name="submit" class="button-primary ">Add Area</button>
                        <img id="loading" src="<?= admin_url('images/loading.gif') ?>" title="loading" style="display:none;"/>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>