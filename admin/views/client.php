<?php
$client = new WP_Client();
if (isset($_GET['client'])) {
    $client->get_row('id', $_GET['client']);
}
?>
<div class="wrap">

    <h1><?= $_GET['action'] == 'edit' ? 'Edit' : 'New' ?> Client</h1>
    <hr>

    <div class="pm_block">
        <div class="errorMessage"></div>
        <div class="successMessage"></div>
    </div>

    <form id="user-reg" method="<?= $_GET['action'] == 'edit' ? 'update' : 'register' ?>">
        <?php if ($_GET['action'] == 'edit'): ?>
            <input type="hidden" id="id" name="id" value="<?= $client->id ?>"/>
        <?php else: ?>
            <input type="hidden" id="isActive" name="status" value="1"/>
            <input type="hidden" id="createdOn" name="created_on" value="<?= date('Y-m-d h:i') ?>"/>
            <input type="hidden" id="owner" name="owner" value="<?= get_current_user_id() ?>"/>
        <?php endif; ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label>Username</label></th>
                    <td><input class="regular-text code" name="username" type="text" id="username" required="required" value="<?= $client->username ?>" <?= $_GET['action'] == 'edit' ? 'readonly=""' : '' ?> /></td>
                </tr>
                <?php if ($_GET['action'] != 'edit'): ?>
                    <tr>
                        <th><label>Password</label></th>
                        <td><input class="regular-text" name="password" type="text" id="password" required="required" /></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th><label>First Name</label></th>
                    <td><input class="regular-text" name="firstname" type="text" id="firstname" required="required" value="<?= $client->firstname ?>" /></td>
                </tr>
                <tr>
                    <th><label>Last Name</label></th>
                    <td><input class="regular-text" name="lastname" type="text" id="lastname" value="<?= $client->lastname ?>" /></td>
                </tr>
                <tr>
                    <th><label>Email Address</label></th>
                    <td><input class="regular-text" name="email" type="email" id="email" required="required" value="<?= $client->email ?>" /></td>
                </tr>
                <tr>
                    <th><label>Address</label></th>
                    <td><textarea name="address" class=""><?= $client->address ?></textarea></td>
                </tr>
                <tr>
                    <th><label>Telephone Number</label></th>
                    <td><input class="regular-text" name="telno" type="text" id="telno" value="<?= $client->telno ?>" /></td>
                </tr>
                <tr>
                    <th><label>Mobile Number</label></th>
                    <td><input class="regular-text" name="mobileno" type="text" id="mobileno" value="<?= $client->mobileno ?>" /></td>
                </tr>
                <?php if ($_GET['action'] != 'edit'): ?>
                    <tr>
                        <th><label>Email Details To User</label></th>
                        <td><input type="checkbox" id="chkSendDetails" name="send_details" value="1" /></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th><label>&nbsp;</label></th>
                    <td>
                        <button type="submit" name="submit" class="button-primary "><?= $_GET['action'] == 'edit' ? 'Update' : 'Create' ?> Client</button>
                        <img id="loading" src="<?= admin_url('images/loading.gif') ?>" title="loading" style="display:none;"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>