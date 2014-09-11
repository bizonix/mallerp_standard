<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Mallerp management system</title>
        <style type='text/css' media='all'>@import url('<?php echo base_url(); ?>static/css/main.css');</style>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/lib/prototype.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/lib/scriptaculous.js?load=effects,controls"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>static/js/ajax/main.js"></script>
        
        <meta http-equiv='expires' content='-1' />
        <meta http-equiv= 'pragma' content='no-cache' />
        <meta name='robots' content='all' />
        <meta name='author' content='Mallerp Inc. Dev Team' />
        <meta name='description' content='Mallerp Inc.' />

        <script type="text/javascript">
            var helper = new Helper();
        </script>
        <?= $_scripts?>
        <?=$_styles?>
    </head>
    <body>
        <div id="wrapper">
            <div id="main">
                <div id="content">
                    <div id="success-msg" class="success-msg" style="padding-left: 30px;display:none"></div>
                    <?php if (isset($errors)): ?>
                    <div id="important" class="important" style="padding-left: 30px;"><?=$errors?></div>
                    <?php else: ?>
                    <div id="important" class="important" style="padding-left: 30px;display:none"></div>
                    <?php endif; ?>
                    <div class="post">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>