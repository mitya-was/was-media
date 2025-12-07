<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/28/2017
 * Time: 17:53
 */

/** @var \Utils\Mailer $this */
?>

<tr class="article">
    <td style="line-height:1.3 !important; padding: 23px 30px 22px 30px">
        <a style="text-decoration: none; color:#111111" rel="nofollow noopener" target="_blank"
           href="<?= $this->generateUTMPermalink() ?>">
            <img src="<?= $this->getCurrentPostCustomImage() ?>" alt=""
                 style="margin:0; padding:0; border:none; vertical-align: top; display: block; height: auto; width: 100%;"
                 border="0"/>
            <h3 style="line-height:1 !important; text-transform: uppercase; padding: 25px 30px 0 45px;">
                <?= $this->getCurrentPostCustomTitle() ?>
            </h3>
            <p style="line-height:1.3 !important; padding: 15px 30px 0 45px;">
                <?= $this->getCurrentPostCustomDescription() ?>
            </p>
        </a>
    </td>
</tr>