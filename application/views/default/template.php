<?php
include(APPPATH . 'config/wisdoms.php');

$wisdom = '';
if (isset($MALLERP_WISDOMS))
{
    $wisdoms_count = count ($MALLERP_WISDOMS);
    $wisdom_index = rand(0, $wisdoms_count - 1);
    $wisdom = $MALLERP_WISDOMS[$wisdom_index];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=7" />
        <title><?= $wisdom .' - ' . lang('mallerp_management_system');?></title>
        <style type='text/css' media='all'>@import url('<?php echo base_url(); ?>static/css/main.css');</style>
        <style type='text/css' media='all'>@import url('<?php echo base_url(); ?>static/css/modalbox.css');</style>
        <style type='text/css' media='all'>@import url('<?php echo base_url(); ?>static/css/nav/menubar.css');</style>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/nav/menubar.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/lib/prototype.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/lib/scriptaculous.js?load=effects,controls"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/modalbox.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/ajax/main.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/ajax/autocomplete.js"></script>
        <meta http-equiv= 'pragma' content='no-cache' />
        <meta http-equiv= 'pragma' content='Wed, 11 Jan 1984 05:00:00 GMT' />
        <meta http-equiv= 'Cache-Control' content='no-store, no-cache, must-revalidate' />
        <meta name='robots' content='all' />
        <meta name='author' content='Mallerp Inc. Dev Team' />
        <meta name='description' content='Mallerp Inc.' />
        <link type="image/x-icon" href="<?php echo base_url(); ?>static/images/icons/favicon.ico" rel="shortcut icon">

            <script type="text/javascript">
                var helper = new Helper();
                helper.periodical_new_task_checker('<?= site_url('message/fetch_messages') ?>');
            </script>
            <?= $_scripts ?>
            <?= $_styles ?>
    </head>

    <body>
        <?= $nav_inner ?> 
        <div id="wrapper">
            <div id="header">

            </div>
            <div id="main">
                <div id="content">
                    <div id="success-msg-top" class="success-msg" style="padding-left: 30px;display:none"></div>
                    <?php if (isset($errors)): ?>
                    <div id="important-top" class="important" style="padding-left: 30px;"><?=$errors?></div>
                    <?php else: ?>
                    <div id="important-top" class="important" style="padding-left: 30px;display:none"></div>
                    <?php endif; ?>
                    <div class="post">
                        <?= $content ?>
                    </div>
                    <div id="success-msg-foot" class="success-msg" style="padding-left: 30px;display:none"></div>
                    <?php if (isset($errors)): ?>
                    <div id="important-foot" class="important" style="padding-left: 30px;"><?=$errors?></div>
                    <?php else: ?>
                    <div id="important-foot" class="important" style="padding-left: 30px;display:none"></div>
                    <?php endif; ?>
                    <div id="message_popup" style="right: 0px; top: 38px; position: absolute; display: none">
                    </div>
                    <div style="left: -2px; top: 0px; width: 1423px; height: 754px;display: none; " id="loading-mask">
                        <p id="loading_mask_loader" class="loader"><img alt="Loading..." src="<?php echo base_url(); ?>static/images/ajax-loader-tr.gif"><br>Please wait...</p>
                    </div>
                </div>
            </div>
            <div id="footer">
                <?= $footer ?>
                <?php
					$elapsed = page_load_time();
					echo "Total execution time: ".$elapsed;
				?>
            </div>
        </div>

    </body>
</html>
