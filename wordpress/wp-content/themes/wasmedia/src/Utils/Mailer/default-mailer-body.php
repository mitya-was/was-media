<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/28/2017
 * Time: 17:53
 */

/** @var \Utils\Mailer $this */
?>

<tr class="article primary-article">
    <td style="line-height:1.3 !important; padding: 30px 0">
        <a style="text-decoration: none; color:#111111" rel="nofollow noopener" target="_blank"
           href="<?= $this->generateUTMPermalink() ?>">
            <img src="<?= $this->getCurrentPostCustomImage() ?>" alt=""
                 style="margin:0; padding:0; border:none; vertical-align: top; display: block; width: 100%; height: auto;"
                 border="0"/>
            <h2 style="line-height:1 !important; padding: 40px 30px 0 30px; text-transform: uppercase; color: #e47200">
                <?= $this->getCurrentPostCustomTitle() ?>
            </h2>
            <p style="line-height:1.3 !important; padding: 25px 30px 0 75px;">
                <?= $this->getCurrentPostCustomDescription() ?>
            </p>
        </a>
    </td>
</tr>