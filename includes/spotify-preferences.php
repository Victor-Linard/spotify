<?php
    date_default_timezone_set('Europe/Paris');
    require_once 'records_update.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    global $chk;
    if (isset($_POST['ld_spotify_save_result'])) {
        ld_spotify_save_result();
    }

    if (isset($_POST['update_records'])) {
        updateRecords();
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

    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Dashkit/css/libs.bundle.css">
    <link rel="stylesheet" media="screen" href="../wp-content/plugins/ld_spotify/Dashkit/css/theme.bundle.css">

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
                <h4 class="mb-1">
                    Save search results
                </h4>
                <p class="small text-muted mb-3">
                    Activate save search result if you want your daily search to be more efficient.
                </p>
                <div class="btn-group-toggle row gx-2 mb-4">
                    <div class="col">
                        <input class="btn-check" onchange="this.form.submit();" name="ld_spotify_save_result"
                               id="ld_spotify_save_result_true" type="radio" value="True"
                            <?php if (get_option('ld_spotify_save_result') == 'True'): ?>
                                checked
                            <?php endif; ?>
                        >
                        <label class="btn w-100 btn-outline-success" for="ld_spotify_save_result_true">
                            Activate
                        </label>
                    </div>
                    <div class="col">
                        <input class="btn-check" onchange="this.form.submit();" name="ld_spotify_save_result"
                               id="ld_spotify_save_result_false" type="radio" value="False"
                            <?php if (get_option('ld_spotify_save_result') == 'False'): ?>
                                checked
                            <?php endif; ?>>
                        <label class="btn w-100 btn-outline-danger" for="ld_spotify_save_result_false">
                            Desactivate
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mb-4">
        <div class="row">
            <h4 class="mb-1">
                Local data
            </h4>
        </div>
        <div class="row">
            <p class="small text-muted mb-3">
                Here are some key figures on the records.
            </p>
        </div>
        <div class="row">
            <div class="col-auto">
                Records : <span class="badge bg-info-soft"><?php echo getNbRecords(); ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-auto">
                Up to date records : <span class="badge bg-success-soft"><?php echo getNbUpToDateRecords(); ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-auto">
                Outdated records : <span class="badge bg-danger-soft"><?php echo getNbOutdatesRecords(); ?></span>
            </div>
        </div>
        <?php if (getNbOutdatesRecords() > 0): ?>
        <div class="row">
            <div class="col-auto">
                <button type="submit" name="update_records" class="btn btn-primary mb-2">Update</button>
            </div>
        </div>
        <?php endif; ?>
    </form>

    <script src="../wp-content/plugins/ld_spotify/Dashkit/js/theme.bundle.js"></script>
    <script src="../wp-content/plugins/ld_spotify/Dashkit/js/vendor.bundle.js"></script>
</div>