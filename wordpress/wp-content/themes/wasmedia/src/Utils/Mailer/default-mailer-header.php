<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/28/2017
 * Time: 17:53
 */

/** @var \Utils\Mailer $this */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>WAS.MEDIA</title>

    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        body {
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0;
        }

        img {
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a img {
            border: none;
        }

        p {
            margin: 0;
        }

        table td {
            border-collapse: collapse;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0;
            mso-table-rspace: 0;
        }

        @media only screen and (max-device-width: 480px) {
            a[href^="tel"], a[href^="sms"] {
                text-decoration: none;
                pointer-events: none;
                cursor: default;
            }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                text-decoration: default;
                pointer-events: auto;
                cursor: default;
            }
        }

        @media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
            a[href^="tel"], a[href^="sms"] {
                text-decoration: none;
                pointer-events: none;
                cursor: default;
            }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                text-decoration: default;
                pointer-events: auto;
                cursor: default;
            }
        }
    </style>
</head>
<body style="margin:0; padding:0; border: 0;">
<!-- Template -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0"
           style="font-family: 'Arial', sans-serif !important; font-size:18px !important; line-height:1.3 !important; color:#000000;">
        <tr>
            <td align="center" valign="top">
                <table width="600" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
                    <tr class="header">
                        <td>
                            <a href="https://was.media/<?= pll_current_language() ?>"
                               style="text-decoration: none; color:#111111" rel="nofollow noopener"
                               target="_blank">
                                <img src="<?= $this->getEmailTopLogo() ?>" width="600" height="114"
                                     alt=""
                                     style="margin:0; padding:0; border:none; vertical-align: middle; max-width: 100%"
                                     border="0"/>
                            </a>
                        </td>
                    </tr>
                    <!-- General Title & Lead -->

                     <?php if ($this->getEmailTitle() !== "") : ?>
                        <tr class="title">
                            <td style="line-height:1 !important; text-transform: uppercase; padding: 30px 30px 0 30px;">
                                <h1 style="line-height:1 !important;"><?= $this->getEmailTitle() ?></h1>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($this->getEmailDescription() !== "") : ?>
                        <tr class="lead">
                            <td style="line-height:1.3 !important; padding: 20px 30px 30px 75px">
                                <p style="line-height:1.3 !important;"><?= $this->getEmailDescription() ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>