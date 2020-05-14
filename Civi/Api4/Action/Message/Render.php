<?php


namespace Civi\Api4\Action\Message;

use Civi;
use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Token\TokenProcessor;

/**
 * Class Render.
 *
 * Get the content of an email for the given template text, rendering tokens.
 *
 * @method $this setWorkflowName(string $messageTemplateID) Set Message Template Name.
 * @method string getWorkflowName() Get Message Template Name.
 * @method $this setMessageSubject(string $messageSubject) Set Message Subject
 * @method string getMessageSubject() Get Message Subject
 * @method $this setMessageHtml(string $messageHtml) Set Message Html
 * @method string getMessageHtml() Get Message Html
 * @method $this setMessageText(string $messageHtml) Set Message Text
 * @method string getMessageText() Get Message Text
 * @method array getMessages() Get array of adhoc strings to parse.
 * @method $this setMessages(array $stringToParse) Set array of adhoc strings to parse.
 * @method $this setEntity(string $entity) Set entity.
 * @method string getEntity() Get entity.
 * @method $this setEntityIDs(array $entityIDs) Set entity IDs
 * @method array getEntityIDs() Get entity IDs
 * @method $this setLanguage(string $entityIDs) Set language (e.g en_NZ).
 * @method string getLanguage() Get language (e.g en_NZ)
 */
class Render extends AbstractAction {

  /**
   * ID of message template.
   *
   * It is necessary to pass this or at least one string.
   *
   * @var string
   */
  protected $workflowName;

  /**
   * Ad hoc html strings to parse.
   *
   * Array of adhoc strings arrays to pass e.g
   *  [
   *    ['string' => 'Dear {contact.first_name}', 'format' => 'text/html', 'key' => 'greeting'],
   *    ['string' => 'Also known as {contact.first_name}', 'format' => 'text/plain', 'key' => 'nick_name'],
   * ]
   *
   * If no provided the key will default to 'string' and the format will default to 'text'
   *
   * @var array
   */
  protected $messages = [];

  /**
   * String to be returned as the subject.
   *
   * @var string
   */
  protected $messageSubject;

  /**
   * String to be returned as the subject.
   *
   * @var string
   */
  protected $messageText;

  /**
   * String to be returned as the subject.
   *
   * @var string
   */
  protected $messageHtml;

  /**
   * Entity for which tokens need to be resolved.
   *
   * This is required if tokens related to the entity are to be parsed and the entity cannot
   * be derived from the message_template.
   *
   * Only Activity is currently supported in this initial implementation.
   *
   * @var string
   *
   * @options Activity
   *
   */
  protected $entity;

  /**
   * An array of one of more ids for which the html should be rendered.
   *
   * These will be the keys of the returned results.
   *
   * @var array
   */
  protected $entityIDs = [];

  /**
   * Language to use.
   *
   * @var string
   */
  protected $language;
  /**
   * @inheritDoc
   */
  public function _run(Result $result) {
    $this->loadMessageTemplate();
    $tokenProcessor = new TokenProcessor(Civi::dispatcher(), [
      'controller' => __CLASS__,
      'smarty' => FALSE,
      'schema' => [$this->getEntity() => $this->getEntityKey()],
    ]);

    // Use wrapper as we don't know which entity.
    // Doing a get here allows us to do permission checking which is not obviously present in the token processor.
    // It also helps with the fact some entities don't have processors. Note that it's not ideal to do select * but...
    // otherwise we need to know the tokens.
    $entities = \civicrm_api4($this->getEntity(), 'get', ['where' => [['id', 'IN', $this->entityIDs]], 'select' => ['*'], 'checkPermissions' => $this->checkPermissions]);

    foreach ($entities as $entity) {
      foreach ($this->getStringsToParse() as $fieldKey => $textField) {
        if (empty($textField['string'])) {
          continue;
        }
        if (!empty($entity['contact_id'])) {
          $tokenProcessor->addRow()->context(array_merge(['contactId' => $entity['contact_id'], 'entity' => $this->getEntity()], $entity));
        }
        else {
          $tokenProcessor->addRow()->context();
        }

        $tokenProcessor->addMessage($fieldKey, $textField['string'], $textField['format']);
        $tokenProcessor->evaluate();
        foreach ($tokenProcessor->getRows() as $row) {
          /* @var \Civi\Token\TokenRow $row */
          $result[$entity['id']][$fieldKey] = $row->render($fieldKey);
        }
      }
    }
  }

  /**
   * Array holding
   *  - string String to parse, required
   *  - key Key to key by in results, defaults to 'string'
   *  - format - format passed to token providers.
   *
   * @param array $stringDetails
   *
   * @return \Civi\Api4\Action\Message\Render
   */
  public function addMessage(array $stringDetails): Render {
    $this->messages[] = $stringDetails;
    return $this;
  }

  /**
   * Get the strings to render civicrm tokens for.
   *
   * @return array
   */
  protected function getStringsToParse(): array {
    $textFields = [
      'msg_html' => ['string' => $this->getMessageHtml(), 'format' => 'text/html', 'key' => 'msg_html'],
      'msg_subject' => ['string' => $this->getMessageSubject(), 'format' => 'text/plain', 'key' => 'msg_subject'],
      'msg_text' => ['string' => $this->getMessageText(), 'format' => 'text/plain', 'key' => 'msg_text'],
    ];
    foreach ($this->getMessages() as $message) {
      $message['key']  = $message['key'] ?? 'string';
      $message['format'] = $message['format'] ?? 'text/plain';
      $textFields[$message['key']] = $message;
    }
    return $textFields;
  }

  /**
   * Get the key to use for the entity ID field.
   *
   * @return string
   */
  protected function getEntityKey(): string {
    return strtolower($this->getEntity()) . 'Id';
  }

  /**
   *
   */
  protected function loadMessageTemplate() {
    if ($this->getWorkflowName()) {
      $messageTemplate = \Civi\Api4\MessageTemplate::get()
        ->setLanguage($this->getLanguage())
        ->addWhere('workflow_name', '=', $this->getWorkflowName())
        ->addWhere('is_active', '=', TRUE)
        ->addWhere('is_default', '=', TRUE)
        ->setSelect(['*'])
        // I think we want to check permissions - but only render permissioned.
        ->setCheckPermissions(FALSE)
        ->execute()->first();
      $this->setMessageHtml($messageTemplate['msg_html']);
      $this->setMessageText($messageTemplate['msg_text']);
      $this->setMessageSubject($messageTemplate['msg_subject']);
    }
  }

}
