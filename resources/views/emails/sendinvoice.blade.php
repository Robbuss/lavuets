<!DOCTYPE html>
<!-- Set the language of your main document. This helps screenreaders use the proper language profile, pronunciation, and accent. -->
<html lang="en">
  <head>
    <!-- The title is useful for screenreaders reading a document. Use your sender name or subject line. -->
    <title>Welkom bij opslagmagazijn.nl</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Never disable zoom behavior! Fine to set the initial width and scale, but allow users to set their own zoom preferences. -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        /* RESET STYLES */
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        /* GMAIL BLUE LINKS */
        u + #body a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }
        /* SAMSUNG MAIL BLUE LINKS */
        #MessageViewBody a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }
        /* These rules set the link and hover states, making it clear that links are, in fact, links. */
        /* Embrace established conventions like underlines on links to keep emails accessible. */
        a { color: #B200FD; font-weight: 600; text-decoration: underline; }
        a:hover { color: #000000 !important; text-decoration: none !important; }
        /* These rules adjust styles for desktop devices, keeping the email responsive for users. */
        /* Some email clients don't properly apply media query-based styles, which is why we go mobile-first. */
        @media screen and (min-width:600px) {
            h1 { font-size: 48px !important; line-height: 48px !important; }
            .intro { font-size: 24px !important; line-height: 36px !important; }
        }
    </style>
  </head>
  <body style="margin: 0 !important; padding: 0 !important;">
    <!-- This ghost table is used to constrain the width in Outlook. The role attribute is set to presentation to prevent it from being read by screenreaders. -->
    <!--[if (gte mso 9)|(IE)]>
    <table cellspacing="0" cellpadding="0" border="0" width="720" align="center" role="presentation"><tr><td>
    <![endif]-->
    <!-- The role and aria-label attributes are added to wrap the email content as an article for screen readers. Some of them will read out the aria-label as the title of the document, so use something like "An email from Your Brand Name" to make it recognizable. -->
    <!-- Default styling of text is applied to the wrapper div. Be sure to use text that is large enough and has a high contrast with the background color for people with visual impairments. -->
    <div role="article" aria-label="Bedankt voor het aanmelden bij opslagmagazijn.nl!" lang="en" style="background-color: white; color: #2b2b2b; font-family: 'Avenir Next', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-size: 18px; font-weight: 400; line-height: 28px; margin: 0 auto; max-width: 720px; padding: 40px 20px 40px 20px;">
        
        <!-- Logo section and header. Headers are useful landmark elements. -->
        <header>
            <!-- Since this is a purely decorative image, we can leave the alternative text blank. -->
            <!-- Linking images also helps with Gmail displaying download links next to them. -->
            <a href="https://opslagmagazijn.nl">
                <center><img src="https://www.opslagmagazijn.nl/wp-content/uploads/2018/02/self-storage-breukelen-1-1.png" alt="" height="50" width="50"></center>
            </a>
            <!-- The h1 is the main heading of the document and should come first. -->
            <!-- We can override the default styles inline. -->
            <h1 style="color: #000000; font-size: 32px; font-weight: 800; line-height: 32px; margin: 48px 0; text-align: center;">
                Welkom bij opslagmagazijn!
            </h1>
        </header>

        <!-- Main content section. Main is a useful landmark element. -->
        <main>
            <!-- This div is purely presentational, providing a container for the message. -->
            <div style="background-color: ghostwhite; border-radius: 4px; padding: 24px 48px;">
                <!-- This ghost table is used solely for padding in Word-based Outlook clients. -->
                <!--[if (gte mso 9)|(IE)]>
                <table cellspacing="0" cellpadding="0" border="0" width="720" align="center" role="presentation"><tr><td style="background-color: ghostwhite; padding: 24px 48px 24px 48px;">
                <![endif]-->

                <!-- Body copy -->
                <p>
                    Beste klant,
                </p>

                <p>
                    In de bijlage vind je jouw nieuwe factuur.
                </p>
                @if(isset($payment->_links->checkout))
                <p>
                    <a href="{{ $payment->_links->checkout->href }}">Naar betalen</a>
                </p>
                @else
                <p>
                    Wanneer je automatische incasso hebt ingesteld, wordt het te bedrag automatisch van je rekening afgeschreven.                    
                </p>
                @endif
                <!--[if (gte mso 9)|(IE)]>
                </td></tr></table>
                <![endif]-->
            </div>
        </main>

        <!-- Footer information. Footer is a useful landmark element. -->
        <footer>
            <!-- Since this is a transactional email, you aren't required to include opt-out language. -->
            <p style="font-size: 16px; font-weight: 400; line-height: 24px; margin-top: 48px;">
                Tot snel in Breukelen!
            </p>
    
            <!-- The address element does exactly what it says. By default, it is block-level and italicizes text. You can override this behavior inline. -->
            <address style="font-size: 16px; font-style: normal; font-weight: 400; line-height: 24px;">
            Met vriendelijke groet,<br><br>
                <strong>Nigel van Leusden</strong> 
            </address>
        </footer>

    </div>
    <!--[if (gte mso 9)|(IE)]>
    </td></tr></table>
    <![endif]-->
  </body>
</html>