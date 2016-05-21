<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Test de Cron</title>
</head>
<body>

    <?php
    // Destinataire
    $to = $GLOBALS['mailTest'];
    // Sujet
    $subject = 'Test de planification de tâche Cron';

    // Message
    $message = '
    <html>
      <head>
        <title>Test Cron</title>
      </head>
      <body>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td align="center">
              <p>
                Ceci est un test qui prouve que Cron fonctionne correctement !
              </p>
              <p>
                Chouette, hein ?
              </p>
            </td>
          </tr>
        </table>
      </body>
    </html>
    ';

    // Pour envoyer un mail HTML, l en-tête Content-type doit être défini
    $headers = "MIME-Version: 1.0" . "\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";

    // En-têtes additionnels
    $headers .= 'From: Mail de test <no-reply@monsitedetest.com>' . "\r\n";

    // Envoie
    $resultat = mail($to, $subject, $message, $headers);
    ?>

</body>
</html>