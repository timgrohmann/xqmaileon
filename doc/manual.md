# Anleitung für das Prestashop-Modul "Maileon-Integraton"

Mit diesem Modul können Sie Newsletter-Automatisierung mit Maileon in Prestashop integrieren.


## Voraussetzungen

| Anforderung | Version |
| ----------- | ------- |
| Prestashop  | 1.7.1+  |
| PHP         | 7.0+    |

## Installation

Zur Installation laden Sie die beiliegende ZIP auf der Modulseite im Prestashop-Backoffice hoch
oder installieren Sie das Modul über den Addon-Marketplace (sobald verfügbar).

Zudem sollten Sie das standardmäßig in Prestashop installierte Modul "Newsletter-Anmeldung" im
Bereich "Werbung & Marketing" deaktivieren. Sonst sehen Ihre Kunden zwei Anmelde-Formulare, eins
von Prestashop und eins von diesem Modul. Das Modul "Newsletter" aus dem Bereich "Verwaltung"
können Sie aktiviert lassen. Mithilfe von diesem Modul sehen Sie auch in der Kundenübersicht in
Prestashop, welche Kunden sich für Newsletter mit Maileon angemeldet haben.

## Einstellungen

### Erstmalige Einrichtung

Nach der Installation können Sie die Einstellungs-Seite des Moduls im Prestashop-Backoffice öffnen.
Dort sollten Sie zunächst Ihren Maileon-API-Schlüssel (API-Key) eintragen. Dies ist eine lange
Buchstaben- und Zahlenfolge, die durch Bindestriche getrennt ist. Klicken Sie auf "Speichern".

Nun sollte im nächsten Abschnitt "Api-Verbindung prüfen" eine Bestätigungsmeldung erscheinen.
Ist dies nicht der Fall, prüfen Sie bitte den eigegebenen API-Schlüssel.

### Berechtigungen

Weiterhin sollten Sie eine Ziel-Berechtigung konfigurieren, wenn Sie ein Double-Opt-In-Verfahren
wählen, muss zusätzlich ein DOI-Schlüssel konfiguriert werden. Diesen entnehmen Sie dem Maileon-Backend.
Sie können außerdem wählen, mit welcher Berechtigung neue Kontakte angelegt werden, die bei einer Bestell-
Bestätigung oder Warenkorbabbrecher-Mail erstellt werden. Dies ist der Fall, wenn ein Gast, der Newsletter
nicht abonniert hat, in Ihrem Shop bestellt.

Im Feld "Newsletter-Anmeldung bei Kunden mit Account" können Sie entscheiden, ob bereits registrierte Kunden,
die sich für den Newsletter anmelden, zusätzlich eine DOI-Mail erhalten sollen, oder ob die Berechtigung
automatisch gesetzt wird.

### Anmeldungs-Formular

Das Modul zeigt standardmäßig eine Anmeldeformular über dem Footer Ihrer Seite an. Wünschen Sie dies nicht,
können Sie dieses Formular mit der Funktion "Newsletter-Anmeldung über Seitenfooter" deaktivieren. Zudem
haben Sie die Möglichkeit, zusätzliche Texte, wie eine CTA über und/oder unter dem Anmeldeformular zu erstellen.

### Warenkorb-Abbrecher

Sie können konfigurieren, nach welchem Zeitraum eine Warenkorbabbrecher-Mail über Maileon verwendet werden soll.
Damit ein Warenkorb als "vergessen" gewertet wird, muss die konfigurierte Zeit verstrichen sein. Der Timer
beginnt mit jedem Update des Warenkorbs erneut (Hinzufügen eines neuen Produktes etc.)

Der tatsächliche Zeitpunkt der Versendung kann je nach Konfiguration des Cronjobs abweichen.

## Cronjob

Damit Warenkorb-Abbrecher-Mails versendet werden können, müssen Sie einen Cronjob auf Ihrem System einrichten.
Dies funktioniert ja nach System unterschiedlich.
Auf Ubuntu folgen Sie [dieser Anleitung](https://wiki.ubuntuusers.de/Cron/).

Im Abschnitt "Warenkorbabbrecher-Cronjob einrichten" sehen Sie, welche URL von Ihrem Cronjob aufgerufen werden soll.

Ein beispielhafter Cron-Eintrag sieht wie folgt aus:

```sh
# Prestashop Abandoned Cart Service
*/5 * * * * curl https://ihrshop.de/module/xqmaileon/cron?token=XXXXXXXXX > /dev/null
```

Dieses Beispiel führt dazu, dass alle 5 Minuten überprüft wird, ob neue Abbrecher-Mails versendet werden sollen.

## Webhooks

Webhooks sind ein Feature von Maileon, das Ihnen erlaubt, durch Maileon ausgelöste Änderungen in Ihren Shop
zurückzuspiegeln. Unter _Einstellungen > Konto > Webhooks_ können diese in Maileon konfiguriert werden.

Geben Sie im Feld _HTTP-Post-URL_ die URL ein, die sie im Abschnitt "Webhooks einrichten" im Prestashop-Backoffice
sehen. Verwenden Sie die gesamte URL inklusive der Parameter hinter dem Fragezeichen.

Zusätzlich müssen _JSON-Informationen integrieren_ und die Option _Externe ID anhängen_ ausgewählt sein.
