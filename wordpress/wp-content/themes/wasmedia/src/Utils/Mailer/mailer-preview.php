<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/28/2017
 * Time: 14:29
 */

/** @var \Utils\Mailer $this */

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Mailer</title>
</head>
<body style="background-color: #ad4c18; font-family: 'Arial', sans-serif;">
<div style="width: 80%; margin: 20px auto; text-align: center;">
    <div style="margin: 20px; color: white">Вид письма рассылки при открытии email'a</div>
    <div style="margin: 20px; font-weight: bold; color: white">Язык рассылки -
        <u><?= strtoupper(pll_current_language()) ?></u></div>
    <div style="margin: 40px auto; padding: 5px; border: 1px solid white; width: 600px;">
        <?= $this->getMailHeaderPart() ?>
        <?= $this->generateMailContent() ?>
        <?= $this->getMailFooterPart() ?>
    </div>
</div>

<?php if ($this->isReadyToGo()) : ?>
    <div style="width: 80%; margin: 40px auto; text-align: center;">
        <a id="was-confirm-delivery" href="#" data-post="<?= $this->getGlobalPost()->ID ?>"
           style="color: black; background-color: white; padding: 10px; text-decoration: none; border-radius: 10px; font-weight: bold;">
            START
        </a>
    </div>
    <script>
        $(document).ready(function () {

            $(document).on("click", "#was-confirm-delivery", function (event) {
                event.preventDefault();

                if (confirm("Вы уверены, что хотите запустить рассылку?")) {
                    let mailStartButton = $(this);

                    mailStartButton.replaceWith("<img id='mail_delivery_loader' src='https://was.media/mail/img/loading_email.gif' width='50px'/>");

                    $.ajax({
                        type: "POST",
                        url: "/wp-admin/admin-ajax.php?action=was_start_mailer",
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        data: JSON.stringify({
                            post_id: $(this).attr("data-post")
                        }),
                        success: function (response) {

                            if (response.success) {
                                $("#mail_delivery_loader").replaceWith("<div style='color: white; font-weight: bold; font-size: 26px;'>ГОТОВО</div>");
                            } else {
                                console.log(response);

                                $("#mail_delivery_loader").replaceWith(mailStartButton);
                            }

                        },
                        error: function (response) {
                            console.log(response);

                            $("#mail_delivery_loader").replaceWith(mailStartButton);
                        }
                    });
                }
            });
        });
    </script>
<?php endif; ?>

</body>
</html>