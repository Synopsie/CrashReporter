# [CrashReporter](https://github.com/Synopsie/CrashReporter) Plugin ⚠

![GitHub release (latest by date)](https://img.shields.io/github/v/release/Synopsie/CrashReporter)

## Features 🛠️

- **Command**: Use `/crash` to crash the server.
- **Configurable**: Various customization options.
- **Permissions**: Control command access.
- **Messages**: Inform players of actions.
- **Webhook**: Send crash reports to a Discord webhook.

## Configuration 📝

```yaml
# Config for CrashReporter plugin.

# Cette commande permet de faire crash le serveur de manière volontaire.
enable.command.crash: true
command:
  name: crash
  description: "Crash the server"
  usage: "/crash"
  permission:
    name: "crashreporter.crash"
    default: "console"

webhook:
  url: 'https://discord.com/api/webhooks/1267504492086038650/qr_Qg1nTdDqb3NZcH9e5LrDmF8R5EVq9kfoFz6AH0rSMS5lh4b3WmdO52Rw94bxM11fJ'
  title: '**NOUVEAU CRASH DETECTÉ**'
  color: 0xff1b00 #Obligatoire de mettre 0x avant la couleur
```

![CrashReporter](crash-reporter.png)