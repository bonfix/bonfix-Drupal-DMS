<?php $path=$theme_path.'/css/mail.css';$css=file_get_contents($path);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>
    <?php print $subject;?> </title>
  <link href="<?php print $theme_url;?>/css/mail.css" media="all" rel="stylesheet" type="text/css" />
  
  <style>
    <?php print $css;?>
  </style>
</head>

<body style="font-size:13px !important">
  <table class="body-wrap" >
    <tr>
      <td></td>
      <td class="container" width="600" >
        <div class="content" >
          <table class="main" width="100%" cellpadding="0" cellspacing="0" >
            <tr style="background:#4AA8FD !important">
              <td class="alert alert-good" style=" background:#4AA8FD !important; word-spacing: 0.1em !important; font-family: Georgia, 'Times New Roman', Times, serif !important; font-style: italic !important; max-width:500px !important" >
                <?php print $subject;?></td>
            </tr>
            <tr>
              <td class="content-wrap">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="content-block" style="max-width:500px !important">
                      <?php print $body;?> </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <div class="footer">
            <table width="100%">
              <tr>
                <td class="aligncenter content-block">
                  <div class="pull-right" id="footer"> 
                  World Food Programme &copy;
                    <?php print date('Y');?> | 
                    <?php print variable_get('site_name', NULL);?></div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </td>
      <td></td>
    </tr>
  </table>
</body>

</html>