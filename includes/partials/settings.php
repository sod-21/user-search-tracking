<div class="wrap memberpress-sod-settings">
    <h2>Settings</h2>
    <div class="refresh-success-message notice notice-success is-dismissible" style="display: none;">
        <p><?php _e("Refresh successfully.", "memberpress-sod-cha"); ?>
        <p>
    </div>
    <form>
        <p><em>* maybe you can add the url editing.</em></p>
        <p><em>* maybe you can add the time interval.</em></p>
        <br>
        <p class="em">
            <span>================ WP CLI Command ================</span><br>
            <b>wp memberpress sod challenge refresh</b><br>
            <span>============================================</span>
        </p>
        <br>

        <p class="em"><span>================ WP ShortCode ================</span> <br>
            <b>[memberpress_sod_challenge title=""]</b><br>
            <span>============================================</span>
        </p>
        <br>
        <p>Getting the data from the api.</p>
        <input type="hidden" name="page" value="<?php echo $_GET["page"]; ?>" />
        <input type="submit" name="refresh_api" class="button button-primary" value="Refresh" />
    </form>
</div>
<script type="text/javascript">
    if (window.location.hash == "#success") {
        document.querySelector(".refresh-success-message").style.display = "block";
        window.location.hash = "";
    }
</script>