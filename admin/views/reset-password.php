<?php
$clients = new WP_Client();
$user_id = get_current_user_id();
$condition = is_super_admin() ? '' : "WHERE owner = $user_id";
?>
<div class="wrap">

    <h1>Reset Client Password</h1>
    <hr>

    <div class="pm_block">
        <div class="errorMessage"></div>
        <div class="successMessage"></div>
    </div>
    <form method="reset_password">
        <table class="form-table">
            <tbody>
                <tr>
                    <th>Client</th>
                    <td>
                        <select id="user_id" name="id" class="pm_selectOption">
                            <option value="" >--Select Client--</option>
                            <?php foreach ($clients->get_clients() as $client): ?>
                                <option value="<?php echo esc_attr($client->id); ?>" <?php selected($client->id, $client_id); ?>><?php echo esc_html($client->firstname); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>New Password</th>
                    <td><input id="new_password" name="password" type="text" /> </td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><input id="btnNPSubmit" class="butt_ons button-primary button-large" type="submit"  value="Reset" /></td>
                    <!--<input id="btnNPCancel" class="butt_ons button-primary button-large" type="button" value="Cancel" />-->
                </tr>
            </tbody>
        </table>
    </form>
</div>