<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/28/2017
 * Time: 17:53
 */

/** @var \Utils\Mailer $this */
?>

<tr class="social-media">
    <td align="center" style="padding: 25px 30px 0 30px">
        <a href="https://t.me/wasmedia"
           style="text-decoration: none; color:#111111; margin: 15px 30px; display: inline-block; vertical-align: top;"
           rel="nofollow noopener" target="_blank">
            <img width="48" style="margin:0; padding:0; border:none; vertical-align: bottom;"
                 border="0" src="https://was.media/mail/img/tg_mailer_48.png" alt="">
        </a>
        <a href="https://www.facebook.com/was.by.depositphotos/"
           style="text-decoration: none; color:#111111; margin: 15px 30px; display: inline-block; vertical-align: top;"
           rel="nofollow noopener" target="_blank">
            <img width="48" style="margin:0; padding:0; border:none; vertical-align: bottom;"
                 border="0" src="https://was.media/mail/img/fb_mailer_48.png" alt="">
        </a>
        <a href="https://twitter.com/was_dot_media"
           style="text-decoration: none; color:#111111; margin: 15px 30px; display: inline-block; vertical-align: top;"
           rel="nofollow noopener" target="_blank">
            <img width="48" style="margin:0; padding:0; border:none; vertical-align: bottom;"
                 border="0" src="https://was.media/mail/img/tw_mailer_48.png" alt="">
        </a>
    </td>
</tr>
<tr class="details">
    <td align="center" style="padding: 50px 30px 30px 30px">
        <p>
            <?= pll__("Это письмо было отправлено редакцией") ?>
            <a href="https://was.media/<?= pll_current_language() ?>"
               style="text-decoration: none; color:#e47200;" rel="nofollow noopener"
               target="_blank">WAS</a>.
        </p>
        <p style="margin-top: 10px;">
            <?= pll__("Чтобы отписаться от рассылки, нажмите") ?>
            <a href='[unsubscribe]'
               style="text-decoration: none; color:#e47200;" rel="nofollow noopener"
               target="_blank">
                <?= pll__("здесь") ?></a>.
        </p>
    </td>
</tr>
</table>
</td>
</tr>
</table>
<!-- Template end -->
</body>
</html>