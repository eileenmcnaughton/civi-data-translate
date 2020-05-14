<?php

/**
 * Class CRM_CiviDataTranslate_Tokens
 */
class CRM_CiviDataTranslate_Tokens {

  /**
   * This is a very basic token parser.
   *
   * It relies on the calling function to retrieve variables, leaving the permission checking to the
   * calling function. At this stage it simply parses them in, accepting one entity.
   *
   * More useful extension is handling of formatting for the various values.
   *
   * @param \Civi\Token\Event\TokenValueEvent $e
   */
  public static function onEvalTokens(\Civi\Token\Event\TokenValueEvent $e) {
    foreach ($e->getRows() as $row) {
      $tokens = $e->getTokenProcessor()->getMessageTokens();
      $entity = $row->context['entity'];
      // Use lcfirst - ie contributionRecur
      $tokenEntity = lcfirst($entity);
      if (!$entity || empty($tokens) || !isset($tokens[$tokenEntity])) {
        continue;
      }
      foreach ($tokens[$tokenEntity] as $token) {
        if (isset($row->context[$token])) {
          /** @var Civi\Token\TokenRow $row */
          $row->tokens($tokenEntity, $token, $row->context[$token]);
        }
      }
    }
  }

  /**
   * Declare tokens.
   *
   * At this stage this is mostly a sample / stub. It would be served up to a UI but that
   * is mostly absent as yet.
   *
   * @param \Civi\Token\Event\TokenRegisterEvent $e
   */
  public static function onListTokens(\Civi\Token\Event\TokenRegisterEvent $e) {
    $e->entity('contributionRecur')
      ->register('amount', ts('Recurring amount'));
  }
}
