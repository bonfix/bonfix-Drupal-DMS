<!doctype html>
<html>
 <head>
  <meta name="viewport" content="width=device-width">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>
   <?php print @$subject;?>
  </title>
  <link href="<?php print @$css_url;?>" rel="stylesheet" type="text/css"></link>
  <style>
   <?php print @$css_content;?>
  </style>
 </head>
 <body bgcolor="#f7f9fa">
 <table class="body-wrap" bgcolor="#f7f9fa">
  <tr>
   <td></td>
   <td class="container header">
    <h1><?php print @$shortkey;?></h1>
   </td>
   <td></td>
  </tr>
  <tr>
   <td></td>
    <td class="container" bgcolor="#FFFFFF">
     <div class="content">
      <table>
       <tr>
        <td>
	     <?php print it_communities_mail_format(@$body);?>
        </td>
       </tr>
      </table>
     </div>
    </td>
    <td></td>
   </tr>
  </table>
  <table class="footer-wrap">
   <tr>
    <td></td>
    <td class="container">
     <div class="content">
      <table>
       <tr>
        <td align="center">
         <p>
         <?php print @$footer; ?> 
         <p>
        </td>
       </tr>
      </table>
     </div>
    </td>
    <td></td>
   </tr>
  </table>
 </body>
</html>