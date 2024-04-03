# osTicket Fetch Note plugin Fork
osTicket plugin to fetch additional note content on ticket creation.

Original plugin by https://github.com/bkonetzny/osticket-fetch-note

Fork created to support updated OSTicket versions with multi plugin instances, after 1.17.2ish


## Installation
Place the content of this plugin in `include/plugins/osticket-fetch-note` and install via osTicket Admin Control Panel.

This plugin registers to OnTicketCreated and pulls a "note" from an external system.  Please use care in designing your external webhook.  "Note" Security is not handled in this plugin.

## Webhook
The webhook is pinged with the following JSON payload, and can return content for a note which is added to the ticket.

```
{
  "email": "<TICKET-AUTHOR-EMAIL>"
}
```


## Use Suggestions
Many options exist for this external note.  We use it to provide extra details from an LDAP lookup including OU and Description.

Once connected to external webhook the options are as endless as the platforms you can connect to.

- look up user devices in Aruba Clearpass
- Check student schedule in SIS software
- Reference asset library in inventory software




## Thanks
Code heavily borrowed from https://github.com/thammanna/osticket-slack ;)

