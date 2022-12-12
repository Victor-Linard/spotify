<?php
    date_default_timezone_set('Europe/Paris');
    global $chk;
    if (isset($_POST['ld_spotify_save_result'])) {
        ld_spotify_save_result();
    }

    function ld_spotify_save_result() {
        $ld_spotify_save_result = $_POST['ld_spotify_save_result'];

        global $chk;

        if (get_option('ld_spotify_save_result') != trim($ld_spotify_save_result)) {
            $chk = update_option('ld_spotify_save_result', trim($ld_spotify_save_result));
        }
    }
?>
<div>

    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Finder/css/theme.min.css">

    <?php if (isset($_POST['ld_spotify_save_result']) && $chk): ?>
        <div id="message" class="alert alert-success d-flex m-3" role="alert">
            <i class="fi-check-circle me-2 me-sm-3 lead"></i>
            <div>Content updated successfully at <?php echo date("H:i:s"); ?></div>
        </div>
    <?php endif; ?>

    <h3>Spotify Plugin Preferences</h3>
    <form method="post" action="">
        <div class="row">
            <div class="col-auto">
                <span>Save search : </span>
            </div>
            <div class="col-auto">
                <?php if (get_option('ld_spotify_save_result') == 'True'): ?>
                    <span class="badge bg-faded-success">Active</span>
                <?php else: ?>
                    <span class="badge bg-faded-danger">Inactive</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-auto">
                <?php if (get_option('ld_spotify_save_result') == 'True'): ?>
                    <button type="submit" name="ld_spotify_save_result" value="False" class="btn btn-primary btn-sm">Desactivate</button>
                <?php else: ?>
                    <button type="submit" name="ld_spotify_save_result" value="True" class="btn btn-primary btn-sm">Activate</button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>