# civi-data-translate

This extension is an attempt to prototype a new storage method for translations user strings.

The background to this is a discussion about how we would ideally store message_templates on a per
language basis without using the existing multilingual (which only scales to half-a-dozen languages)

Discussion notes are here:
https://pad.riseup.net/p/yoqWgVlcBIEwKWh7cob0

This extension provides the data structure for the civicrm_strings approach and
makes it available for get requests by the apiv4 api.

The implementation is best explained via the test:
```

 /**
   * Test that our wrapper interprets locales.
   *
   * @throws \API_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public function testMessageTemplateWithWrapper() {
    $template = MessageTemplate::create()->setValues(['msg_html' => 'blah'])->execute()->first();
    Strings::create()->setValues(['entity_table' => 'civicrm_msg_template', 'entity_field' => 'msg_html','entity_id' => $template['id'], 'string' => 'not blah', 'language' => 'fr_FR'])->execute();
    $template = MessageTemplate::get()->addWhere('id', '=', $template['id'])->setSelect(['*'])->setLanguage('fr_FR')->execute()->first();
    $this->assertEquals('not blah', $template['msg_html']);
  }
```




Note that I did think ideally the wrapper would intervene on the create too. At the moment
it doesn't because there is a question as to which fields would then be language specific.

However, if we DID intervene on the create it might be fairly simple to inject this
into the message template screen - ie it would be necessary to be able to choose a language
on the screen & for language to be able to be set on the api call.


The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.25

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl civi-data-translate@https://github.com/FIXME/civi-data-translate/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/civi-data-translate.git
cv en civi_data_translate
```

## Usage

(* FIXME: Where would a new user navigate to get started? What changes would they see? *)

## Known Issues

(* FIXME *)
